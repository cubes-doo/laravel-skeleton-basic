@extends('auth._layout.layout')

@section('title', __('Register Page'))

@section('content')
    <div class="card card-signup">
        <h2 class="card-title text-center">Register</h2>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 ml-auto">
                    <div class="social text-center">
                        <button class="btn btn-just-icon btn-round btn-twitter">
                            <i class="fa fa-twitter"></i>
                        </button>
                        <button class="btn btn-just-icon btn-round btn-dribbble">
                            <i class="fa fa-dribbble"></i>
                        </button>
                        <button class="btn btn-just-icon btn-round btn-facebook">
                            <i class="fa fa-facebook"> </i>
                        </button>
                        <h4 class="mt-3"> or be classical </h4>
                    </div>
                    <form class="form-horizontal" method="POST" action="{{ route('register') }}">
                        {{ csrf_field() }}

                        <div class="form-group bmd-form-group @errors('name', 'has-error')">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="material-icons">face</i>
                                    </span>
                                </div>
                                <input class="form-control" name="name" value="{{ old('name') }}" required autofocus placeholder="@lang('Name')">
                                @component('_layout.partials.form.error', ['field' => 'name'])
                                @endcomponent
                            </div>
                        </div>
                        
                        <div class="form-group bmd-form-group @errors('email', 'has-error')">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="material-icons">mail</i>
                                    </span>
                                </div>
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="@lang('E-Mail')" class="form-control" required>
                                @component('_layout.partials.form.error', ['field' => 'email'])
                                @endcomponent
                            </div>
                        </div>
                        <div class="form-group has-default bmd-form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="material-icons">lock_outline</i>
                                    </span>
                                </div>
                                <input type="password" class="form-control" name="password" placeholder="@lang('Password')" class="form-control" required>
                                @component('_layout.partials.form.error', ['field' => 'password'])
                                @endcomponent
                            </div>
                        </div>
                        <div class="form-group has-default bmd-form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="material-icons">lock_outline</i>
                                    </span>
                                </div>
                                <input type="password" placeholder="@lang('Confirm Password')" class="form-control">
                                @component('_layout.partials.form.error', ['field' => 'password'])
                                @endcomponent
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label"></label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Register
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
