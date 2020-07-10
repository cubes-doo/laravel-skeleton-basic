<?php

namespace App\Jobs\Traits;

use Imtigger\LaravelJobStatus\Trackable;
use App\Models\JobSubtaskFailure;
use App\Models\JobStatus;
use App\Models\User;
use Illuminate\Validation\ValidationException;

/**
 * This trait enables tracking of state and progress of Laravel job and
 * recording job errors into a database.
 * 
 * proxy* methods were created because those methods in trait Trackable 
 * have protected access and we want to enable calls to those methods 
 * also from outside of job class.
 * 
 */
Trait TrackableJobTrait
{
    use Trackable;

    /*
     * This constructor is necessary when Imtigger\LaravelJobStatus\Trackable
     * is used because of $this->prepareStatus() call.
     */
    public function __construct(array $params=[])
    {
        if (get_parent_class($this)) {
            // we need to pass parameters to parent if he exists
            parent::__construct($params); 
        }

        $dataForPrepareStatus = $this->getDataForPrepareStatus();

        // we are passing parameters which will be set when new job_status row is created
        $this->prepareStatus($dataForPrepareStatus);
    }

    public function proxySetProgressMax(int $value) 
    {
        $this->setProgressMax($value);
    }

    public function proxySetProgressNow(int $value, $every=1) 
    {
        $this->setProgressNow($value, $every);
    }

    public function proxySetInput(array $value) 
    {
        $this->setInput($value);
    }

    public function proxySetOutput(array $value) 
    {
        $this->setOutput($value);
    }

    /**
     * Return array which will be passed to prepareStatus() 
     * If trackableDataForPrepareStatus() is defined in a calling class
     * then take value from that method.
     * 
     * @return array
     */
    private function getDataForPrepareStatus()
    {
        $data = [
            'user_id' => 0
        ];

        if(method_exists($this, 'trackableJobInitData')) {
            $data = $this->trackableJobInitData();
        }

        return $data;
    }

    /**
     * Record error in 'job_failures' table.
     * 
     * @param array $rowData
     * @param int $rowNumber
     * @param \Exception $exception
     * @param string $exceptionMessage
     * @param array $allExceptionMessages
     * 
     * @return void 
     */
    public function recordSubtaskFailure(
        $rowData, 
        $rowNumber, 
        \Exception $exception=NULL, 
        $exceptionMessage=NULL, 
        $allExceptionMessages=[]
    ) {
        if( is_null($exceptionMessage) ) {
            $exceptionMessage = $exception->getMessage();
        }
        if($exception instanceof ValidationException && count($allExceptionMessages) == 0) {
            $allExceptionMessages = $exception->errors();
        }

        JobSubtaskFailure::prepareAndCreate($this, $rowNumber, $rowData, 
                                                   $exceptionMessage, $allExceptionMessages);
    }

    /**
     * Automatically make right recordSubtaskFailure() call depending on exception.
     * 
     * @param array $rowData
     * @param int $rowNumber
     * @param \Exception $exception
     * 
     * @return void
     */
    public function smartRecordSubtaskFailure($rowData, $rowNumber, $exception)
    {
        if($exception instanceof ValidationException) {
            $this->recordSubtaskFailure($rowData, $rowNumber, $exception, 
                                       __('Validation error'), $exception->errors());
        }
        else {
            $this->recordSubtaskFailure($rowData, $rowNumber, $exception);
        }
    }

    /**
     * Calculate remaining time for job execution.
     * 
     * @param integer $totalRowsCount | total number of rows
     * @param integer $rowsTimedCount | number of rows whose execution is passed
     * @param integer $iter           | current iteration number
     * @param integer $timed          | execution time in seconds
     * @param float   $avgRowTime     | Array with elements that represents time
     *                                  required for executing one row (subtask)
     * @return integer
     */
    public function calculateRemainingExecutionTime(
        $totalRowsCount, 
        $rowsTimedCount, 
        $iter,
        $timed,
        &$rowsAvgTimeBag
    ) {

        // time to execute one row
        $oneRowTimedAvg = ($timed / $rowsTimedCount);

        // add new value to 'rowsAvgTimeBag'
        array_push($rowsAvgTimeBag, $oneRowTimedAvg);
        $avgRowTime = collect($rowsAvgTimeBag)->avg();
        
        $rowsRemaining = $totalRowsCount - $iter;
        $timeRemaining = round($avgRowTime * $rowsRemaining);

        return $timeRemaining;
    }

    /**
     * Calculate remaining time for execution of a job.
     * This method measures time for job execution and it's measure quantum
     * value is taken from "config".
     * 
     * @param integer $rowsCount   | total number of rows
     * @param integer $iter        | current iteration number
     * @param integer $initStarted | initial value when process is started
     *                               in seconds (by call to time())
     * 
     * @return void
     */
    public function smartCalcRemainingExecTime($rowsCount, $iter, $initStarted)
    {
        if($iter + 1 >= $rowsCount) { // +1 because iteration beginns with 0
            return;
        }

        static $started;
        static $timed = 0;
        static $rowsAvgTimeBag;

        // initialize static variables
        if(is_null($started)) {
            $started = $initStarted;
        }
        if(is_null($rowsAvgTimeBag)) {
            $rowsAvgTimeBag = [];
        }

        $timeRemainingCalcQuantum =  config('job-status.import_job_calc_time_remaining_range_quantum', 100);

        if($iter % $timeRemainingCalcQuantum == 0) {
            $now = time();
            $timed = $now - $started;
            $started = $now;

            $res = $this->calculateRemainingExecutionTime($rowsCount, $timeRemainingCalcQuantum, 
                                                          $iter, $timed, $rowsAvgTimeBag);

            // Record remaining time in 'job_status' 'output' 
            $this->setOutput(['time_remaining' => $res]);

            return;
        }
    }

    /**
     * Set value of column 'user_id' in table 'job_statuses'.
     * Internal construction of this method(call to $this->update) is modeled
     * per setter with the same name in trait Imtigger\LaravelJobStatus\Trackable.
     * 
     * @param integer $value | ID of user who initialized a job
     * 
     * @return void
     */
    protected function setUserId($userId)
    {
        $this->update(['user_id' => $userId]);
        $this->user_id = $userId;
    }

    /**
     * Initialize tracking of a job 
     * 
     * @param integer $rowsCount        | total number of rows
     * @param array   $importFieldsMap  | when importing from "Excel" this represents
     *                                    mapping of column number(letter) to column name
     * @throws \Exception
     * 
     * @return void
     */
    public function trackingInitialize($rowsCount=1, $importFieldsMap=[])
    {
        $this->setProgressMax($rowsCount);
        $initData = $this->getDataForPrepareStatus();
        $this->setInput(array_merge($initData['input'] ?? [], ['import_fields_map' => $importFieldsMap]));
    }

    /**
     * Record an error in 'job_subtasks_failures' table.
     * It accepts all Exception classes and depending on the type of 
     * exception it sets error data in the table.
     * 
     * @param \Exception $exception
     * @param array $rowData
     * @param int $rowNumber
     * 
     * @return void
     */
    public function trackingSmartRecordFailure($exception, $row=[], $rowNumber=0)
    {
        $this->smartRecordSubtaskFailure($row, $rowNumber, $exception);
    }

    /**
     * Calculate and set remaining time for job execution.
     * Set current progress iteration.
     * 
     * @param integer $rowsCount     | total number of rows
     * @param integer $i             | number of current iteration
     * 
     * @return void
     */
    public function trackingSmartRecordProgress($rowsCount, &$i)
    {
        static $initStarted;

        if(is_null($initStarted)) {
            $initStarted = time();
        }

        $this->smartCalcRemainingExecTime($rowsCount, $i, $initStarted);

        //sleep(2); // DEBUG !!!!!!!!!! IMPORTANT! COMMENT THIS OUT !!!!!!
        $this->proxySetProgressNow(++$i);
    }

    /**
     * Mark tracking progress as finished. 
     * 
     * @return void
     */
    public function trackingMarkProgressFinished()
    {
        $this->proxySetProgressNow($this->progressMax);
    }

    /**
     * Call when job is finished.
     * Set final job message.
     * 
     * @param string $finalJobStatusMsg
     * 
     * @return void
     */
    public function trackingCleanup($finalJobStatusMsg) 
    {
        $this->setOutput(['message' => $finalJobStatusMsg]);
    }
}