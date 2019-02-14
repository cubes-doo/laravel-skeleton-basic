@extends('auth._layout.layout')

@section('title', __('Reset Password Page'))

@section('content')
    <form class="form" method="POST" action="@route('password.email')">
        {{ csrf_field() }}
        <div class="card card-login card-hidden">
            <div class="card-header card-header-rose text-center">
                <h4 class="card-title">@lang('Reset Password')</h4>
            </div>
            <div class="card-body ">
                <span class="bmd-form-group @errors('email', 'has-danger')">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="material-icons">email</i>
                            </span>
                        </div>
                        <input type="email" name="email" class="form-control" placeholder="@lang('Email...')">
                    </div>
                    @component('_layout.partials.form.error', ['field' => 'email'])
                            
                    @endcomponent
                </span>
            </div>
            <div class="card-footer justify-content-center">
                <button class="btn btn-rose btn-link btn-lg" type="submit">@lang('Send Password Reset Link')</button>
            </div>
        </div>
    </form>
@endsection
