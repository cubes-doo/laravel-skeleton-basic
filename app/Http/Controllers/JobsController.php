<?php

namespace App\Http\Controllers;

use App\Jobs\ExampleTrackableJob;
use App\Jobs\ExampleTrackableJobWithSubtasks;
use App\Lib\DatetimeTrait;
use Illuminate\Http\Request;
use App\Models\JobStatus;
use App\Models\JobSubtaskFailure;
use App\Http\Resources\Json as JsonResource;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * Backbone of jobs feature is https://github.com/imTigger/laravel-job-status
 * In order to test jobs you must run alias command `quelis` or vanilla command
 * `php artisan queue:listen database --tries=1`
 * For extensive documentation look in app/Jobs/README.md
 */
 class JobsController extends Controller
{
    use DatetimeTrait;

    /**
     * @var array Holds a list of all job executors data
     */
    private $jobExecutors = [
        'single_action_job'  => [
            'title' => 'Single Action Job',
            'description' => 'Run single action job. Single action means that there are no ' .
                             'subtasks(rows) performed by this job. You could use this when ' .
                             'calling Schedule:run() for example or some other external command.',
            'executor' => 'runSingleActionJob'
        ],
        'subtasks_job'  => [
            'title' => 'Subtasks job',
            'description' => 'Run job which contains subtasks(rows). If error occurs in any of ' .
                             'the subtasks that error will be recorded and shown in "job status" page',
            'executor' => 'runSubtasksJob'
        ]
    ];
    
    /**
     * Return list of all jobs
     */
    public function all()
    {
        $allStatusesInfo = [];

        $statusCounts = JobStatus::getStatusCounts(1000000000000);

        $allStatusesInfo['broj_zavrsenih'] = $statusCounts[JobStatus::STATUS_FINISHED];
        $allStatusesInfo['broj_potpuno_neuspesnih'] = $statusCounts[JobStatus::STATUS_FAILED];
        $allStatusesInfo['broj_na_cekanju'] = $statusCounts[JobStatus::STATUS_QUEUED];
        $allStatusesInfo['broj_na_izvrsavanju'] = $statusCounts[JobStatus::STATUS_EXECUTING];
        $allStatusesInfo['broj_zavrsenih_ali_sa_greskama'] = $statusCounts[JobStatus::STATUS_FINISHED_WITH_ERRORS];
        $allStatusesInfo['number_of_retrying'] = $statusCounts[JobStatus::STATUS_RETRYING];

        return view('jobs.all', 
                    ['jobExecutors' => $this->jobExecutors,
                     'allStatusesInfo' => $allStatusesInfo]);
    }

    /**
     * 'Search' filter method for 'yajrabox datatable'
     */
    private function datatableFilter($query, $request)
    {
        // QUERY FROM TOP SEARCH INPUTS
        if ($request->has('status') && !empty($status = $request->status)) {
            $query
                ->where(function($q) use($status) {
                    $q->where('status', $status);
                });
        }
    }

    /*
     * Yajrabox datatables ajax method for listing of all 'job' statuses.
     */
    public function datatable(Request $request)
    {
        $query = JobStatus::query()
                          ->orderByDesc('created_at');

        return datatables($query)
            ->addColumn('actions', function ($entity) {
                return view('jobs.partials.table.actions', 
                            ['entity' => $entity]);
            })
            ->addColumn('job_type', function($entity) {
                return $entity->getJobType();
            })
            ->addColumn('job_task_name', function($entity) {
                return $entity->getJobTaskName();
            })
            ->editColumn('status', function($entity) {
                return view('jobs.partials.table.status', ['entity' => $entity]);
            })
            ->addColumn('kreirao', function($entity) {
                return optional($entity->kreator)->getFullName();
            })
            ->editColumn('created_at', function($entity) {
                return view('jobs.partials.table.date_and_time', ['datum' => $entity->created_at]);
            })
            ->editColumn('finished_at', function($entity) {
                return view('jobs.partials.table.date_and_time', ['datum' => $entity->finished_at]);
            })
            ->editColumn('started_at', function($entity) {
                return view('jobs.partials.table.date_and_time', ['datum' => $entity->started_at]);
            })
            ->filter(function ($query) use ($request) {
                $this->datatableFilter($query, $request);
            })
            ->rawColumns(['actions', 'status', 'created_at', 'finished_at', 'started_at'])
            ->setRowClass(function ($entity) {
                switch($entity->status) {
                    case JobStatus::STATUS_FINISHED:
                        return 'job-finished';
                    case JobStatus::STATUS_FAILED:
                        return 'job-failed';
                    case JobStatus::STATUS_EXECUTING:
                        return 'job-executing';
                    case JobStatus::STATUS_QUEUED:
                        return 'job-queued';
                }
            })
            ->setRowAttr([
                'data-id' => function($entity) {
                    return $entity->id;
                }
            ])
            ->make(true);
    }

    /**
     * Return 'view' which shows condition/progress of a job.
     */
    public function showJobProgress(Request $request, JobStatus $jobStatus)
    {
        /*
        Uncomment if only a job creator can see his job progress.
        if($jobStatus->user_id != auth()->user()->id) {
            return abort(401);
        }
        */

        return view('jobs.status', 
                    ['jobStatus' => $jobStatus]);
    }

    /**
     * Return job data over ajax.
     * Fetching only of wanted data is enabled through 'fetch' parameter.
     * 
     * @return JSON
     */
    public function ajaxGetJobStatusInfo(Request $request)
    {
        $data = $request->validate([
            "job_id" => ["required", "integer", "exists:job_statuses,id"],
            "job_subtasks_failures_last_received_id" => ["nullable", "integer", "min:0"],
            "fetch" => ["nullable", "array"],
            "fetch.*" => ["in:job_status,job_subtasks_failures,input_params"]
        ]);

        $fetchParams = $data['fetch'] ?? []; //['job_status', 'job_subtasks_failures', 'input_params'];
        $response = ['data' => []];

        $jobStatus = JobStatus::where('id', $data['job_id'])   //::where('user_id', auth()->user()->id)
                              ->first();

        if(!$jobStatus) { 
            // if jobStatus isn't found that means that the user isn't a job creator
            return JsonResource::make(['status' => 'unauthorized']);
        }

        if( in_array('job_status', $fetchParams) ) {
            $failedSubtasksCount = JobSubtaskFailure::where('job_status_id', $jobStatus->id)
                                                           ->count();
            $jobStatusData = $this->formatJobStatus($jobStatus, $failedSubtasksCount);
            $response['data']['job_status'] = $jobStatusData;
        }

        if( in_array('job_subtasks_failures', $fetchParams) ) {
            $lastReceivedJobSubtaskFailureId = $data['job_subtasks_failures_last_received_id'] ?? 0;
            $jobSubtasksFailures = JobSubtaskFailure::where('job_status_id', $data['job_id'])
                                                            ->where('id', '>', $lastReceivedJobSubtaskFailureId)
                                                            ->limit(10) // frontend is fetching constantly at an interval when this method is called with 'job_subtask_failures' key, therefore limit is set.
                                                            ->get();
            $failuresData = $this->formatJobSubtasksFailures($jobSubtasksFailures);
            $response['data']['job_subtasks_failures'] = $failuresData;
        }

        if( in_array('input_params', $fetchParams) ) {
            $inputParamsData = $this->formatInputParamsData($jobStatus);
            $response['data']['input_params'] = $inputParamsData;
        }

        return JsonResource::make($response);
    }

    /**
     * Return only essential data about progress of jobs.
     * 
     * @return JSON
     */
    public function ajaxGetMyJobsStatusesInfoTiny(Request $request)
    {
        $data = $request->validate([
            "count" => ["required", "integer", "min:1"],
            "skip" => ["required", "integer", "min:0"]
        ]);

        $myJobs = JobStatus::orderByDesc('created_at') //::where('user_id', auth()->user()->id)
                            ->take($data['count'])
                            ->skip($data['skip'])
                            ->get();

        $myPreparedJobs = []; 
        foreach($myJobs as $key => $job)  {
            $myPreparedJobs[$key] = [];
            $myPreparedJobs[$key]['id'] = $job->id;
            $myPreparedJobs[$key]['status'] = $job->getJobStatus();
            $myPreparedJobs[$key]['progress'] = $job->progress_percentage;
            $myPreparedJobs[$key]['type_and_name'] = $job->getPresentationFullJobTaskName();
        }

        return JsonResource::make(['data' => ['jobs_statuses' => $myPreparedJobs]]);
    }

    /**
     * Format 'import_params' data for ajax.
     * 
     * @param JobStatus $jobStatus
     * 
     * @return array
     */
    private function formatInputParamsData($jobStatus)
    {
        $inputParamsData = [];

        $inputParamsData['import_fields'] = $jobStatus->getImportFieldsMapPrepared();

        return $inputParamsData;
    }

    /**
     * Format 'job_status' data for ajax.
     * 
     * @param JobStatus $jobStatus
     * 
     * @return array
     */
    private function formatJobStatus($jobStatus, $failedSubtasksCount)
    {
        $posao = [
            'status_id' => $jobStatus->id,
            'status' => $jobStatus->getJobStatusTrans(),
            'status_original' => $jobStatus->getJobStatus(), // TODO: promeni ovaj kljuc u 'status' a prethodni u 'status_trans'
            'ime_i_tip'=> $jobStatus->getPresentationFullJobTaskName(),
            'kreiran' => $jobStatus->created_at ? $this->timestampToCustomFormat($jobStatus->created_at, $this->serbianDateTimeFormat) : '',
            'progres' => $jobStatus->progress_now . '/' . $jobStatus->progress_max,
            'pokrenut' => $jobStatus->started_at ? $this->timestampToCustomFormat($jobStatus->started_at, $this->serbianDateTimeFormat) : '',
            'zavrsen' => $jobStatus->finished_at ? $this->timestampToCustomFormat($jobStatus->finished_at, $this->serbianDateTimeFormat) : '',
            'progres_procenat' => $jobStatus->progress_percentage,
            'broj_neuspesnih_subtaskova' => $failedSubtasksCount,
            'procenat_uspesnosti' => $this->calcJobSubtasksSuccessRate($jobStatus, $failedSubtasksCount),
            'preostalo_vreme_izvrsavanja' => $jobStatus->getFormattedRemainingExecTime(),
            'cekanje_u_redu' => $jobStatus->getQueueStackCurrentWaitNumber(),
            //'tip' => $jobStatus->getJobType()
        ];

        return $posao;
    }

    /**
     * Format 'job_status_failures' data for ajax.
     * 
     * @param JobStatus $jobStatus
     * 
     * @return array
     */
    private function formatJobSubtasksFailures($jobSubtasksFailures)
    {
        return $jobSubtasksFailures;
    }

    /**
     * Izracunaj procentualno kolika je uspesnost izvrsavanja 'subtask'-ova.
     * 
     * @return integer;
     */
    private function calcJobSubtasksSuccessRate($jobStatus, $failedSubtasksCount)
    {
        $finishedSubtasksCount = $jobStatus->progress_now;

        // spreci 'division by zero'
        if(!$finishedSubtasksCount) {
            return 100;
        }
        
        return (1 - round($failedSubtasksCount/$finishedSubtasksCount, 2)) * 100;
    }

    /**
     * Start a job using executor method
     * 
     * @return JSON
     */
    public function runJob(Request $request)
    {
        $jobExecutorKeys = array_keys($this->jobExecutors);

        $data = $request->validate([
            'executor_name' => ['required', 'string', 'in:' . implode(',', $jobExecutorKeys) ]
        ]);

        $jobExecutorMethod = $this->jobExecutors[$data['executor_name']]['executor'];

        call_user_func([$this, $jobExecutorMethod]);

        return JsonResource::make(['data' => ['message' => 'Job put on queue', 'status' => 'OK']]);
    }

    /**
     * Re-run job with received job_id
     * 
     * @return JSON
     */
    public function rerunJob(Request $request)
    {
        $data = $request->validate([
            'job_id' => ['required', 'integer', 'min:0']
        ]);

        Log::info("received command to rerun job with job_id = " . $data['job_id']);

        // TODO: get failed_job ID from job_id
        // https://stackoverflow.com/questions/40139208/how-do-i-nicely-decode-laravel-failed-jobs-json
        $failedJobs = \DB::table('failed_jobs')->select()->get();

        foreach($failedJobs as $fj) {
            $aw = json_decode($fj->payload)->data->command;
            $cm = unserialize($aw);
            //logger(var_export($cm, TRUE));

            $statusId = $cm->getJobStatusId();

            if($statusId == $data['job_id']) {
                Log::info('Found entry of failed job with job ID = ' . $data['job_id'] . ' Failed job ID = ' . $fj->id);
                Artisan::call('queue:retry', ['id' => $fj->id]);

                // TODO: get result from Artisan::call queue:retry and return an error if result is invalid.

                return JsonResource::make(['data' => ['message' => 'Rerunning a job', 'status' => 'OK']]);
            }
        }

        return JsonResource::make()->withError("Error. Couldn't rerun job. Missing failed job ID");
    }

    /**
     * @return boolean
     */
    private function runSingleActionJob()
    {
        $data = [
            'user' => auth()->user()
        ];

        $job = new ExampleTrackableJob($data);
        $this->dispatch($job->onConnection('database'));

        // you can get job status ID if you need to:
        $jobStatusId = $job->getJobStatusId();

        return TRUE;
    }
    
    /**
     * @return boolean
     */
    private function runSubtasksJob()
    {
        $data = [
            'user' => auth()->user()
        ];

        $job = new ExampleTrackableJobWithSubtasks($data);
        $this->dispatch($job->onConnection('database'));

        return TRUE;
    }
}
