@extends('layouts.master_auth')

@section('title', 'Admin Portal: Sign In')

@section('content')
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
                  style="cursor: pointer; margin-left:-1.5em; position:relative; margin-top:0.7em;"></span>
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
            <div class="col-6">
                <input type="submit" class="btn btn-primary px-4"
                       value='{{ trans('global.login') }}'>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12 text-center">
                <p>Forgot Password? <a href="{{ route('forgot-password') }}">Recover Here</a></p>
            </div>
        </div>
    </form>
@endsection
