@push('head_scripts')
    <link href="{{asset('/theme/plugins/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
@endpush
<!-- begin:form -->
<form id="groups-form" method="POST" class="form-horizontal" enctype="multipart/form-data">
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
        @unless (empty($permissions))
            <label class="col-md-2 control-label">
                @lang('Permissions')
                <span class="text-danger">*</span>
            </label>
            <div class="col-md-10">
                <div class="accordion" id="accordion-test-2">
                    @foreach ($permissions as $permissionGroup)
                        <div class="card mb-2">
                            <div class="card-heading">
                                <h4 class="card-title font-14">
                                    <a href="#" class="collapsed" data-toggle="collapse" data-target="#group-{{$permissionGroup['id']}}" @if($loop->first) aria-expanded="true" @endif aria-controls="group-{{$permissionGroup['id']}}">
                                        {{$permissionGroup['text']}}
                                    </a>
                                </h4>
                            </div>
                            <div id="group-{{$permissionGroup['id']}}" class="collapse @if($loop->first)show @endif" data-parent="#accordion-test-2">
                                <div class="card-body">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="permissions[{{$permissionGroup['id']}}][]" value="*">
                                            <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                                            *
                                        </label>
                                    </div>
                                    <div class="form-inline">
                                        @foreach ($permissionGroup['children'] as $permission)
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="permissions[{{$permissionGroup['id']}}][]" value="{{$permission['id']}}" @if (in_array($permission['id'], $usedPermissions)) checked @endif>
                                                    <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                                                    {{$permission['text']}}
                                                </label>
                                            </div>
                                            &nbsp;
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endunless
        @formError(['field' => 'permissions'])
        @endformError
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
        $('#groups-form textarea').maxlength({
            threshold: 655,
            placement: 'right'
        });

        $('#groups-form :text').maxlength({
            threshold: 100,
            placement: 'right'
        });

        $('input[name^=permissions]').change(function(e) {
            let $t = $(this);
            let thisPermissionGroup = $(`input[name="${$t.attr('name')}"]`);
            if($(this).val() == '*') {
                thisPermissionGroup.prop('checked', $t.prop('checked'));
            } else {
                let total = thisPermissionGroup.length;
                let checkedTotal = $(`input[name="${$t.attr('name')}"]:checked`).length;
                if(total != checkedTotal) {
                    $(`input[name="${$t.attr('name')}"][value="*"]`).prop('checked', false);
                }
            }
        });

        $("#groups-form").validate({
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

        $('#groups-form').submit(function(e) {
            $(`input[value="*"]`).remove();
        });
    </script>
    <!-- begin:page script -->
@endpush