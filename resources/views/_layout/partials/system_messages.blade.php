<!-- begin:system-message rendering script-->
<script type="text/javascript">
    function showSystemMessage(text, type) {
        type = type ? type : 'success';

        options = {
            type: type,
            title: text,
            showConfirmButton: false,
            position: 'top-end'
        };

        if (type === 'error') {
            options['showConfirmButton'] = true;
            options['confirmButtonClass'] = 'btn-danger';
        } else {
            options['timer'] = 1500;
        }

        Swal.fire(options);
    }
    
    @unless(empty($message = request()->getSystemMessage()))
        showSystemMessage("{!!$message['text']!!}", "{{$message['type']}}");
    @endunless
</script>
<!-- end:system-message rendering script-->
