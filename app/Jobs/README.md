## Jobs statuses feature

Shows statuses of all jobs and also, if required, statuses of all job subtasks.

Files belonging to this feature:
- app/Jobs/Traits/TrackableJobTrait.php
- app/Jobs/BaseTrackableJob.php
- app/Jobs/ExampleTrackableJob.php
- app/Jobs/ExampleTrackableJobWithSubtasks.php
- app/Models/JobStatus.php
- app/Models/JobSubtaskFailure.php
- app/database/migrations/2020_07_09_125307_create_job_subtasks_failures_table.php
- app/Http/Controllers/JobsController.php
- app/resources/views/jobs/*

Backbone package 'imtigger/laravel-job-status' (https://github.com/imTigger/laravel-job-status)

If installing from scratch:  
After package installation perform actions as specified in https://github.com/imTigger/laravel-job-status/blob/master/INSTALL.md  
Don't forget to publish and to change 'model' key value in config/job-status.php to App\Models\JobStatus::class   

You must add the following line in the published migration file '2017_05_01_000000_create_job_statuses_table.php':  
```
$table->unsignedBigInteger('user_id')
```

If Laravel job tables does not exist yet in your database you must run:  
```
php artisan queue:table
```   
and   
```
php artisan queue:failed-table
```


custom config keys (config file 'job-status'):   
- 'import_job_status_ajax_call_timeout'                       - check job status on 'status' page on every X seconds  
- 'job-status.import_job_calc_time_remaining_range_quantum'   - quantum on which to calculate remaining time  