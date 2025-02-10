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
        <li class="breadcrumb-item"><a href="{{ url('/admin/permissions') }}">Permissions</a></li>
        <li class="breadcrumb-item active">Edit Permissions</li>
    </ol>
</nav>

<h5>Edit Permissions</h5>
<hr />

<div class="card">
    <div class="card-body">
        <h6>Edit Permission form</h6>
        <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>

        <form action="{{ url('admin/permissions/edit') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Name:<span class="text-danger">*</span></label>
                        <input type="text" id="name" placeholder="Enter permission name here" name="name" class="form-control form-control-sm" value="{{ $permission->name }}" required>
                        <input type="hidden" name="permission_id"  value="{{ $permission->id }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="title">Title:<span class="text-danger">*</span></label>
                        <input type="text" id="title" placeholder="Enter permission title here" name="title" class="form-control form-control-sm" value="{{ $permission->title }}" required>
                    </div>
                </div>

                <div class="col-md-12">
                    <hr />
                    <button type="submit" class="btn btn-success btn-sm">Save</button>
                    <a href="{{ url('/admin/permissions') }}" class="btn btn-sm btn-link"><i class="fas fa-arrow-left"></i> Go back</a>
                </div>
            </div>


        </form>
    </div>
</div>

@endsection