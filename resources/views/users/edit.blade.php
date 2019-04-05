@extends('_layout.layout')

@section('head_title', __("Users: Edit"))

@section('content')
    @include('_layout.partials.breadcrumbs', [
        'pageTitle' => __('Users: Edit'),
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
                    <div class="row justify-content-center">
                        <div class="col-1">
                        </div>
                        <div class="col-10">
                            @include('users.partials.form')
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
