<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ trans('global.site_title') }}</title>
    <link href="{{asset('public/css/bootstrap.min.css')}}" rel="stylesheet"/>
    <link href="{{ asset('public/css/main_styles.css') }}" rel="stylesheet">
    <link href="{{ asset('public/css/font-awesome.css') }}" rel="stylesheet"/>
    <link href="{{ asset('public/css/dataTables.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('public/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('public/css/buttons.dataTables.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('public/css/select.dataTables.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('public/css/coreui.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('public/css/all.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('public/css/select2.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('public/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('public/css/dropzone.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('public/css/style.css') }}" rel="stylesheet"/>
    <link href="{{ asset('public/css/custom.css') }}" rel="stylesheet"/>
    <!-- Bootstrap core CSS-->
    <link href="{{ asset('public/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Custom fonts for this template-->
    <link href="{{ asset('public/vendor/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Page level plugin CSS-->
    <link href="{{ asset('public/vendor/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet">
    <script src="{{ asset('public/js/perfect-scrollbar.min.js') }}"></script>

    @yield('styles')
    <style media="print">
        @page {
            size: auto;
            margin: 0;
        }

        body {
            margin: 0px;
        }

        .modal-backdrop {
            opacity: 0.5 !important;
        }

    </style>
</head>

<body class="app header-fixed sidebar-fixed aside-menu-fixed pace-done sidebar-lg-show">
<header class="app-header navbar">
    <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
        <span class="navbar-toggler-icon">

        </span>
    </button>

    <a class="navbar-brand" href="#">

        <span class="navbar-brand-full"> <img src="{{ asset('public/images/azania-logo.png') }}" width="160px"
                                              alt="Azania Logo"></span>
        <span class="navbar-brand-minimized">MHB</span>
    </a>
    <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" data-toggle="sidebar-lg-show">
        <span class="navbar-toggler-icon"></span>
    </button>

    <ul class="nav navbar-nav ml-auto">
        @if(count(config('panel.available_languages', [])) > 1)
            <li class="nav-item dropdown d-md-down-none">
                <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                   aria-expanded="false">
                    {{ strtoupper(app()->getLocale()) }}
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    @foreach(config('panel.available_languages') as $langLocale => $langName)
                        <a class="dropdown-item"
                           href="{{ url()->current() }}?change_language={{ $langLocale }}">{{ strtoupper($langLocale) }}
                            ({{ $langName }})</a>
                    @endforeach
                </div>
            </li>
        @endif
    </ul>
    <div class="dropdown ml-3">
        <a class="dropdown-toggle" type="button" id="userDropdown" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            {{ auth()->user()->name }}
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
            <a class="dropdown-item" href="{{ url('admin/change-password') }}">Change Password</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">Logout</a>
        </div>
    </div>
</header>

<div class="app-body">
    @include('partials.menu')
    <main class="main">
        <div style="padding-top: 20px" class="container-fluid">
            @yield('content')
        </div>
    </main>
    <form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
    </form>
</div>

<script>
    $(document).ready(function () {
        // Fade out the alert after 4 seconds (4000 milliseconds) smoothly over 1 second (1000 milliseconds)
        $('.alert').delay(4000).fadeOut(1000);
    });
    var coordinate = document.getElementById("coordinateMessage");
    var latitude = document.getElementById("latitude");
    var longitude = document.getElementById("longitude");

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            coordinate.innerHTML = "Geolocation is not supported by this browser.";
        }
    }

    function showPosition(position) {
        $('#latitude').val(position.coords.latitude);
        $('#longitude').val(position.coords.longitude);
    }
</script>
<script src="{{asset('public/js/jquery.min.js')}}"></script>
<script src="{{asset('public/js/bootstrap.min.js')}}"></script>
<script src="{{asset('public/js/popper.min.js')}}"></script>
<script src="{{asset('public/js/coreui.min.js')}}"></script>
<script src="{{asset('public/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('public/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('public/js/buttons.print.min.js')}}"></script>
<script src="{{asset('public/js/buttons.colVis.min.js')}}"></script>
<script src="{{asset('public/js/pdfmake.min.js')}}"></script>
<script src="{{asset('public/js/vfs_fonts.js')}}"></script>
<script src="{{asset('public/js/dataTables.select.min.js')}}"></script>
<script src="{{asset('public/js/ckeditor.js')}}"></script>
<script src="{{asset('public/js/moment.min.js')}}"></script>
<script src="{{asset('public/js/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{asset('public/js/select2.full.min.js')}}"></script>
<script src="{{asset('public/js/dropzone.min.js')}}"></script>
<script src="{{ asset('public/js/main.js') }}"></script>
<script src="{{ asset('public/js/app.js') }}"></script>
<script src="{{ asset('public/js/jquery.min.js') }}"></script>
<script src="{{ asset('public/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('public/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('public/js/dataTables.bootstrap4.min.js') }}"></script>
@yield('scripts')

</body>

</html>
