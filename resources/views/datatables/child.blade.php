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
                <div class="card-body">
                    <p class="text-muted mb-0 text-center font-weight-bold">
                        Datatable with data from entity model with child relation.
                    </p>
                    <p class="text-muted mb-0 text-center">
                        This datatables code is exact same as code in datatable with parent relationship.
                        <br>
                        Only difference is relationship with entity (main) table and relation child table.
                    </p>
                </div>
                <!-- end content-->
            </div>
        <!--  end card  -->
        </div>
        <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary card-header-icon">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title"></h4>
                    <!-- begin:title-toolbar -->
                    <a href="javascript:;" class="btn btn-primary btn-round">
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
                            <th>@lang('Title')</th>
                            <th>@lang('Child Title')</th>
                            <th class="disabled-sorting text-right">@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>@lang('Title')</th>
                            <th>@lang('Child Title')</th>
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
                url: "@route('datatables.with_child.datatable')",
                type: "POST"
            },
            "columns": [
                {"data": "title", "name":"title"},
                {"data": "child", "name":"title"},
                {"data": "actions", orderable: false, searchable: false, "className": "text-right"}
            ]
        });
        
    </script>
    <!-- end:page script -->
@endpush

