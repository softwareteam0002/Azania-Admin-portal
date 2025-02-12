@extends('layouts.master_auth')

@section('title', 'Admin Portal: Forgot Password')

@section('content')
    <div class="login-container">

        <!-- Row for two columns -->
        <div class="row">
            <!-- First Container -->
            <div class="col-md-12 mb-4">
                <div class="card p-4 border-0">
                    <div class="card-body">
                        <h4 class="text-center">Recover Password</h4>
                        <div class="pt-2 mt-4 text-center">
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
                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    <p>Return Back To <a href="{{url('/home')}}">Login</a></p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="{{ asset('public/js/custom.js') }}"></script>
@endsection

