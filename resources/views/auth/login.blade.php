<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Welcome</title>
    <link rel="stylesheet" href="{{asset('public/css/style.css')}}">
    <script src="{{asset('public/js/jquery.min.js')}}"></script>
    <script src="{{asset('public/js/ajax-jquery.min.js')}}"></script>
    <link href="{{asset('public/css/bootstrap.min.css')}}" rel="stylesheet"/>
    <link href="{{asset('public/css/font-awesome.css')}}" rel="stylesheet"/>
    <link href="{{asset('public/css/coreui.min.css')}}" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet"/>
</head>

<body style="
	background-image: url('{{ asset('public/images/mhb_background.jpeg') }}');
    background-size: cover;
    width: 100%;
height: 100vh">
<div>
    <div style="
    position: absolute;
    top: 200px;
    left: 33%;
    width: 45rem;
    height: 35rem;
    overflow: hidden;">
        <div class="col-md-8">
            <div class="card-group">
                <div class="card p-4 border-0">
                    <div class="card-body">
                        <div class="head-logo">
                            <img src="{{asset('public/images/mhb-logo.png')}}" class="client-logo" alt="MHB Logo">
                        </div>
                        <div class="pt-2">
                            @if ($errors->any())
                                @foreach ($errors->all() as $error)
                                    <div class="alert alert-danger text-center">
                                        {{ $error}}
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
                                    {{ trans('global.site_title') }}
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
                                    <p>Forgot Password? <a href="{{route('forgot-password')}}">Recover Here</a></p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('#reload').click(function () {
        $.ajax({
            type: 'GET',
            url: 'reload-captcha',
            success: function (data) {
                $(".captcha span").html(data.captcha);
            }
        });
    });
    $("body").on('click', '.toggle-password', function () {
        $(this).toggleClass("fa-eye fa-eye-slash");

        var input = $("#password").attr("type");
        if (input === "password") {
            $("#password").attr("type", "text");
        } else {
            $("#password").attr("type", "password");
        }
    });

    $(document).ready(function () {
        // Fade out the alert after 4 seconds (4000 milliseconds)
        $('.alert').delay(4000).fadeOut(500);
    });

</script>

</script>

</body>
</html>
