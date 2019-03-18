@extends('_layout.layout')

@section('head_title', __("Dashboard"))

@section('content')
@include('_layout.partials.breadcrumbs', [
    'pageTitle' => __("Dashboard"),
    'breadcrumbs' => [
        url('/') => __("Home"),
    ]
])
@endsection