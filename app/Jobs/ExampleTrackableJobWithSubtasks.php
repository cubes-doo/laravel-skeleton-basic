<?php

/**
 * Class
 *
 * PHP version 7.2
 */
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Queue;
use App\Jobs\Traits\TrackableJobTrait;

/**
 * Example Trackable Job which has subtasks.
 *
 * Used for jobs which statuses and progress should be tracked by a user
 * and which have multiple subtasks (data rows).
 */
class ExampleTrackableJobWithSubtasks extends BaseTrackableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable,
        SerializesModels;

    use TrackableJobTrait;
    
    public function handle()
    {

        // data for this job to process
        $data = factory(\App\Models\Example::class, $max = mt_rand(5, 20))->create()->toArray(); 
        logger(var_export($data, TRUE));

        $rowsCount = count($data);
        $i = 0;
        $errorCount = 0;

        // initialize tracking
        $this->trackingInitialize($rowsCount);

        // main job action
        foreach($data as $row) {
            try {
                // subtask action
                logger("data row processed");

                if($i % 2 == 0) {
                    throw new \Exception("Example of exception thrown in a subtask");
                }

            }
            catch(\Exception $ex) {
                // if exception occurs, record failure 
                $this->trackingSmartRecordFailure($ex, [json_encode($row)], $i); // if row is complex data you should perform json_encode() on it.
                ++$errorCount;
            }

            // record progress
            $this->trackingSmartRecordProgress($rowsCount, $i);

            // sleep() is here only for demo purposes - allows job status progress to be tracked live
            sleep(2);
        }

        // cleanup after tracking and setting of 'final' message
        $finalJobStatusMsg = $errorCount > 0 ? __('Job finished but with errors') : "OK";
        $this->trackingCleanup($finalJobStatusMsg); // message must be "OK" if job is finished as planned. 

        return TRUE;
    }

    /**
     * Initiate job status trackable data
     */
    private function trackableJobInitData()
    {
        Queue::setConnectionName('database');

        return [
            'user_id' => $this->user->id,
            'input' => [
                'job_type' => \App\Models\JobStatus::JOB_TYPE_MISCELLANEOUS,
                'job_task_name' => 'Example Trackable Job With Subtasks',
                'queue_stack_wait' => Queue::size() 
            ]
        ];
    }

}
