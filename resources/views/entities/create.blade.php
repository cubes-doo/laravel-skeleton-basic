@extends('_layout.layout')

@section('head_title', __("Entities: Create"))

@section('content')
    @include('_layout.partials.breadcrumbs', [
        'pageTitle' => __('Entities: Create'),
        'breadcrumbs' => [
            url('/') => __('Home'),
            route('entities.list') => __('Entities'),
        ]
    ])
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary card-header-icon">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title"></h4>
                    <!-- begin:title-toolbar -->
                    <a href="@route('entities.list')" class="btn btn-primary btn-round">
                        <span class="btn-label">
                            <i class="mdi mdi-keyboard-backspace"></i>
                        </span>
                        @lang('Back')
                    </a>
                    <!-- end:title-toolbar  -->
                </div>
            </div>
            <div class="card-body">
                <div class="tabs-vertical-env tabs-vertical-env-right">
                    <div class="tab-content">
                        <div class="tab-pane show active" id="simple-form" role="tabpanel" aria-labelledby="simple-form-tab">
                            @include('entities.partials.form')
                        </div>
                        <div class="tab-pane" id="extended-form" role="tabpanel" aria-labelledby="extended-form-tab">
                            @include('entities.partials.form_extended')
                        </div>
                    </div>
                    <ul class="nav nav-tabs flex-column tabs-vertical" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="simple-form-tab" data-toggle="tab" href="#simple-form" role="tab" aria-controls="simple-form" aria-selected="true">Simple Form</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="extended-form-tab" data-toggle="tab" href="#extended-form" role="tab" aria-controls="extended-form" aria-selected="false">Extended Form</a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- end content-->
        </div>
        <!--  end card  -->
        </div>
        <!-- end col-md-12 -->
    </div>
@endsection