@extends('auth._layout.layout')

@section('head_title', __('Login'))

@section('content')

    <form class="form-horizontal" method="post" action="@route('login')">
            @csrf       
            <div class="form-group m-b-25">
                <div class="col-12">
                    <label for="emailaddress">@lang('Email')</label>
                    <input name="email" value="{{old('email')}}"class="form-control input-lg @errorClass('email', 'is-invalid')" type="email" id="emailaddress" required="" placeholder="mailbox@example.com" tabindex="1" autofocus>
                    @include('_layout.partials.form.error', ['field' => 'email'])
                </div>
            </div>

            <div class="form-group m-b-25">
                <div class="col-12">
                    <a href="@route('password.request')" class="text-muted float-right" tabindex="3">@lang('Forgot your password?')</a>
                    <label for="password">@lang('Password')</label>
                    <input name="password" class="form-control input-lg @errorClass('password', 'is-invalid')" type="password" required="" id="password" placeholder="@lang('Enter your password')" tabindex="2">
                    @include('_layout.partials.form.error', ['field' => 'password'])
                </div>
            </div>

            <div class="form-group m-b-20">
                <div class="col-12">
                    <div class="checkbox checkbox-custom">
                        <label>
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                            @lang('Remember me')
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group account-btn text-center m-t-10">
                <div class="col-12">
                    <button class="btn w-lg btn-rounded btn-lg btn-primary waves-effect waves-light" type="submit">@lang('Log In')</button>
                </div>
            </div>

        </form>

        <div class="clearfix"></div>

@endsection
