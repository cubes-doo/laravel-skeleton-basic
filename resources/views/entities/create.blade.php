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
                @include('entities.partials.form')
            </div>
            <!-- end content-->
        </div>
        <!--  end card  -->
        </div>
        <!-- end col-md-12 -->
    </div>
@endsection