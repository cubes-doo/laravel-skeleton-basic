@extends('_layout.layout')

@section('head_title', __("Entities"))

@push('head_links')
    <link href="{{asset('/theme/plugins/datatables/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>    
    <link href="../plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
@endpush

@section('content')
    @include('_layout.partials.breadcrumbs', [
        'pageTitle' => __("Entities"),
        'breadcrumbs' => [
            url('/') => __('Home')
        ]
    ])
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary card-header-icon">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title"></h4>
                    <!-- begin:title-toolbar -->
                    <a href="@route('entities.create')" class="btn btn-primary btn-round">
                        <span class="btn-label">
                            <i class="mdi mdi-plus-circle-outline"></i>
                        </span>
                        @lang('Create')
                    </a>
                    <!-- end:title-toolbar  -->
                </div>
            </div>
            <div class="card-body">
                <div class="material-datatables">
                    <table id="datatables" class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
                    <thead>
                        <tr>
                            <th>@lang('Active')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Title')</th>
                            <th>@lang('Description')</th>
                            <th>@lang('Photo')</th>
                            <th class="disabled-sorting text-right">@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>@lang('Active')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Title')</th>
                            <th>@lang('Description')</th>
                            <th>@lang('Photo')</th>
                            <th class="text-right">@lang('Actions')</th>
                        </tr>
                    </tfoot>
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
        var table = $('#datatables').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "@route('entities.datatable')",
                type: "POST",
            },
            "columns": [
                {"data": "active"},
                {"data": "status"},
                {"data": "title"},
                {"data": "description"},
                {"data": "photo", orderable: false, searchable: false},
                {"data": "actions", orderable: false, searchable: false, "className": "text-right"}
            ],
        });

        // Delete record
        table.on('click', '.delete', function() {
            // fetch needed data from row

            // show swal to make sure this is an intentional action

            // make an ajax request

            // show swal w/ the system-message
            $tr = $(this).closest('tr');
            var data = table.row($tr).data();
            table.draw();
            alert('You press on Row: ' + data[0] + ' ' + data[1] + ' ' + data[2] + '\'s row.');
        });

        // De-/Activate a record
        table.on('click', '.activate-deactivate', function(e) {
            // fetch needed data from row

            // make an ajax request
            
            // show swal w/ the system-message
            $tr = $(this).closest('tr');
            table.draw();
        });
    </script>
    <!-- end:page script -->
@endpush