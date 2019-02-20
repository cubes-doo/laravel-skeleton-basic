@extends('_layout.layout')

@section('title', __("Entities: Create"))

@section('content')
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary card-header-icon">
                <div class="card-icon">
                    <i class="material-icons">waves</i>
                </div>
                <div class="d-flex justify-content-between">
                    <h4 class="card-title">@lang('Entities: Create')</h4>
                    <!-- begin:title-toolbar -->
                    <a href="@route('entities.list')" class="btn btn-primary btn-round">
                        <span class="btn-label">
                            <i class="material-icons">arrow_back</i>
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