<?php

namespace App\Models;

use Imtigger\LaravelJobStatus\JobStatus as ImtiggerJobStatus;
use Carbon\CarbonInterval;

/**
 * 'Custom' class which extends model from the package Imtigeger\LaravelJobStatus.
 * It must be announced in config/job-status for package to recognize it and use
 * it as it's job status class.
 */
class JobStatus extends ImtiggerJobStatus
{
    const STATUS_FINISHED_WITH_ERRORS = 'finished_with_errors';

    /*
     * All original statuses (defined inside
     * Imtigger\LaravelJobStatus\JobStatus)
     */
    const ALL_STATUSES = [
        self::STATUS_QUEUED,
        self::STATUS_EXECUTING,
        self::STATUS_FINISHED,
        self::STATUS_FAILED,
        self::STATUS_FINISHED_WITH_ERRORS,
        self::STATUS_RETRYING
    ];

    /* Translations */
    const STATUS_QUEUED_TRANS = self::STATUS_QUEUED;// 'na čekanju';
    const STATUS_EXECUTING_TRANS = self::STATUS_EXECUTING; //'u toku';
    const STATUS_FINISHED_TRANS = self::STATUS_FINISHED; //'završen';
    const STATUS_FAILED_TRANS = self::STATUS_FAILED; //'potpuno neuspešan';
    const STATUS_FINISHED_WITH_ERRORS_TRANS = 'finished with errors'; //'završen ali sa greškama';
    const STATUS_RETRYING_TRANS = 'retryable'; //self::STATUS_RETRYING; // 'moguće ponovo pokretanje'; 

    const ALL_STATUSES_TRANS = [
        self::STATUS_QUEUED_TRANS,
        self::STATUS_EXECUTING_TRANS,
        self::STATUS_FINISHED_TRANS,
        self::STATUS_FAILED_TRANS,
        self::STATUS_FINISHED_WITH_ERRORS_TRANS,
        self::STATUS_RETRYING
    ];

    const JOB_TYPE_MISCELLANEOUS = 'Miscellaneous';
    const JOB_TYPE_EXCEL_IMPORT = 'Excel Import';

    /*
     * All job types
     */
    const ALL_JOB_TYPES = [
        self::JOB_TYPE_MISCELLANEOUS,
        self::JOB_TYPE_EXCEL_IMPORT
    ];

    protected $table = 'job_statuses';

    public function subtasksFailures()
    {
        return $this->hasMany(JobSubtaskFailure::class, 'job_status_id');
    }

    public function kreirao()
    {
        return $this->hasOne(User::class);
    }

    /**
     * Return job status string of this model instance.
     */
    public function getJobStatus()
    {
        if($this->status == JobStatus::STATUS_FINISHED) {
            if($this->subtasksFailures()->count()) {
                return JobStatus::STATUS_FINISHED_WITH_ERRORS;
            }
        }
        return $this->status;
    }

    /**
     * Package does not have late static binding for STATUS* constants 
     * therefore this translation maping was created.
     * 
     * @return string
     */
    public function getJobStatusTrans()
    {
        switch($this->getJobStatus()) {
            case JobStatus::STATUS_QUEUED: 
                $status = JobStatus::STATUS_QUEUED_TRANS;
                break;
            case JobStatus::STATUS_EXECUTING: 
                $status = JobStatus::STATUS_EXECUTING_TRANS;
                break;
            case JobStatus::STATUS_FINISHED: 
                $status = JobStatus::STATUS_FINISHED_TRANS;
                break;
            case JobStatus::STATUS_FAILED: 
                $status = JobStatus::STATUS_FAILED_TRANS;
                break;
            case JobStatus::STATUS_FINISHED_WITH_ERRORS: 
                $status = JobStatus::STATUS_FINISHED_WITH_ERRORS_TRANS;
                break;
            case JobStatus::STATUS_RETRYING;
                $status = JobStatus::STATUS_RETRYING_TRANS;
                break;
            default:
                $status = __('unknown!');
        }

        return $status;
    }

    /**
     * Note: $this->output is casted into array.
     */
    public function getFormattedRemainingExecTime()
    {
        if($this->status != JobStatus::STATUS_EXECUTING) {
            if($this->status == JobStatus::STATUS_QUEUED) {
                return __("waiting");
            }
            return __("finished");
        }

        $remainingExecTime = 0;

        if($this->output) {
            if(is_array($this->output) && isset($this->output['time_remaining'])) {
                $remainingExecTime = $this->output['time_remaining'];
            }
        }

        return CarbonInterval::seconds($remainingExecTime)->cascade()->forHumans();
    }

    /**
     * Note: $this->input is casted into array.
     * Returns initial count of jobs on the queue when job is created.
     */
    public function getQueueStackWaitNumber()
    {
        $queueStackWaitNumber = 0;

        if($this->input) {
            if(is_array($this->input) && isset($this->input['queue_stack_wait'])) {
                $queueStackWaitNumber = $this->input['queue_stack_wait'];
            }
        }

        return $queueStackWaitNumber;
    }

    /**
     * Note: $this->input is casted into array.
     */
    public function getJobType()
    {
        $jobType = __('unknown');

        if($this->input) {
            if(is_array($this->input) && isset($this->input['job_type'])) {
                $jobType = $this->input['job_type'];
            }
        }

        return $jobType;
    }

    /**
     * Note: $this->input is casted into array.
     */
    public function getJobTaskName()
    {
        $jobTaskName = __('unknown');

        if($this->input) {
            if(is_array($this->input) && isset($this->input['job_task_name'])) {
                $jobTaskName = $this->input['job_task_name'];
            }
        }

        return $jobTaskName;
    }

    /**
     * Return job task name to show to the app users.
     * 
     * @return string
     */
    public function getPresentationFullJobTaskName() {
        return $this->getJobTaskName() . ' / ' . $this->getJobType();
    }

    /**
     * Note: $this->input is casted into array.
     */
    public function getImportFieldsMap()
    {
        $map = [];

        if($this->input) {
            if(is_array($this->input) && isset($this->input['import_fields_map'])) {
                $map = $this->input['import_fields_map'];
            }
        }

        return $map;
    }

    /**
     * Return prepared 'import_fields' data.
     * 
     * @return array
     */
    public function getImportFieldsMapPrepared()
    {
        $map = $this->getImportFieldsMap();
        $preparedMap = array_flip($map);
        ksort($preparedMap); // !IMPORTANT so as to avoid resorting of this
                             // map array when map is passed through ajax

        return $preparedMap;
    }

    /**
     * Note: $this->output is casted into array.
     */
    public function getOutputMessage()
    {
        $jobMessage = '';
        
        if($this->output) {
            if(is_array($this->output) && isset($this->output['message'])) {
                $jobMessage = $this->output['message'];
            }
        }

        return $jobMessage;
    }

    /**
     * Return status data from chosen jobs.
     * 
     * @param integer $last  | how many of the last jobs to take
     * 
     * @return array
     */
    public static function getStatusCounts($last=5)
    {
        $lastStatuses = self::select('id', 'status')
                            //->where('user_id', auth()->user()->id)
                            ->orderByDesc('created_at')
                            ->limit($last)
                            ->get();
        
        $statusesCounted = [];
        foreach(self::ALL_STATUSES as $status) {
            $statusesCounted[$status] = 0;
        }

        $statusesCounted['total_count'] = count($lastStatuses);
        $statusesCounted['last_statuses'] = [];

        foreach($lastStatuses as $statusObj) {
            $status = $statusObj->status;

            if($status == self::STATUS_FINISHED) {
                if($statusObj->subtasksFailures()->count()) {
                    $statusesCounted[self::STATUS_FINISHED_WITH_ERRORS] += 1;
                    $status = self::STATUS_FINISHED_WITH_ERRORS;
                }
                else {
                    $statusesCounted[self::STATUS_FINISHED] += 1;
                }
            }
            else {
                $statusesCounted[$status] += 1;
            }

            $statusesCounted['last_statuses'][] = $status;
        }

        return $statusesCounted;
    }

    /**
     * Return a number how many jobs are waiting in the queue before this 
     * instance.
     * 
     * @return integer
     */
    public function getQueueStackCurrentWaitNumber()
    {
        $res = 0;

        if($this->job_id && is_numeric($this->job_id)) {
            $res = \DB::table('jobs')
                      ->where('id', '<', $this->job_id)
                      ->count();
        }
        else {
            $res = $this->getQueueStackWaitNumber();
        }

        return $res;
    }

}
