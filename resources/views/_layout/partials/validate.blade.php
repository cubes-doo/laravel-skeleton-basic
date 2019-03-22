<script src="{{asset('/theme/plugins/jquery-validation/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('/theme/plugins/jquery-validation/js/additional-methods.js')}}"></script>
<!-- begin:jq validation setup script-->
<script type="text/javascript">
    jQuery.validator.setDefaults({
        highlight: function (element) {
            $(element).closest('.form-group').addClass("is-invalid");
            $(element).addClass("is-invalid");
        },
        unhighlight: function (element) {
            $(element).closest('.form-group').removeClass('is-invalid').addClass('is-valid');
            $(element).removeClass('is-invalid').addClass('is-valid');
        },
        errorElement: 'div',
        errorPlacement: function (error, element) {
            error.appendTo($(element).closest('.form-group').find('.invalid-feedback'));
            $(element).closest('.form-group').find('.invalid-feedback').addClass('d-block');
        }
    });
</script>
<!-- end:jq validation setup script-->
