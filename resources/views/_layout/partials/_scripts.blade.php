<script>
    var resizefunc = [];
</script>

<!-- jQuery  -->
<script src="{{asset('/theme/assets/js/jquery.min.js')}}"></script>
<script src="{{asset('/theme/assets/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('/theme/assets/js/metisMenu.min.js')}}"></script>
<script src="{{asset('/theme/assets/js/waves.js')}}"></script>
<script src="{{asset('/theme/assets/js/jquery.slimscroll.js')}}"></script>
<script src="{{asset('/theme/plugins/bootstrap-select/js/bootstrap-select.min.js')}}"></script>
<script src="{{asset('/theme/plugins/sweet-alert2/sweetalert2.min.js')}}"></script>
<script src="{{asset('/theme/plugins/switchery/switchery.min.js')}}"></script>
<script src="{{asset('/theme/plugins/ckeditor5-build-classic/ckeditor.js')}}"></script>
<script src="{{asset('/theme/plugins/select2/js/select2.full.min.js')}}" type="text/javascript"></script>
<script src="{{asset('/theme/plugins/select2/js/i18n/' . app()->getLocale() . '.js')}}" type="text/javascript"></script>        

<!-- App js -->
<script src="{{asset('/theme/assets/js/jquery.core.js')}}"></script>
<script src="{{asset('/theme/assets/js/jquery.app.js')}}"></script>

<script src="{{asset('/custom/js/main.js')}}"></script>

@include('_layout.partials.ajax')
@include('_layout.partials.system_messages')
@include('_layout.partials.validate')

@stack('footer_scripts')

