@extends('auth._layout.layout')

@section('head_title', __('Reset Password Confirm'))

@section('content')
<form class="form-horizontal" method="post" action="@route('password.request')">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="form-group">
            <div class="col-12">
                <label for="emailaddress">@lang('Email address')</label>
                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>
                @include('_layout.partials.form.error', ['field' => 'email'])
            </div>
        </div>
    
        <div class="form-group">
            <div class="col-12">
                <label for="password">@lang('New Password')</label>
                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                @include('_layout.partials.form.error', ['field' => 'password'])
            </div>
        </div>
    
        <div class="form-group">
            <div class="col-12">
                <label for="password_confirmation">@lang('Confirm New Password')</label>
                <input id="password_confirmation" type="password" class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" name="password_confirmation" required>
                @include('_layout.partials.form.error', ['field' => 'password_confirmation'])
            </div>
        </div>
        
        <div class="form-group text-center m-t-10">
            <div class="col-12">
                <button class="btn w-lg btn-rounded btn-lg btn-primary waves-effect waves-light" type="submit">
                    @lang('Reset Password')
                </button>
            </div>
        </div>
    
    </form>
    
    <div class="clearfix"></div>
@endsection
