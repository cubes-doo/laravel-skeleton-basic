<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model which encompasses job subtasks(in most of cases subtasks are rows)
 */
class JobSubtaskFailure extends Model
{
    protected $table = 'job_subtasks_failures';

    protected $fillable = [
        'subtask_id', 
        'job_status_id', 
        'main_error_message', 
        'all_errors_messages',
        'subtask_data'
    ];


    public function jobStatus()
    {
        return $this->belongsTo(JobStatus::class, 'job_status_id');
    }


    /**
     * Prepare data (call 'json encode') and record error row into a database 
     * table row.
     * 
     * @param Job $job
     * @param int $subtaskId
     * @param array $subtaskData
     * @param string $mainErrorMessage
     * @param array $errorsMessages
     * 
     * @return void
     */
    public static function prepareAndCreate(
        $job, 
        $subtaskId, 
        $subtaskData, 
        $mainErrorMessage, 
        $errorsMessages=[]
    ) {
        JobSubtaskFailure::create([
            'job_status_id' => $job->getJobStatusId(),
            'subtask_id' => $subtaskId, 
            'main_error_message' => $mainErrorMessage,
            'all_errors_messages' => json_encode($errorsMessages),
            'subtask_data' => json_encode($subtaskData)
        ]);
    }

}
