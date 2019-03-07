<!-- begin:system-message rendering script-->
<script type="text/javascript">
    function showSystemMessage(text, type) {
        type = type ? type : 'success';
        let color = {
            'info': 'info', 
            'error': 'danger', 
            'success': 'success', 
            'warning': 'warning'
        };
        
        let icon = {
            'info': 'info', 
            'error': 'error', 
            'success': 'done', 
            'warning': 'warning'
        };

        $.notify({
            icon: icon[type],
            message: text
        }, {
            type: color[type],
            timer: 3000,
            placement: {
                from: 'top',
                align: 'right'
            }
        });
    }

    @unless(empty($message = request()->getSystemMessage()))
        showSystemMessage("{!!$message['text']!!}", "{{$message['type']}}");
    @endunless
</script>
<!-- end:system-message rendering script-->
