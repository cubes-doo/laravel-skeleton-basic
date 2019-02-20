@extends('_layout.layout')

@section('title', __("Entities"))

@section('content')
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary card-header-icon">
                <div class="card-icon">
                    <i class="material-icons">waves</i>
                </div>
                <div class="d-flex justify-content-between">
                    <h4 class="card-title">@lang('Entities')</h4>
                    <!-- begin:title-toolbar -->
                    <a href="@route('entities.create')" class="btn btn-primary btn-round">
                        <span class="btn-label">
                            <i class="material-icons">add</i>
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
                            <th>Active</th>
                            <th>Status</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Photo</th>
                            <th class="disabled-sorting text-right">Actions</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Active</th>
                            <th>Status</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Photo</th>
                            <th class="text-right">Actions</th>
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

@push('js')
    <!--  DataTables.net Plugin, full documentation here: https://datatables.net/  -->
    <script src="/theme/assets/js/plugins/jquery.dataTables.min.js"></script>
    <!-- begin:page script -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('#datatables').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "@route('entities.datatable')",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                },
                "columns": [
                    {"data": "active"},
                    {"data": "status"},
                    {"data": "title"},
                    {"data": "description"},
                    {"data": "photo", orderable: false, searchable: false},
                    {"data": "actions", orderable: false, searchable: false, "className": "text-right"}
                ],
                "pagingType": "full_numbers",
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search records",
                }
            });
    
            var table = $('#datatable').DataTable();
    
            // Edit record
            table.on('click', '.edit', function() {
                $tr = $(this).closest('tr');
                var data = table.row($tr).data();
                alert('You press on Row: ' + data[0] + ' ' + data[1] + ' ' + data[2] + '\'s row.');
            });
    
            // Delete a record
            table.on('click', '.remove', function(e) {
                $tr = $(this).closest('tr');
                table.row($tr).remove().draw();
                e.preventDefault();
            });
        
            //Like record
            table.on('click', '.like', function() {
                alert('You clicked on Like button');
            });
        });
    </script>
    <!-- end:page script -->
@endpush