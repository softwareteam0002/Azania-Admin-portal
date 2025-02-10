@extends('layouts.admin')
@section('content')
<div class="row mb-3">
    <div class="col-md-8">
        <!-- Notifications-->
        @if(Session::has('color'))
        <div class="alert alert-{{ Session::get('color') }} alert-dismissible fade show" role="alert">
            {{ Session::get('notification') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
    </div>

    <div class="col-md-4 text-right">
        <!-- Date and Time-->
        <p id="todayDate" class="small"><b></b></p>
        <h5 id="todayTime" class=""><b></b></h5>
    </div>
    <hr />
</div>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb small" style="background-color:#f9f9f9; padding: .55rem 1rem;">
        <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
        <li class="breadcrumb-item active">Change Password</li>
    </ol>
</nav>

<h5>Change Password</h5>
<hr />



<div class="card">
    <div class="card-body">
        <h6>Changing Password form</h6>
        <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>

        <form action="{{ url('admin/update-password') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('error') ? 'has-error' : '' }}">
                        <label for="name">New Password:<span class="text-danger">*</span></label>
                        <input type="password" id="password" placeholder="Enter new password here" name="password" class="form-control form-control-sm" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="title">Confirm Password:<span class="text-danger">*</span></label>
                        <input type="password" id="new_password" placeholder="Confirm new password" name="new_password" class="form-control form-control-sm" required>
                    </div>
                </div>

                <div class="col-md-12">
                    <hr />
                    <button type="submit" class="btn btn-success btn-sm">Update Password</button>
                    <button type="reset" class="btn btn-link btn-sm">Clear form</button>
                </div>
            </div>
        </form>
    </div>
</div>




@section('scripts')
<!--@parent
<script>
    $(function() {
        //load the data table
        $('.datatable').DataTable({})
    })
</script>-->
@endsection
@endsection