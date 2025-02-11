<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Welcome')</title>
    <link rel="stylesheet" href="{{ asset('public/css/style.css') }}">
    <script src="{{ asset('public/js/jquery.min.js') }}"></script>
    <script src="{{ asset('public/js/ajax-jquery.min.js') }}"></script>
    <link href="{{ asset('public/css/bootstrap.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('public/css/font-awesome.css') }}" rel="stylesheet"/>
    <link href="{{ asset('public/css/coreui.min.css') }}" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet"/>
</head>

<body class="login-bg">
<div>
    <div class="login-cont">
        <div class="col-md-8">
            <div class="card-group">
                <div class="card p-4 border-0">
                    <div class="card-body">
                        <div class="head-logo">
                            <img src="{{ asset('public/images/azania-logo.png') }}" class="client-logo" alt="MHB Logo">
                        </div>
                        <div class="pt-2 ">
                            @if ($errors->any())
                                @foreach ($errors->all() as $error)
                                    <div class="alert alert-danger text-center">
                                        {{ $error }}
                                    </div>
                                @endforeach
                            @else
                                @if(Session::has('color'))
                                    <div
                                        class="alert alert-{{ Session::get('color') }} alert-dismissible fade show text-center"
                                        role="alert">
                                        {{ Session::get('notification') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                            @endif
                        </div>

                        @yield('content')

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('public/js/custom.js') }}"></script>
</body>
</html>
