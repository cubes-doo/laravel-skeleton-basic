<!-- begin:system-message rendering script-->
<script type="text/javascript">
    @unless(empty($message = request()->getSystemMessage()))
        let type = '{{$message['type']}}';
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
            message: "{!!{{$message['text']}}!!}"
        }, {
            type: color[type],
            timer: 3000,
            placement: {
                from: 'top',
                align: 'right'
            }
        });
    @endunless
</script>
<!-- end:system-message rendering script-->
