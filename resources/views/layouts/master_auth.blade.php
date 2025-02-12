<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Azania: Admin Portal')</title>
    <link rel="stylesheet" href="{{ asset('public/css/style.css') }}">
    <script src="{{ asset('public/js/jquery.min.js') }}"></script>
    <script src="{{ asset('public/js/ajax-jquery.min.js') }}"></script>
    <link href="{{ asset('public/css/bootstrap.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('public/css/font-awesome.css') }}" rel="stylesheet"/>
    <link href="{{ asset('public/css/coreui.min.css') }}" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet"/>
</head>

<body class="auth-body">
@yield('content')

<script src="{{ asset('public/js/custom.js') }}"></script>
</body>
</html>
