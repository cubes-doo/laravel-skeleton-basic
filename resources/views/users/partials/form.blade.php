<!-- begin:form -->
<form id="users-form" method="POST" class="form-horizontal" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="form-group row">
        <label class="col-md-2 control-label">
            @lang('First Name')
            <span class="text-danger">*</span>
        </label>
        <div class="col-md-10">
            <input type="text" name="first_name" value="{{old('first_name', $entity->first_name)}}" class="form-control @errorClass('first_name', 'is-invalid')" placeholder="@lang('Enter a First Name')" autofocus maxlength="100">
            @formError(['field' => 'first_name'])
            @endformError
        </div>
    </div>
    <div class="form-group row">
        <label class="col-md-2 control-label">
            @lang('Last Name')
            <span class="text-danger">*</span>
        </label>
        <div class="col-md-10">
            <input type="text" name="last_name" value="{{old('last_name', $entity->last_name)}}" class="form-control @errorClass('last_name', 'is-invalid')" placeholder="@lang('Enter a Last Name')" maxlength="100">
            @formError(['field' => 'last_name'])
            @endformError
        </div>
    </div>
    <div class="form-group row">
        <label class="col-md-2 control-label">
            @lang('Email')
            <span class="text-danger">*</span>
        </label>
        <div class="col-md-10">
            <input type="text" name="email" value="{{old('email', $entity->email)}}" class="form-control @errorClass('email', 'is-invalid')" placeholder="@lang('Enter a Email')" @if($entity->id === auth()->user()->id) disabled @endif>
            @formError(['field' => 'email'])
            @endformError
        </div>
    </div>
    <div class="form-group text-right m-b-0">
        <button class="btn btn-primary waves-effect waves-light" type="submit">
            @lang('Submit')
        </button>
        <a href="@route('users.list')" class="btn btn-secondary waves-effect m-l-5">
            @lang('Cancel')
        </a>
    </div>
</form>
<!-- end:form -->
@push('footer_scripts')
    <!-- begin:page script -->
    <script src="{{asset('/theme/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js')}}"></script>
    <script type="text/javascript">
        $('#users-form [maxlength]').maxlength({
            threshold: 20,
            placement: 'right'
        });

        $("#users-form").validate({
            rules: {
                first_name: {
                    required: true,
                    rangelength: [2, 100]
                },
                last_name: {
                    required: true,
                    rangelength: [2, 100]
                },
                email: {
                    required: true,
                    email: true
                },
            }
        });
    </script>
    <!-- begin:page script -->
@endpush