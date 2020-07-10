@extends('_layout.layout')

@section('head_title', __('Job status'))

@push('head_links')
<style>
    .distancer {
        background-color: rgb(69, 187, 224, 0.3);
        margin-bottom: 10px;
    }

    /* MAYBE X-001   <--- 'string' to search
    .knob-design-balancer {
        padding-top: 10px;
    }
    .knob-design-balancer .vertical-text {
        transform: rotate(90deg);
        transform-origin: left top 0;
        border: 1px solid rgb(230,230,230); 
        color: rgb(200,200,200); 
        padding-left: 13px; 
        padding-right: 13px;
        padding-top: 7px;
        padding-bottom: 7px;
    }*/

    .table-sim {
        display: table;
    }
    .tr-sim {
        display: table-row;
    }
    .tc-sim {
        display: table-cell;
        height: 60px;
        min-width: 272px;
        vertical-align: middle;
    }

    .status-wrapper {
        font-size: 14px;
        color: #797979;
    }
    .status-wrapper .naznaceno {
        color: #797979;
    }
    .status-wrapper #status {
        font-size: 16px;
        font-weight: bold;
        /* color: rgb(247, 83, 31); */
        color: gray;
    }
    .status-wrapper #progres {
        color: rgb(0, 123, 255);
    }
    .status-wrapper #preostalo_vreme_izvrsavanja {
        color: #348cd4;
    }
    .status-wrapper #cekanje_u_redu {
        color: #8892d6;
    }
    .status-wrapper #kreiran, .status-wrapper #pokrenut, .status-wrapper #zavrsen {
        color: rgb(23, 162, 184);
    }
    .status-wrapper #broj_neuspesnih_subtaskova {
        color: rgb(247, 83, 31);
    }

    #velika_statusna_poruka {
        border: 1px dashed #ff9800;
    }
    #velika_statusna_poruka h1 {
        margin-top: 100px;
        margin-bottom: 100px;
        color: #ff9800;
        text-align: center;
    }

    #job_subtasks_failures_table .validationErrorMsg {
        color: red;
    }
    #job_subtasks_failures_table .subtaskIdTd {
        text-align: center;
        font-weight: bold;
        background-color: rgb(255,237, 232, 0.6);
    }
    #job_subtasks_failures_table .subtableTd {
        padding: 0;
    }
    #job_subtasks_failures_table .mainErrorMsg {
        font-weight: bold;
        color: rgb(247, 83, 31);
        text-align: center;
    }
    #job_subtasks_failures_table .failureRow > td {
        border-bottom: 2px solid rgb(247, 83, 31);
        border-right: 1px solid #dee2e6;
    }
    #job_subtasks_failures_table .subtableTd table tr.failureSubtableRow td {
        padding-top: 3px;
        padding-bottom: 3px;
    }
    #job_subtasks_failures_table .subtableTd table tr.failureSubtableRow td:nth-child(1) {
        width: 20%; 
    }
    #job_subtasks_failures_table .subtableTd table tr.failureSubtableRow td:nth-child(2) {
        width: 4%; 
        color: gray;
        border-left: 1px solid #dee2e6;
        border-right: 1px solid #dee2e6;
        text-align: center;
        background-color: whitesmoke;
    }
    #job_subtasks_failures_table .subtableTd table tr.failureSubtableRow td:nth-child(3) {
        width: 50%; 
    }
    #job_subtasks_failures_table .subtableTd table tr.failureSubtableRow td:nth-child(4) {
        width: 26%; 
    }
</style>
@endpush

@section('content')

@include('_layout.partials.breadcrumbs', [
    'pageTitle' => __("Job status"),
    'breadcrumbs' => [
    ]
])

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-heading d-flex justify-content-between">
                <div class="card-heading-title">
                    <h3 class="card-title">
                        @lang('Job status')
                    </h3>
                    <p class="card-sub-title text-muted">@lang('Overview of job status/progress')</p>
                </div>
                <div class="card-heading-actions">
                    <h4 style="color: rgba(121,121,121,0.8);"><span><!-- i class="fa fa-random mr-2"></i></span--><span id="ime_i_tip"></span></h4>
                </div>
                <div class="card-heading-actions">
                    <a href="{{ route('jobs.list') }}" class="btn btn-success">
                        <i class="fa fa-cloud"></i>
                        &nbsp; @lang('All jobs')
                    </a>
                </div>
            </div>
            <div class="card-body" style="padding-top: 0px;">

                <hr class="distancer">

                <!-- JOB STATUS INFO -->
                <div class="row status-wrapper">
                    <div class="col-9 table-sim">
                        <div class="tr-sim">
                            <div class="tc-sim">
                                <span class="naznaceno">@lang('Status'):</span> <span id="status"></span><br>
                            </div>
                            <div class="tc-sim">
                                <span class="naznaceno">@lang('Created'):</span> <span id="kreiran"></span><br>
                            </div>
                            <div class="tc-sim">
                                <span class="naznaceno">@lang('Failed subtasks'):</span> <span id="broj_neuspesnih_subtaskova">0</span><br>
                            </div>
                            <div class="tc-sim">
                                <span class="naznaceno">@lang('Remaining time'):</span> <span id="preostalo_vreme_izvrsavanja"></span><br>
                            </div>
                        </div>
                        <div class="tr-sim">
                            <div class="tc-sim">
                                <span class="naznaceno">@lang('Completed'):</span> <span id="progres"></span>
                            </div>
                            <div class="tc-sim">
                                <span class="naznaceno">@lang('Started'):</span> <span id="pokrenut"></span>
                            </div>
                            <div class="tc-sim">
                                <span class="naznaceno">@lang('Queue wait'):</span> <span id="cekanje_u_redu">0</span>
                            </div>
                            <div class="tc-sim">
                                <span class="naznaceno">@lang('Finished'):</span> <span id="zavrsen"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-3" style="padding-top: 10px;">
                        <!-- MOZDA X-001 -->
                        <!-- div class="text-center knob-design-balancer" style="position: absolute; left: 100px; top: 0px;">
                            <p class="vertical-text">
                                procenat <br>uspešnosti
                            </p>
                        </div -->
                        <!-- KNOB (PROCENAT USPESNOSTI) -->
                        <div class="widget-chart text-center">
                            <div style="display:inline;width:100px;height:100px;"><input id="procenat_uspesnosti" data-width="100" data-height="100" data-linecap="round" data-fgcolor="gray" value="0" data-skin="tron" data-angleoffset="180" data-readonly="true" data-thickness=".15" readonly="readonly" style="width: 79px; height: 50px; position: absolute; vertical-align: middle; margin-top: 50px; margin-left: -114px; border: 0px none; background: rgba(0, 0, 0, 0) none repeat scroll 0% 0%; font: bold 30px Arial; text-align: center; color: rgb(52, 211, 235); padding: 0px; -moz-appearance: none;"></div>
                            <!-- span class="text-muted m-t-30">@lang('Procenat uspešnosti')</span -->
                        </div>
                        <!-- MOZDA X-001 -->
                        <!-- div class="text-center knob-design-balancer" style="position: absolute; right: -65px; top: 0px;">
                            <p class="vertical-text">
                                procenat <br>uspešnosti
                            </p>
                        </div -->
                    </div>
                </div>
                <!-- PROGRESS BAR -->
                <div class="row">
                    <div class="col-12">
                        <div class="progress progress-md mt-3" id="job_progressbar">
                            <div class="progress-bar bg-danger" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                                <span class="sr-only">0% Complete</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- LISTA SVIH GRESAKA -->
<div class="row" id="job_subtasks_failures_wrapper">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <div id="velika_statusna_poruka">
                    <h1></h1>
                </div>

                <table id="job_subtasks_failures_table" class="d-none table m-0 table-colored-bordered table-bordered-danger">
                    <thead>
                        <tr>
                            <th style="width: 90px;">@lang('Row number')</th>
                            <th style="width: 330px;">@lang('Error text')</th>
                            <th>@lang('Column / Data / Errors')</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<!-- TEMPLATE (hidden) -->
<div id="failures_table_template" class="d-none">
    <table>
        <thead>
        </thead>
        <tbody>
            <tr class="failureRow">
                <td class="subtaskIdTd"></td>
                <td class="mainErrorMsg"></td>
                <td class="subtableTd">
                    <table style="width: 100%">
                    </table>
                </td>
            </tr>
            <tr class="failureSubtableRow"> {{-- namenjeno je da se koristi kao 'template' za red unutar $('.failureRow table') --}}
            </tr>
        </tbody>
    </table>
</div>


@endsection

@push('footer_scripts')
<!-- KNOB JS -->
<!--[if IE]>
<script type="text/javascript" src={{asset('/theme/plugins/jquery-knob/excanvas.js')}}"></script>
<![endif]-->
<script src="{{asset('/theme/plugins/jquery-knob/jquery.knob.js')}}"></script>

<script>
$(function() {

    var blade = {
        jobInitialStatus: "{{ $jobStatus->getJobStatusTrans() }}",
        jobStatusId: "{{ $jobStatus->id }}",
        jobEnded: "{{ $jobStatus->is_ended ? 1 : 0 }}",
        jobStatusAjaxUrl: "@route('jobs.ajax_status')",
        jobStatusQueued: "{{ \App\Models\JobStatus::STATUS_QUEUED_TRANS }}",
        jobStatusExecuting: "{{ \App\Models\JobStatus::STATUS_EXECUTING_TRANS }}",
        jobStatusRetrying: "{{ \App\Models\JobStatus::STATUS_RETRYING }}",
        jobStatusFinished: "{{ \App\Models\JobStatus::STATUS_FINISHED_TRANS }}",
        jobStatusFailed: "{{ \App\Models\JobStatus::STATUS_FAILED_TRANS }}",
        jobStatusFinishedWithErrors: "{{ \App\Models\JobStatus::STATUS_FINISHED_WITH_ERRORS_TRANS }}",
        jobStatusAjaxCallTimeout: "{{ config('job-status.import_job_status_ajax_call_timeout', 5000) }}",
        jobType: "{{ $jobStatus->getJobType() }}",
        excelImportJobType: "{{ \App\Models\JobStatus::JOB_TYPE_EXCEL_IMPORT }}"
    };

    var JOB_FAILURES_LAST_RECEIVED_ID = 0;

    // inicijalizuj 'widget' za procenat uspesnosti
    $('#procenat_uspesnosti').knob() /*{           // ne izvrsava 'format callback' nakon trigger('configure') !
        'format' : function(v) { return v + '%' }
    });*/

    /*
     * Popuni podatke 'top info' i 'update'-uj vrednost progresbara.
     */
    function updateJobStatus(data) {
        $.each(data, function(key, entry) {
            if(key == 'progres_procenat') {
                setProgressbarAdvancement(entry);
            }
            else if(key == 'procenat_uspesnosti') {
                setProcenatUspesnostiKnob(entry);
            }
            else {
                if($('#' + key).length) {
                    $('#' + key).text(entry);
                }
            }
        });

        obojiInfoStatus(data.status);
    }

    function setProcenatUspesnostiKnob(val) {

        if(val == 100) {
            color = '#78c350';
        }
        else if(val >= 90 ) {
            color = 'lightgreen';
        }
        else if(val >= 60 ) {
            color = '#ff9800';
        }
        else if(val >= 40) {
            color = 'rgb(255, 132, 0)';
        }
        else {
            color = 'red';
        }

        $('#procenat_uspesnosti')
            .val(val)
            .trigger('change')
            .trigger(
                'configure',
                {
                    "inputColor": color,
                    "fgColor": color,
                    //'format' : function(v) { return v + '%' }
                }
            )
    }

    /**
     * Popuni tabelu sa 'job failures'-ima.
     */
    function updateJobFailures(all_data) {
        let data = all_data.job_subtasks_failures;
        let tableBody = $('#job_subtasks_failures_table tbody')

        let importFields = [];
        if(all_data.input_params && all_data.input_params.import_fields) {
            importFields = all_data.input_params.import_fields;
        }

        if(data.length > 0) {
            $('#velika_statusna_poruka').addClass('d-none');
            $('#job_subtasks_failures_table').removeClass('d-none');
        }

        $.each(data, function(key, failureData) {

            let templateFailureRow = $('#failures_table_template .failureRow').first();

            let jqRow = templateFailureRow.clone();
            let cells = jqRow.children('td');

            let jqIdCell = $(cells[0]);
            let jqErrMsgCell = $(cells[1]);
            let jqRowDataCell = $(cells[2]);

            jqIdCell.html(failureData.subtask_id);
            jqErrMsgCell.html(failureData.main_error_message);

            // ubaci podatke u pod-tabelu
            let cellDataTable = jqRowDataCell.find('table');
            let templateFailureSubtableRow = $('#failures_table_template .failureSubtableRow').first();

            let subtaskData = JSON.parse(failureData.subtask_data); // objekat
            let allErrorsData = JSON.parse(failureData.all_errors_messages); // niz

            // iskombinuj kljuceve objekta i niza u jedan niz kljuceva
            // -------------------------------------------------------
            // TODO: debagovati na 'backend'-u ovu situaciju kada br. el. podataka nije jednak br. el. grasaka!
            let allKeys = [];
            $.each(subtaskData, function(key, val) {
                allKeys.push(key);
            });
            for(i=0; i<allErrorsData.length; ++i) {
                if($.inArray(i, allKeys) == -1) {
                    allKeys.push(i);
                }
            }
            // --------------------------------------------------------------------

            // prodji kroz sve greske jednog reda, i dodaj kreirani element u tabeli sa greskama
            for(i=0; i<allKeys.length; ++i) { // subtaskData.length
                let jqSubtableRow = templateFailureSubtableRow.clone();
                let colName = importFields[i] !== undefined ? importFields[i] : '---';
                let colLetter = String.fromCharCode(65 + i);

                let colNameHtml = '<td style="display: none;"></td>';
                let letterHtml = '<td style="display: none;"></td>';
                let validationErrMsg = '<td style="display: none;"></td>';

                if(blade.jobType == blade.excelImportJobType) {
                    colNameHtml = '<td>' + colName + '</td>';
                    letterHtml = '<td>' + colLetter + '</td>';
                    validationErrMsg = '<td class="validationErrorMsg">' + (allErrorsData[i] !== undefined ? allErrorsData[i] : '') + '</td>';
                }

                let html = colNameHtml
                         + letterHtml
                         + '<td>' + (subtaskData[i] !== undefined ? subtaskData[i] : '') + '</td>'
                         + validationErrMsg;

                jqSubtableRow.html(html);
                cellDataTable.append(jqSubtableRow);
            }

            tableBody.append(jqRow);

            JOB_FAILURES_LAST_RECEIVED_ID = failureData.id;
        });
    }

    function getJobSubtasksFailuresLastRecvId() {
        return JOB_FAILURES_LAST_RECEIVED_ID;
    }

    /* 
     * Uzmi podatke statusa 'job'-a kao i podatke gresaka
     */
    function getAjaxData(fetchParams) {
        
        let ajaxData = {
                    'job_id': blade.jobStatusId,
                    'job_subtasks_failures_last_received_id': getJobSubtasksFailuresLastRecvId(),
                    'fetch': []
                };

        // ukoliko je broj gresaka vec prikazanih manji od 20 onda zatrazi i greske 
        // u svakom slucaju nezavisno da li ih pozivalac trazi.
        if($('#job_subtasks_failures_table tbody tr.failureRow').length < 20) {
            ajaxData.fetch = ['job_status', 'job_subtasks_failures', 'input_params'];
        }
        else if(fetchParams) {
            ajaxData.fetch = fetchParams;
        } 
        else {
            ajaxData.fetch = ['job_status', 'job_subtasks_failures', 'input_params'];
        }

        $.ajax({
            'url': blade.jobStatusAjaxUrl,
            'type': 'get',
            'data': ajaxData, 
            'success': function(response) { // <- not using .done() because ajax.blade.php $.ajaxSetup uses 'success' callback.
                if(response.data.job_status) {
                    updateJobStatus(response.data.job_status);
                }
                if(response.data.job_subtasks_failures) {
                    updateJobFailures(response.data);
                }
            },
            'error': function(e) {
                console.error(e);
            }
        })
    }

    // Proveri status 'job'-a na svakih blade.jobStatusAjaxCallTimeout milisekundi
    // nemoj da 'fetch'-ujes greske
    var ajaxCall = setInterval(function() {
        getAjaxData(['job_status']); 
    }, blade.jobStatusAjaxCallTimeout);

    /**
     * Oboji tekst statusa.
     */
    function obojiInfoStatus(status) {
        let statusBoja = 'blue'; // status zavrsen ali sa greskama
        $('#velika_statusna_poruka h1').text('Job was finished but with errors');

        if(status == blade.jobStatusFinished) {
            statusBoja = 'rgb(107, 187, 65)'; // 'green';
            $('#velika_statusna_poruka h1').text('Job finished successfuly');
            setProgressbarAdvancement(100);
            //clearInterval(ajaxCall); // TODO: Uradi clearInteraval tek kada prikazes sve greske!
        }
        else if(status == blade.jobStatusExecuting) {
            statusBoja = 'rgb(23, 162, 184)'; // 'green-blue';
            $('#velika_statusna_poruka h1').text('Job is executing without errors');
        }
        // currently, 'imTigger package' sets 'retrying' status even when an exception occurs.
        else if(status == blade.jobStatusRetrying) {
            statusBoja = 'rgb(108, 117, 125)'; // 'gray'
            $('#velika_statusna_poruka h1').text('Retrying job execution');
        }
        else if(status == blade.jobStatusFailed) {
            statusBoja = 'rgb(247, 83, 31)'; // 'red'
            $('#velika_statusna_poruka h1').text('Job was terminated because of an error');
        }
        else if(status == blade.jobStatusQueued) {
            statusBoja = 'orange';
            $('#velika_statusna_poruka h1').text('Job is waiting on queue');
        }

        $('#velika_statusna_poruka').css({'border': '1px dashed ' + statusBoja});
        $('#velika_statusna_poruka h1').css({color: statusBoja});

        $('#status').css({color: statusBoja});
    }


    /*
     * Pomeri progres bar i stilizuj ga sa bojom.
     */
    function setProgressbarAdvancement(percentageValue) {
        let progBar = $('#job_progressbar .progress-bar').first();
        progBar.css({ width: percentageValue + '%' });
        progBar.attr('aria-valuenow', percentageValue);

        if(percentageValue >= 20 && percentageValue < 50) {
            progBar.removeClass(['bg-danger']);
            progBar.addClass('bg-warning');
        }
        else if(percentageValue >= 50 && percentageValue < 70) {
            progBar.removeClass(['bg-danger', 'bg-warning']);
            progBar.addClass('bg-info');
        }
        else if(percentageValue >= 70) {
            progBar.removeClass(['bg-danger', 'bg-warning', 'bg-info']);
            progBar.addClass('bg-success');
        }
    }

    // inicijalno pozovi podatke statusa i gresaka (zato sto se setInterval 
    // okida prvi put tek nakon intervala a i u tim intervalskim pozivima 
    // se ne 'fetch'-uju greske
    getAjaxData(['job_status', 'job_subtasks_failures', 'input_params']);


    // Sledi kod vezan za okidanje nakon skrolovanja na dno stranice
    // ---------------------------------------------------------------
    function throttle(fn, threshhold, scope) {
        threshhold || (threshhold = 250);
        var last,
            deferTimer;
        return function () {
            var context = scope || this;

            var now = +new Date,
                args = arguments;
            if (last && now < last + threshhold) {
                // hold on to it
                clearTimeout(deferTimer);
                deferTimer = setTimeout(function () {
                                last = now;
                                fn.apply(context, args);
                            }, threshhold);
            } else {
                last = now;
                fn.apply(context, args);
            }
        };
    }

    function isElBottom(el) {
        return el.getBoundingClientRect().bottom <= window.innerHeight;
    }

    function trackScrolling() {
        let wrappedElement = document.getElementById('job_subtasks_failures_wrapper');

        if (isElBottom(wrappedElement)) {
            $(wrappedElement).trigger('job_failures_bottom_scroll.skeleton');
        }
    }

    document.addEventListener('scroll', trackScrolling);

    // Prilikom skrolovanja na dno gresaka uzmi nove greske preko ajaksa
    $('#job_subtasks_failures_wrapper').on('job_failures_bottom_scroll.skeleton', throttle(function(e) {
        getAjaxData(['job_subtasks_failures', 'input_params']);
    }, 3000));

});
</script>
@endpush
