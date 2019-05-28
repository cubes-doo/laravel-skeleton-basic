<!-- begin:form -->
<form id="permissions-form" method="POST" class="form-horizontal" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="form-group row">
        <label class="col-md-2 control-label">
            @lang('Model')
            <span class="text-danger">*</span>
        </label>
        <div class="col-md-10">
            <input type="text" name="model" value="{{old('model', optional(explode(':', $entity->slug))[0])}}" class="form-control" placeholder="@lang('Enter a Model')" autofocus maxlength="100">
            <p class="text-muted font-13">
                entity to which the action is applied
            </p>
            @formError(['field' => 'model'])
            @endformError
        </div>
    </div>
    {{-- <div class="checkbox checkbox-primary mb-2">
        <input id="checkbox2" type="checkbox" checked="">
        <label for="checkbox2">
            Use default actions (creates CRUD model permissions, i.e. 4 in total)
        </label>
    </div> --}}
    <div class="form-group row">
        <label class="col-md-2 control-label">
            @lang('Action')
            <span class="text-danger">*</span>
        </label>
        <div class="col-md-10">
            <input type="text" name="action" value="{{old('action', optional(explode(':', $entity->slug))[1])}}" class="form-control" placeholder="@lang('Enter a Action')" autofocus maxlength="100">
            <p class="text-muted font-13">
                name of the action that can be applied to the above entity
            </p>
            @formError(['field' => 'action'])
            @endformError
        </div>
    </div>
    <div class="form-group row">
        <label class="col-md-2 control-label">
            @lang('Name')
            <span class="text-danger">*</span>
        </label>
        <div class="col-md-10">
            <input type="text" name="name" value="{{old('name', $entity->name)}}" class="form-control" placeholder="@lang('Enter a Name')" autofocus maxlength="100">
            <p class="text-muted font-13">
                make it human readable, since it will be a label for this permission
            </p>
            @formError(['field' => 'name'])
            @endformError
        </div>
    </div>
    <div class="form-group row">
        <label class="col-md-2 control-label">
            @lang('Description')
        </label>
        <div class="col-md-10">
            <textarea name="description" placeholder="@lang('Enter a description')" class="form-control" rows="5" maxlength="655">{{old('description', $entity->description)}}</textarea>
            @formError(['field' => 'description'])
            @endformError
        </div>
    </div>
    <div class="form-group text-right m-b-0">
        <button class="btn btn-primary waves-effect waves-light" type="submit">
            @lang('Submit')
        </button>
        <a href="@route('acl.permissions.list')" class="btn btn-secondary waves-effect m-l-5">
            @lang('Cancel')
        </a>
    </div>
</form>
<!-- end:form -->
@push('footer_scripts')
    <!-- begin:page script -->
    <script src="{{asset('/theme/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js')}}"></script>
    <script type="text/javascript">
        $('#permissions-form textarea').maxlength({
            threshold: 655,
            placement: 'right'
        });
        $('#permissions-form :text').maxlength({
            threshold: 100,
            placement: 'right'
        });

        $("#permissions-form").validate({
            rules: {
                name: {
                    required: true,
                    rangelength: [3, 100]
                },
                model: {
                    required: true,
                    rangelength: [3, 100]
                },
                action: {
                    required: true,
                    rangelength: [3, 100]
                },
                description: {
                    required: false,
                    rangelength: [10, 655]
                }
            }
        });
    </script>
    <!-- begin:page script -->
@endpush