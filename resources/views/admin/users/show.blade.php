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
        <li class="breadcrumb-item"><a href="{{ url('/admin/users') }}">System Users</a></li>
        <li class="breadcrumb-item active">Users Details</li>
    </ol>
</nav>

<h5>Users Details</h5>
<hr />

<div class="card">
    <div class="card-body">
        <div class="row">

            <div class="col-md-4">
                <p class="bold">Email:</p>
                <h5>{{ $user->email }}</h5>
            </div>

            <div class="col-md-4 text-center">
                <p class="bold">Name:</p>
                <h5>{{ $user->name }}</h5>
                
            </div>

            <div class="col-md-4 text-right">
                <p class="bold">Role:</p>
                @foreach($user->roles as $key => $item)
                            <span class="badge badge-info">{{ $item->title }}</span>
                            @endforeach
                <!-- <h5>{{ $user->roles[0]->title }}</h5> -->
            </div>

            <div class="col-md-12">
                    <hr />
                    <a href="{{ url('/admin/users') }}" class="btn btn-sm btn-link"><i class="fas fa-arrow-left"></i> Go back</a>
                </div>
        </div>
    </div>
</div>

@endsection
