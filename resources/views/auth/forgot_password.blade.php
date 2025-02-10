<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>MHB|Change Password</title>
    <link rel="stylesheet" href="{{asset('public/css/style.css')}}">
    <script src="{{asset('public/js/jquery.min.js')}}"></script>
    <script src="{{asset('public/js/ajax-jquery.min.js')}}"></script>
    <link href="{{asset('public/css/bootstrap.min.css')}}" rel="stylesheet"/>
    <link href="{{asset('public/css/font-awesome.css')}}" rel="stylesheet"/>
    <link href="{{asset('public/css/coreui.min.css')}}" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet"/>
</head>

<body
    style="background-image: url('{{ asset('public/images/mhb_background.jpeg') }}'); background-size: cover; width: 100%; height: 100vh;">
<div>
    <div class="container"
         style="position: absolute; top: 200px; left: 50%; transform: translateX(-50%); width: 90%; max-width: 1000px;">
        <!-- Row for two columns -->
        <div class="row">
            <!-- First Container -->
            <div class="col-md-12 mb-4">
                <div class="card p-4 border-0">
                    <div class="card-body">
                        <h4 class="text-center">Recover Password</h4>
                        <div class="pt-2 mt-4">
                            <!-- Notifications-->
                            @if(Session::has('color'))
                                <div class="alert alert-{{ Session::get('color') }} alert-dismissible fade show"
                                     role="alert">
                                    {{ Session::get('notification') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('send-link')}}">
                            {{ csrf_field() }}
                            <!-- New Password Field -->
                            <div class="form-group mb-4">
                                <label for="new_password">Email:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                    </div>
                                    <input name="email" type="email" class="form-control" placeholder="email"
                                           required>
                                </div>
                            </div>
                            <!-- Submit Button -->
                            <div class="row">
                                <div class="col-12 text-center">
                                    <input type="submit" class="btn btn-primary px-4" value="Send Recovery Email">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Fade out the alert after 4 seconds (4000 milliseconds)
        $('.alert').delay(4000).fadeOut(500);
    });
</script>
</body>
</html>
