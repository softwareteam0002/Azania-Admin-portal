<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>MHB|Recover Password</title>
    <link rel="stylesheet" href="{{asset('public/css/style.css')}}">
    <script src="{{asset('public/js/jquery.min.js')}}"></script>
    <script src="{{asset('public/js/ajax-jquery.min.js')}}"></script>
    <link href="{{asset('public/css/bootstrap.min.css')}}" rel="stylesheet"/>
    <link href="{{asset('public/css/font-awesome.css')}}" rel="stylesheet"/>
    <link href="{{asset('public/css/coreui.min.css')}}" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet"/>
</head>

<body class="login-bg">
<div>
    <div class="container set-cont">
        <!-- Row for two columns -->
        <div class="row">
            <!-- Second Container (You can customize this container as needed) -->
            <div class="col-md-4 mb-4">
                <div class="card p-4 border-0">
                    <div class="card-body">
                        <h4 class="text-center">Password Policy</h4>
                        <hr>
                        <ul class="mb-0 list-unstyled">
                            @if($policy->complexity == 'strong')
                                <h4 class="text-center text-black-50">Strong</h4>
                            @endif
                            <hr>
                            @if($policy->complexity == 'strong')
                                <ul class="list-unstyled ">
                                    <li><span class="text text-danger">* Mandatory rules</span></li>
                                    <li><span class="badge badge-success">✓</span> {{ $policy->min_length }}
                                        Characters (Minimum)
                                    </li>
                                    <li><span class="badge badge-success">✓</span> Letters</li>
                                    <li><span class="badge badge-success">✓</span> Numbers</li>
                                    <li><span class="badge badge-success">✓</span> Symbols/Characters</li>
                                </ul>
                            @elseif($policy->complexity == 'medium')
                                <ul class="list-unstyled">
                                    <li><span class="text text-danger">* Contains at least one</span></li>
                                    <li><span class="badge badge-success">✓</span> {{ $policy->min_length }}
                                        Characters (Minimum)
                                    </li>
                                    <li><span class="badge badge-success">✓</span> Letters</li>
                                    <li><span class="badge badge-success">✓</span> Numbers</li>
                                    <li><span class="badge badge-success">✓</span> Symbols/Characters</li>
                                </ul>
                            @else
                                <ul class="list-unstyled">
                                    <li><span class="text text-danger">* Optional rules</span></li>
                                    <li><span class="badge badge-success">✓</span> {{ $policy->min_length }}
                                        Characters (Minimum)
                                    </li>
                                    <li><span class="badge badge-success">✓</span> Letters</li>
                                    <li><span class="badge badge-success">✓</span> Numbers</li>
                                    <li><span class="badge badge-success">✓</span> Symbols/Characters</li>
                                </ul>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <!-- First Container -->
            <div class="col-md-8 mb-4">
                <div class="card p-4 border-0">
                    <div class="card-body">
                        <div class="pt-2">
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
                        <form method="POST" action="{{ route('recover-update-password') }}">
                            {{ csrf_field() }}
                            <!-- New Password Field -->
                            <input type="hidden" name="token" value="{{ $token ?? Session::get('recover_token') }}">
                            <div class="form-group mb-4">
                                <label for="new_password">New Password:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                    </div>
                                    <input name="password" type="password" id="new_password" class="form-control"
                                           placeholder="{{ trans('global.login_password') }}" required
                                           autocomplete="off">
                                    <span toggle="#new_password" class="fa fa-fw fa-eye field_icon toggle-password"
                                          style="cursor: pointer; margin-left:-1.5em; position:relative; margin-top:0.7em;"></span>
                                </div>
                            </div>

                            <!-- Confirm Password Field -->
                            <div class="form-group mb-4">
                                <label for="confirm_password">Confirm Password:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                    </div>
                                    <input name="confirm_password" type="password" id="confirm_password"
                                           class="form-control"
                                           placeholder="{{ trans('global.login_password') }}" required
                                           autocomplete="off">
                                    <span toggle="#confirm_password" class="fa fa-fw fa-eye field_icon toggle-password"
                                          style="cursor: pointer; margin-left:-1.5em; position:relative; margin-top:0.7em;"></span>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="row">
                                <div class="col-6">
                                    <input type="submit" class="btn btn-primary px-4" value="Change Password">
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

    document.querySelectorAll('.toggle-password').forEach(item => {
        item.addEventListener('click', function () {
            const passwordField = document.querySelector(this.getAttribute('toggle'));
            const icon = this;

            // Toggle password field visibility
            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
</script>
</body>
</html>
