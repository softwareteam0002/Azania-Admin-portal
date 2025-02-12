@extends('layouts.master_auth')

@section('title', 'Admin Portal: Sign In')

@section('content')
    <div class="login-container">
        <div class="card-group">
            <div class="card p-4 border-0">
                <div class="card-body">
                    <div class="pt-2 text-center text-nowrap">
                        @if ($errors->any())
                            @foreach ($errors->all() as $error)
                                <div class="alert alert-danger text-center">
                                    {{ $error }}
                                </div>
                            @endforeach
                        @else
                            @if(Session::has('color'))
                                <div class="alert alert-{{ Session::get('color') }} alert-dismissible fade show"
                                     role="alert">
                                    {{ Session::get('notification') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                        @endif
                    </div>
                    <form method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}
                        <h3>
                            <div class="text-muted login-logo text-center pt-2">
                                Sign In
                            </div>
                        </h3>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-user"></i></span>
                            </div>
                            <input name="email" type="text"
                                   class="form-control  @error('email') is-invalid @enderror"
                                   placeholder="{{ trans('global.login_email') }}" required autocomplete="off">
                        </div>
                        <div class="input-group mb-4">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-lock"></i></span>
                            </div>
                            <input name="password" type="password" id="password" class="form-control"
                                   placeholder="{{ trans('global.login_password') }}" required autocomplete="off">
                            <span toggle="#password-field" class="fa fa-fw fa-eye field_icon toggle-password"
                                  style="cursor: pointer;"></span>
                        </div>
                        <div class="form-group mt-4 mb-4">
                            <div class="captcha">
                                <span>{!! captcha_img() !!}</span>
                                <button type="button" class="btn btn-danger" class="reload" id="reload">
                                    &#x21bb;
                                </button>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <input id="captcha" type="text" class="form-control" placeholder="Enter Captcha"
                                   name="captcha" required autocomplete="off">
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <input type="submit" class="btn btn-primary login-btn"
                                       value='{{ trans('global.login') }}'>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <p>Forgot Password? <a href="{{ route('forgot-password') }}">Recover Here</a></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('public/js/custom.js') }}"></script>
@endsection
