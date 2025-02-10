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
    <style>
        .otp-container {
            display: flex !important;
            justify-content: center !important;
            gap: 10px !important;
            margin-top: 10px !important;
        }

        .otp-box {
            width: 50px !important;
            height: 50px !important;
            font-size: 24px !important;
            text-align: center !important;
            border: 2px solid #007bff !important;
            border-radius: 8px !important;
            outline: none !important;
            transition: all 0.3s ease-in-out !important;
        }

        .otp-box:focus {
            border-color: #0056b3 !important;
            box-shadow: 0px 0px 8px rgba(0, 91, 187, 0.5) !important;
        }

        .otp-box::-webkit-inner-spin-button,
        .otp-box::-webkit-outer-spin-button {
            -webkit-appearance: none !important;
            margin: 0 !important;
        }

        .otp-box:disabled {
            background-color: #f5f5f5 !important;
            cursor: not-allowed !important;
        }
    </style>

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
                            @if (session()->has("message"))
                                <div class="alert alert-{{session()->get('color')}} text-center">
                                    {{session()->get('message')}}
                                </div>
                            @endif
                        </div>
                        <form method="POST" action="{{route('two-fa.verify')}}">
                            @csrf
                            <div class="mb-0 pt-2">
                                <div class="text-center">
                                    <h4 class="text-muted">Enter OTP</h4>
                                </div>
                                <div class="otp-container">
                                    <input type="text" class="otp-box" name="otp[]" maxlength="1" required
                                           oninput="moveToNext(this, event)" onkeypress="return isNumberKey(event)">
                                    <input type="text" class="otp-box" name="otp[]" maxlength="1" required
                                           oninput="moveToNext(this, event)" onkeypress="return isNumberKey(event)">
                                    <input type="text" class="otp-box" name="otp[]" maxlength="1" required
                                           oninput="moveToNext(this, event)" onkeypress="return isNumberKey(event)">
                                    <input type="text" class="otp-box" name="otp[]" maxlength="1" required
                                           oninput="moveToNext(this, event)" onkeypress="return isNumberKey(event)">
                                    <input type="text" class="otp-box" name="otp[]" maxlength="1" required
                                           oninput="moveToNext(this, event)" onkeypress="return isNumberKey(event)">
                                    <input type="text" class="otp-box" name="otp[]" maxlength="1" required
                                           oninput="moveToNext(this, event)" onkeypress="return isNumberKey(event)">
                                </div>
                                <!-- Countdown Timer -->
                                <div class="timer-container">
                                    <p id="timer" class="fw-bold">05:00</p>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-8 fs-3 mb-4 rounded-2">Verify Code
                            </button>
                        </form>
                        <form action="{{route('two-fa.resend')}}" method="GET">
                            <p>You have not received OTP?</p>
                            <button type="submit" class="btn btn-dark w-50 py-8 fs-3 mb-4 rounded-2">Resend Code
                            </button>
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

    document.querySelectorAll(".otp-box").forEach((box, index, boxes) => {
        box.addEventListener("keydown", function (e) {
            if (e.key === "Backspace" && this.value === "") {
                const prev = boxes[index - 1];
                if (prev && prev.classList.contains("otp-box")) {
                    prev.focus();
                }
            }
        });
    });

    function isNumberKey(evt) {
        var charCode = evt.which ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            evt.preventDefault();
        }
    }

    function moveToNext(input, event) {
        if (input.nextElementSibling && input.value.length >= input.maxLength) {
            input.nextElementSibling.focus();
        }
    }

    function startTimer(duration, display) {
        let timer = duration,
            minutes,
            seconds;
        setInterval(function () {
            if (!display) return; // Check if display is not null
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;
            display.textContent = minutes + ":" + seconds;
            if (--timer < 0) {
                timer = duration;
            }
        }, 1000);
    }

    window.onload = function () {
        const fiveMinutes = 60 * 5,
            display = document.querySelector("#timer");
        startTimer(fiveMinutes, display);
    };
</script>

</script>

</body>
</html>
