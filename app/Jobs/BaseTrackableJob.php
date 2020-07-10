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
use App\Lib\ConfigurableProperties;

/**
 * Base class for Trackable Job
 * 
 * Uses ConfigurableProperties and sets userId in the __constructor()
 * Initiates trackableJobInitData() -> you should override this method
 * with your own data.
 *
 */
abstract class BaseTrackableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable,
        SerializesModels;

    use ConfigurableProperties;

    protected $user;
    
    public function handle()
    {
        return FALSE;
    }

    /**
     * Initialize 'trackable job status' data
     */
    private function trackableJobInitData()
    {
        Queue::setConnectionName('database');

        return [
            'user_id' => $this->user->id,
            'input' => [
                'job_type' => \App\Models\JobStatus::JOB_TYPE_MISCELLANEOUS,
                'job_task_name' => 'BaseTrackableJob job init data',
                'queue_stack_wait' => Queue::size() 
            ]
        ];
    }

    public function setUser(\App\Models\User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }  

}
