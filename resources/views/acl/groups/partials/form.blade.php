@push('head_scripts')
    <link href="{{asset('/theme/plugins/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
@endpush
<!-- begin:form -->
<form id="permissions-form" method="POST" class="form-horizontal" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="form-group row">
        <label class="col-md-2 control-label">
            @lang('Name')
            <span class="text-danger">*</span>
        </label>
        <div class="col-md-10">
            <input type="text" name="name" value="{{old('name', $entity->name)}}" class="form-control" placeholder="@lang('Enter a Name')" autofocus maxlength="100">
            @formError(['field' => 'name'])
            @endformError
        </div>
    </div>
    <div class="form-group row">
        <label class="col-md-2 control-label">
            @lang('Permissions')
            <span class="text-danger">*</span>
        </label>
        <div class="col-md-10">
            <select name="permissions[]" multiple class="form-control select2">
                @unless (empty($permissions))
                    @foreach ($permissions as $permission)
                        <option value="{{$permission['id']}}" selected>{{$permission['text']}}</option>
                    @endforeach
                @endunless
            </select>
            @formError(['field' => 'permissions'])
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
        <a href="@route('acl.groups.list')" class="btn btn-secondary waves-effect m-l-5">
            @lang('Cancel')
        </a>
    </div>
</form>
<!-- end:form -->
@push('footer_scripts')
    <!-- begin:page script -->
    <script src="{{asset('/theme/plugins/select2/js/select2.min.js')}}"></script>
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

        $('[name*="permissions"]').select2({
            placeholder: "@lang('--Assign permissions --')",
            ajax: {
                type: 'POST',
                url: '@route(acl.permissions.selection)',
                dataType: 'json',
                delay: 1000
            }
        });

        $("#permissions-form").validate({
            rules: {
                name: {
                    required: true,
                    rangelength: [3, 100]
                },
                permissions: {
                    required: true
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