@extends('auth._layout.layout')

@section('title', __('Login'))

@section('content')
    <form class="form" method="POST" action="{{ route('login') }}">
        {{ csrf_field() }}
        <div class="card card-login card-hidden">
            <div class="card-header card-header-rose text-center">
                <h4 class="card-title">@lang('Login')</h4>
                <div class="social-line">
                    <a href="javascript:;" class="btn btn-just-icon btn-link btn-white">
                        <i class="fa fa-facebook-square"></i>
                    </a>
                    <a href="javascript:;" class="btn btn-just-icon btn-link btn-white">
                        <i class="fa fa-twitter"></i>
                    </a>
                    <a href="javascript:;" class="btn btn-just-icon btn-link btn-white">
                        <i class="fa fa-google-plus"></i>
                    </a>
                </div>
            </div>
            <div class="card-body ">
                <p class="card-description text-center">@lang('Or Be Classical')</p>
                <span class="bmd-form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="material-icons">email</i>
                            </span>
                        </div>
                        <input type="email" class="form-control" placeholder="@lang('Email...')">
                    </div>
                </span>
                <span class="bmd-form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="material-icons">lock_outline</i>
                            </span>
                        </div>
                        <input type="password" class="form-control" placeholder="@lang('Password...')">
                    </div>
                </span>
            </div>
            <div class="card-footer justify-content-center">
                <a class="btn btn-info btn-link btn-sm" href="@route('password.request')">@lang('Forgot Password?')</a>
                <button class="btn btn-rose btn-link btn-lg" type="submit">@lang('Lets Go')</button>
            </div>
        </div>
    </form>
@endsection
