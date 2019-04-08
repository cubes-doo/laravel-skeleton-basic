<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        },
        success: function(response){
            showSystemMessage(response.message);

            if($('#datatables').length > 0) {
                $('#datatables').DataTable().draw();
            } else {
                console.log('No datatables to reload');
            }
        },
        error: function(){
            showSystemMessage("@lang('An error occured when trying to execute this action')", "error");
        }
    });
</script>