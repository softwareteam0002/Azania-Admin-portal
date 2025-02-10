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
            <li class="breadcrumb-item"><a href="{{ url('ib/institutions/index') }}">Institutions</a></li>
            <li class="breadcrumb-item active">Institution Approval</li>
        </ol>
    </nav>


    <h5>Institution Approval</h5>
    <hr/>

    <div class="card">

        <div class="card-body">
            <h6>Institution Approval form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>

            <form action="{{ route('ib.institution.approveAct', $institution->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Institution Name:</label>
                            <input type="text" id="institute_name" name="institute_name" value="{{ $institution->institute_name }}" class="form-control form-control-sm" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Address:</label>
                            <input type="text" id="address" name="address" value="{{ $institution->address }}" class="form-control form-control-sm" readonly>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="types">Payment Solution:<span class="text-danger">*</span></label>
                            <select name="payment_solution" class="form-control form-control-sm" readonly>
                                <option value="1" @if($institution->hasPaySolution==1) selected @endif>Enable</option>
                                <option value="0" @if($institution->hasPaySolution==0) selected @endif>Disable</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Description:</label>
                            <textarea  class="form-control form-control-sm" name="description" id="description" cols="10" rows="1" readonly>{{ $institution->description }}</textarea>
                        </div>
                    </div>
             <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Institution Type:</label>
                            <select name="institution_type" class="form-control form-control-sm">
                                <option value="">Select Institution Type</option>
                                @foreach($institution_types as $institution_type)
                                <option value="{{$institution_type->id}}" @if($institution->institution_type == $institution_type->id) selected @endif>{{$institution_type->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <hr/>
                        <input type="submit" name="approve" value="approve" class="btn btn-success btn-sm" value="Approve" />
                        <input type="submit" name="reject" value="reject" class="btn btn-primary btn-sm" value="Reject" />
                        <a href="{{ url('ib/institutions/index') }}" class="btn btn-link btn-sm"><i class="fas fa-arrow-left"></i> Go back</a>
                    </div>
                </div>
            </form>
        </div>
    </div>


@endsection

