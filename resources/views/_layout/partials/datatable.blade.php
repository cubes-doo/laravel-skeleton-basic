<script src="{{asset('/theme/plugins/datatables/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('/theme/plugins/datatables/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('/theme/plugins/datatables/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('/theme/plugins/datatables/buttons.bootstrap4.min.js')}}"></script>
<!-- begin:jq validation setup script-->
<script type="text/javascript">
    $.extend($.fn.dataTable.defaults, {
        "ajax": {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        "pagingType": "full_numbers",
        "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "@lang('All')"]
        ],
        responsive: true,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "@lang('Search records')",
        },
        initComplete: function () {
            $('#datatables_filter input').focus();
        }
    } );
</script>
<!-- end:jq validation setup script-->
