@extends('auth._layout.layout')

@section('head_title', __('Reset Password'))

@section('content')
@if (session('status'))
<div class="text-center m-b-20">
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
</div>
@else
<div class="text-center m-b-20">
    <p class="text-muted m-b-0">@lang('Enter your email address and we\'ll send you an email with instructions to reset your password.')</p>
</div>
<form class="form-horizontal" method="post" action="@route('password.email')">
    @csrf
    <div class="form-group">
        <div class="col-12">
            <label for="emailaddress">@lang('Email address')</label>
            <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>
            @include('_layout.partials.form.error', ['field' => 'email'])
        </div>
    </div>

    <div class="form-group text-center m-t-10">
        <div class="col-12">
            <button class="btn w-lg btn-rounded btn-lg btn-primary waves-effect waves-light" type="submit">
                @lang('Send Password Reset Link')
            </button>
        </div>
    </div>

</form>

<div class="clearfix"></div>

@endif

<div class="row">
    <div class="col-12 text-center">
        <a class="text-muted" href="@route('login')">@lang('Return to Login')</a>
    </div>
</div>
@endsection
