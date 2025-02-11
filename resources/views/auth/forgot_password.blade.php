@extends('layouts.master_auth')

@section('title', 'Admin Portal: Forgot Password')

@section('content')
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
@endsection
