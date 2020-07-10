@extends('_layout.layout')

@section('head_title', __("Jobs"))

@push('head_links')
    <link href="{{asset('/theme/plugins/datatables/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>    
    <link href="../plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
@endpush

@push('head_links')
<style>
    .job-finished {
        background-color: white !important;
    }
    .job-failed {
        color: rgb(247, 83, 31);
        background-color: rgb(255, 47, 53, 0.2) !important;
    }
    .job-failed td {
        border-top: 1px solid rgb(247, 83, 31) !important;
        border-bottom: 1px solid rgb(247, 83, 31) !important;
    }
    .job-queued {
        background-color: white !important;
    }
    .job-executing {
        background-color: rgb(69, 187, 224, 0.2) !important;
    }
    .job-executing td {
        border-top: 1px solid rgb(69, 187, 224) !important;
        border-bottom: 1px solid rgb(69, 187, 224) !important;
    }

    textarea[name=job_description] {
        border-color: #ff9800; /*rgb(69, 187, 224); */
        resize: none;

        font-size: 14px;
        font-style: italic;

        white-space: normal;
        text-align: justify;
        -moz-text-align-last: center; /* Firefox 12+ */
        text-align-last: center;
    }
    textarea[name=job_description]:disabled {
        background-color: white;
    }
</style>
@endpush

@section('content')
    @include('_layout.partials.breadcrumbs', [
        'pageTitle' => __("Jobs"),
        'breadcrumbs' => [
            url('/') => __('Home')
        ]
    ])

    <!-- SELECT JOB TO RUN -->
    <div class="row">
        <div class="col-12">
            <div class="card pb-4">
                <div class="card-heading d-flex justify-content-between">
                    <div class="card-heading-title">
                        <h3 class="card-title">
                            @lang('Run jobs')
                        </h3>
                        <p class="card-sub-title text-muted">@lang('Select job from a list to run')</p>
                    </div>
                    <div class="card-heading-actions">
                    </div>
                </div>
                <div class="card-body" style="padding-top: 0px; padding-bottom: 0px;">
                    <form id="entities-filter-form" autocomplete="off">
                        <div class="row h-100 justify-content-center align-items-center">
                            <div class="col-sm-3" style="padding: 50px;">
                                <div class="form-group">
                                    <label>@lang('Predefined jobs')</label>
                                    <select id="job_executor_selector" class="selectpicker" data-style="btn-info" name="status">
                                        <option value="">-- @lang('Select predefined job to run') --</option>
                                        @foreach ($jobExecutors as $jobExecutorKey => $jobExecutorData)
                                            <option value="{{ $jobExecutorKey }}">{{ $jobExecutorData['title'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div class="form-group pl-4 ">
                                    <label class="control-label">
                                        @lang('Job description')
                                    </label>
                                    <div>
                                        <textarea name="job_description" disabled="disabled" class="form-control" rows="3" maxlength="655">No predifined job is selected, ergo no description of a job...</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2 text-center">
                                <button type="button" id="run_job" class="btn btn-success btn-round" style="height: 50px; border-radius: 7%;" disabled="disabled">
                                    <span class="btn-label">
                                        <i class="mdi mdi-chevron-left"></i>
                                    </span>
                                    @lang('RUN')
                                    <span class="btn-label">
                                        <i class="mdi mdi-chevron-right"></i>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- FILTER JOBS -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-heading d-flex justify-content-between">
                    <div class="card-heading-title">
                        <h3 class="card-title">
                            @lang('Filter jobs')
                        </h3>
                        <p class="card-sub-title text-muted">@lang('Filter jobs by their status')</p>
                    </div>
                    <div class="card-heading-actions">
                    </div>
                </div>
                <div class="card-body" style="padding-top: 0px; padding-bottom: 0px;">
                    <form id="jobs_filter_form" autocomplete="off">
                        <div class="row h-100 justify-content-center align-items-center">
                            {{-- Show user image if needed --}}
                            <!-- div class="col-sm-3">
                                <div class="about-team-member text-center">
                                    <img src="{{asset('/theme/assets/images/users/avatar-4.jpg')}}" alt="team-member" class="img-fluid d-block rounded-circle" style="margin: 0 auto;">
                                    <h4>Hugo Moncrieff</h4>
                                    <p>Creative Director</p>
                                </div>
                            </div -->
                            <div class="col-sm-2" style="padding: 50px;">
                                <div class="form-group">
                                    @if($allStatusesInfo['broj_na_izvrsavanju'] && ($veznik = $allStatusesInfo['broj_na_izvrsavanju'] > 1 ? __('su') : __('je')) )
                                        <div class="alert alert-info alert-white mb-3 text-center">{{ $allStatusesInfo['broj_na_izvrsavanju'] }} {{ $veznik }} @lang('trenutno na izvršavanju')</div>
                                    @endif
                                    <label>@lang('Status')</label>
                                    <select class="selectpicker" data-style="btn-info" name="status">
                                        <option value="">-- @lang('All') --</option>
                                        @foreach([
                                            \App\Models\JobStatus::STATUS_FINISHED_TRANS => \App\Models\JobStatus::STATUS_FINISHED,
                                            \App\Models\JobStatus::STATUS_EXECUTING_TRANS => \App\Models\JobStatus::STATUS_EXECUTING,
                                            \App\Models\JobStatus::STATUS_RETRYING_TRANS => \App\Models\JobStatus::STATUS_RETRYING,
                                            \App\Models\JobStatus::STATUS_QUEUED_TRANS => \App\Models\JobStatus::STATUS_QUEUED,
                                            \App\Models\JobStatus::STATUS_FAILED_TRANS => \App\Models\JobStatus::STATUS_FAILED,
                                        ] as $statusTrans => $status)
                                            <option value="{{ $status }}">{{ $statusTrans }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="about-features-box text-center">
                                    <div class="feature-icon bg-success mb-4">
                                        <i class="dripicons-graph-pie"></i>
                                    </div>
                                    <h5 class="mb-3">@lang('Finished') </h5>

                                    <p class="info-suma-grupe-statusa text-success">{{ $allStatusesInfo['broj_zavrsenih'] }}</p>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="about-features-box text-center">
                                    <div class="feature-icon bg-primary mb-4">
                                        <i class="dripicons-wrong"></i>
                                    </div>
                                    <h5 class="mb-3">@lang('Finished with errors') </h5>

                                    <p class="info-suma-grupe-statusa text-primary">{{ $allStatusesInfo['broj_zavrsenih_ali_sa_greskama'] }}</p>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="about-features-box text-center">
                                    <div class="feature-icon bg-secondary mb-4">
                                        <i class="dripicons-clockwise"></i>
                                    </div>
                                    <h5 class="mb-3">@lang('Retryable') </h5>

                                    <p class="info-suma-grupe-statusa text-secondary">{{ $allStatusesInfo['number_of_retrying'] }}</p>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="about-features-box text-center">
                                    <div class="feature-icon bg-warning mb-4">
                                        <i class="dripicons-hourglass"></i>
                                    </div>
                                    <h5 class="mb-3">@lang('Waiting on queue')</h5>

                                    <p class="info-suma-grupe-statusa text-warning">{{ $allStatusesInfo['broj_na_cekanju'] }}</p>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="about-features-box text-center">
                                    <div class="feature-icon bg-danger mb-4">
                                        <i class="dripicons-warning"></i>
                                    </div>
                                    <h5 class="mb-3">@lang('Failed')</h5>

                                    <p class="info-suma-grupe-statusa text-danger">{{ $allStatusesInfo['broj_potpuno_neuspesnih'] }}</p>
                                </div>
                            </div>
                            <!-- div class="col-sm-1">
                                &nbsp
                            </div -->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JOBS DATATABLE -->
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary card-header-icon">                    
                <div class="card-heading-title">
                        <h3 class="card-title">
                            @lang('List of all jobs statuses')
                        </h3>
                </div>
            </div>
            <div class="card-body">
                <div class="material-datatables">
                    <table id="datatables" class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Type')</th>
                            <th>@lang('For')</th>
                            <th>@lang('Created')</th>
                            <th>@lang('Started')</th>
                            <th>@lang('Finished')</th>
                            <th>@lang('Total rows')</th>
                            <th>@lang('Options')</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    </table>
                </div>
            </div>
            <!-- end content-->
        </div>
        <!--  end card  -->
        </div>
        <!-- end col-md-12 -->
    </div>
@endsection

@push('footer_scripts')
<!-- begin:page script -->
<script src="{{asset('/theme/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<!-- begin:default config scripts -->
@include('_layout.partials.datatable')
<!-- end:default config scripts -->

<script type="text/javascript">
$(function() {

    var blade = {
        datatableAjaxUrl: "@route('jobs.datatable')",
        runJobUrl: "@route('jobs.run_job')" 
    }

    // unpack job executors directly to JS object
    var jobExecutors = {!! json_encode($jobExecutors) !!}

    // DATATABLES
    var datatable = $('#datatables').DataTable({
        "serverSide": true,
        "responsive": true,
        "searching": false,
        "ajax": {
            url: "@route('jobs.datatable')",
            type: "POST",
        },
        "columns": [
            {data: "id", name: "id", responsivePriority: 1},
            {data: "status", name: "status", responsivePriority: 1},
            {data: "job_type", name: "job_type", responsivePriority: 1},
            {data: "job_task_name", name: "job_task_name", responsivePriority: 1},
            {data: "created_at", name: "created_at", responsivePriority: 2},
            {data: "started_at", name: "started_at", responsivePriority: 2},
            {data: "finished_at", name: "finished_at", responsivePriority: 2},
            {data: "progress_max", name: "progress_max", responsivePriority: 2},
            {data: "actions", orderable: false, searchable: false, "className": "text-right", responsivePriority: 1},
        ],
        "order": [[0, "dsc"]]
    });  

    // Change description on change of selection of an executor
    $('#job_executor_selector').on('change', function() {
        let selected = $(this).val();
        let description = "No predifined job is selected, ergo no description of a job...";
        let btnDisabled = true;

        if (selected !== "") {
            description = jobExecutors[selected]["description"];
            btnDisabled = false;
        }

        $("#run_job").prop('disabled', btnDisabled);
        $("[name=job_description]").val(description);

    });

    // Run job button listener. Sends executor name over ajax to run a job.
    $("#run_job").on('click', function() {
        let executorName = $('#job_executor_selector').val();

        if(executorName === "") {
            return false;
        }

        $.ajax({
            url: "@route('jobs.run_job')",
            method: 'POST',
            data: {
                'executor_name': executorName
            },
            success: function(response){
                showSystemMessage(response.data.message);

                performDatatableUpdate(datatable, $('#jobs_filter_form'), blade.datatableAjaxUrl);
            }
        });
    });

    // Re-run job event listener
    $('#datatables').on('click', '.rerun-job-btn', function() {
        let jobId = $(this).data('job-id');
        console.log("rerunning job with an id = " + jobId);

        $.ajax({
            url: "@route('jobs.rerun_job')",
            method: 'POST',
            data: {
                'job_id': jobId
            },
            success: function(response){
                showSystemMessage(response.data.message);

                performDatatableUpdate(datatable, $('#jobs_filter_form'), blade.datatableAjaxUrl);
            },
            error: function(e){
                let response = e; //JSON.parse(e);
                console.log(e);
                showSystemMessage(response.responseJSON.message, "error");
                console.error(e);
            }
        });

    });

    /* 
     * CUSTOM SEARCH - VER 2. 
     * Adds GET parameters to ajax URL for datatable search.
     * It's used when Ver 1. cannot be used because of parameters
     * which doesn't have same names as column names in the target
     * database table.
     */
    function performDatatableUpdate(datatable, searchForm, ajaxUrl) {

        var paramObj = {};
        
        if(searchForm) {
            searchForm.find('[name]').each(function (index, element) {
                let name = $(element).attr('name');
                let value = $(element).val();
                paramObj[name] = value;
            });
        }

        // construct 'GET query string'
        let qs = $.param( paramObj );

        datatable.ajax.url(ajaxUrl + '?' + qs)
                      .load();

        // TODO: get current datatables page and set it after update
        // instead of .load() -> BAD: FOCUSES ON DATATABLE
        //datatable.draw('full-hold');
    }

    // on filter inputs change, update datatable
    $('#jobs_filter_form').on('change', function () {
        performDatatableUpdate(datatable, $(this), blade.datatableAjaxUrl);
    })  
    .trigger('change');

    // on every 10 seconds perform update of datatable
    //Bad user experience if datatables page is different than 1. It always changes datatables page to 1. 
    /*
    setInterval(function() {
        performDatatableUpdate(datatable, $('#jobs_filter_form'), blade.datatableAjaxUrl);
    }, 10000);
    */

});
</script>
@endpush