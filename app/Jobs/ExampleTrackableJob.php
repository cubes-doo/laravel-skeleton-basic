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
 * Example Trackable Job
 *
 * Used for jobs which statuses and progress should be tracked by a user.
 */
class ExampleTrackableJob extends BaseTrackableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable,
        SerializesModels;

    use TrackableJobTrait;
    
    public function handle()
    {

        // initialize tracking
        $this->trackingInitialize();

        try {
            // main job action (Here only logs "JOB RUN")
            logger("JOB RUN");

            // mark job progress as finished
            $this->trackingMarkProgressFinished();
        }
        catch(\Exception $ex) {
            // if exception occurs, record failure
            $this->trackingSmartRecordFailure($ex);
            $this->trackingCleanup('Job execution failed. Exception thrown.');
            return FALSE;
        }

        // cleanup after tracking and setting of 'final' message
        $this->trackingCleanup('OK'); // message must be "OK" if job is finished as planned. 

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
                'job_task_name' => 'Example Trackable Job With Single Action (no subtasks)',
                'queue_stack_wait' => Queue::size() 
            ]
        ];
    }

}
