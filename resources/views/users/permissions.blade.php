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
                                        <select name="group" class="form-control select2" @unless ($permissions->isEmpty()) disabled @endunless>
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
                                        <input name="custom-permissions" type="checkbox" value="1" @unless ($permissions->isEmpty()) checked @endunless data-plugin="switchery" data-color="#1bb99a"/>
                                    </div>
                                </div>
                                <div class="form-group row custom-permissions" @if($permissions->isEmpty()) style="display: none" @endif>
                                    <label class="col-md-2 control-label">
                                        @lang('Permissions')
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-10">
                                        <select name="permissions[]" multiple class="form-control select2" @unless (empty($group)) disabled @endunless>
                                            @unless ($permissions->isEmpty())
                                                @foreach ($permissions as $permission)
                                                    <option value="{{$permission['id']}}" selected>{{$permission['text']}}</option>
                                                @endforeach
                                            @endunless
                                        </select>
                                        @formError(['field' => 'permissions'])
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
                url: '@route(acl.groups.selection)',
                dataType: 'json',
                delay: 1000
            }
        });

        $('[name="custom-permissions"]').change(function(e){
            if($(this).prop('checked')) {
                $('.custom-permissions').slideDown();
                $('[name=group]').prop('disabled', true);
                $('[name="permissions[]"]').prop('disabled', false);
            } else {
                $('.custom-permissions').slideUp();
                $('[name="permissions[]"]').prop('disabled', true);
                $('[name=group]').prop('disabled', false);
            }
        });

        $('[name="permissions[]"]').select2({
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
    </script>
    <!-- begin:page script -->
@endpush