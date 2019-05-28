@extends('_layout.layout')

@section('head_title', __("Roles: Edit"))

@section('content')
    @include('_layout.partials.breadcrumbs', [
        'pageTitle' => __('Roles: Edit'),
        'breadcrumbs' => [
            url('/') => __('Home'),
            route('acl.groups.list') => __('Roles'),
        ]
    ])
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary card-header-icon">
                    <div class="d-flex justify-content-end">
                        <h4 class="card-title"></h4>
                        <!-- begin:title-toolbar -->
                        <button type="reset" form="examples-form" class="btn btn-danger waves-effect m-l-5">
                            <i class="mdi mdi-autorenew"></i>
                            @lang('Reset')
                        </button>
                        &nbsp;
                        <a href="@route('acl.groups.list')" class="btn btn-primary btn-round">
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
                        <div class="col-lg-3">
                        </div>
                        <div class="col-lg-6">
                            @include('acl.groups.partials.form')
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
