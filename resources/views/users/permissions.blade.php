@extends('_layout.layout')

@section('head_title', __("Users: Permissions"))

@push('head_scripts')
    <link href="{{asset('/theme/plugins/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    @include('_layout.partials.breadcrumbs', [
        'pageTitle' => __('Users: Permissions'),
        'breadcrumbs' => [
            url('/') => __('Home'),
            route('entities.list') => __('Users'),
        ]
    ])
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary card-header-icon">
                    <div class="d-flex justify-content-end">
                        <h4 class="card-title"></h4>
                        <!-- begin:title-toolbar -->
                        <button type="reset" form="users-form" class="btn btn-danger waves-effect m-l-5">
                            <i class="mdi mdi-autorenew"></i>
                            @lang('Reset')
                        </button>
                        &nbsp;
                        <a href="@route('users.list')" class="btn btn-primary btn-round">
                            <span class="btn-label">
                                <i class="mdi mdi-keyboard-backspace"></i>
                            </span>
                            @lang('Back')
                        </a>
                        <!-- end:title-toolbar  -->
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-1">
                        </div>
                        <div class="col-10">
                            <form id="permissions-form" method="POST" class="form-horizontal" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="form-group row">
                                    <label class="col-md-2 control-label">
                                        @lang('Role')
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-10">
                                        <select name="group" class="form-control select2" @unless (empty($usedPermissions)) disabled @endunless>
                                            @unless (empty($group))
                                                <option value="{{$group['id']}}" selected>{{$group['text']}}</option>
                                            @endunless
                                        </select>
                                        @formError(['field' => 'group'])
                                        @endformError
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2 control-label">
                                        @lang('Customize permissions')
                                    </label>
                                    <div class="col-md-10">
                                        <input name="custom-permissions" type="checkbox" value="1" @unless (empty($usedPermissions)) checked @endunless data-plugin="switchery" data-color="#1bb99a"/>
                                    </div>
                                </div>
                                <div class="form-group row custom-permissions" @if(empty($usedPermissions)) style="display: none" @endif>
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
                                <div class="form-group text-right m-b-0">
                                    <button class="btn btn-primary waves-effect waves-light" type="submit">
                                        @lang('Submit')
                                    </button>
                                    <a href="@route('acl.groups.list')" class="btn btn-secondary waves-effect m-l-5">
                                        @lang('Cancel')
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- end content-->
            </div>
        <!--  end card  -->
        </div>
        <!-- end col-md-12 -->
    </div>
@endsection

@push('footer_scripts')
    <!-- begin:page script -->
    <script src="{{asset('/theme/plugins/select2/js/select2.min.js')}}"></script>
    <script type="text/javascript">
        $('[name*="group"]').select2({
            placeholder: "@lang('--Assign role --')",
            ajax: {
                type: 'POST',
                url: '@route("acl.groups.selection")',
                dataType: 'json',
                delay: 1000
            }
        });

        $('[name="custom-permissions"]').change(function(e){
            if($(this).prop('checked')) {
                $('.custom-permissions').slideDown();
                $('[name=group]').prop('disabled', true);
                $('[name*="permissions["]').prop('disabled', false);
            } else {
                $('.custom-permissions').slideUp();
                $('[name*="permissions["]').prop('disabled', true);
                $('[name=group]').prop('disabled', false);
            }
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

        $("#permissions-form").validate({
            rules: {
                permissions: {
                    required: function(element) {
                        return $('[name="group"]').val();
                    }
                },
                group: {
                    required: function(element) {
                        return $('[name="permissions[]"]').val()[0];
                    }
                }
            }
        });

        $('#permissions-form').submit(function(e) {
            $(`input[value="*"]`).remove();
        });
    </script>
    <!-- begin:page script -->
@endpush