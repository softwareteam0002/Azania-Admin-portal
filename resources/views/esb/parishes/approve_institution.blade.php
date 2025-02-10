@extends('layouts.admin')
@section('content')
    <div class="row mb-3">
        <div class="col-md-8">
            <!-- Notifications-->
            @if(session('notification') && session('color'))
                <div class="alert alert-{{ session('color') }} alert-dismissible fade show" role="alert">
                    {{ session('notification') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif


            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    @foreach($errors->all() as $error)
                        <li class="list-group-item">
                            {{ $error }}
                        </li>
                    @endforeach
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(Session::has('message'))
                <div class="alert alert-success alert-dismissible">
                    <ul>
                        <li>{{Session::get('message')}}</li>
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(isset($notification) && isset($color))
                <div class="alert alert-{{ $color }} alert-dismissible fade show" role="alert">
                    {{ $notification }}
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
        <hr/>
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small" style="background-color:#f9f9f9; padding: .55rem 1rem;">
            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('esb/parishes/index') }}">Church Institutions</a></li>
            <li class="breadcrumb-item active">Church Institution Approval</li>
        </ol>
    </nav>


    <h5>Church Institution Approval</h5>
    <hr/>

    <div class="card">

        <div class="card-body">
            <h6>Church Institution Approval form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>

            <form action="{{ route('esb.churchinstitution.approveAct', $institution->id) }}" method="POST">
                @csrf
                @method('PUT')
				
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Institution Name:</label>
                            <input type="text" id="institute_name" name="institute_name" value="{{$institution->description}}" class="form-control form-control-sm" readonly>
                        </div>
                    </div>
					
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Account Number:</label>
                            <input type="text" id="account_number" name="account_number" value="{{$institution->account_number}}"  class="form-control form-control-sm" readonly>
                        </div>
                    </div>
					<div class="col-md-6">
                        <div class="form-group">
                            <label for="prefix">Church Institution Prefix:</label>                           
                            <input type="text" id="institute_prefix" name="institute_prefix" value="{{$institution->member_id}}" class="form-control form-control-sm" value="" placeholder="Enter institution prefix here.">                          
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <hr/>
                        <input type="submit" name="approve" value="approve" class="btn btn-success btn-sm" value="Approve" />
                        <input type="submit" name="reject" value="reject" class="btn btn-primary btn-sm" value="Reject" />
                        <a href="{{ url('esb/parishes/index') }}" class="btn btn-link btn-sm"><i class="fas fa-arrow-left"></i> Go back</a>
                    </div>
                </div>
            </form>
        </div>
    </div>


@endsection

