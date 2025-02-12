@extends('layouts.master_auth')

@section('title', 'Admin Portal: OTP Verification')

@section('content')
    <div class="login-container">
        <div class="card-group">
            <div class="card p-4 border-0">
                <div class="card-body">
                    <div class="pt-2 text-center text-nowrap">
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
                            <div class="timer-container mt-2">
                                <h4 id="timer" class="fw-bold text-center">05:00</h4>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary login-btn w-100 py-8 fs-3 mb-4 rounded-2">Verify Code
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

    <script src="{{ asset('public/js/custom.js') }}"></script>
@endsection


