<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        },
        success: function(response){
            showSystemMessage(response.message);

            if($('#datatables').DataTable()) {
                $('#datatables').DataTable().draw();
            } else {
                console.log('no datatables to reload; must have id "datatables"');
            }
        },
        error: function(){
            Swal.fire({
                title: "@lang('Ooops..')",
                text: "@lang('An error occured when trying to execute this action')",
                type: 'error'
            });
        }
    });
</script>