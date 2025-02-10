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
            <li class="breadcrumb-item"><a href="{{ url('ib/accounts/index') }}">Accounts</a></li>
            <li class="breadcrumb-item active">Account Approval</li>
        </ol>
    </nav>


    <h5>Account Approval</h5>
    <hr/>

    <div class="card">

        <div class="card-body">
            <h6>Account Approval form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>

            <form action="{{ route('ib.account.approveAct', $account->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-3">
                        <input type="hidden" name="aCStatus" value="1">
                        <div class="form-group">
                            <label for="name">Account Name</label>
                            <input type="text" id="account_name" name="account_name" value="{{ $account->accountName }}" class="form-control form-control-sm" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Account Number</label>
                            <input type="text" id="account_number" name="account_number" value="{{ $account->accountID }}" class="form-control form-control-sm" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="types">Account Type<span class="text-danger">*</span></label>
                            <select name="account_type" class="form-control form-control-sm" readonly>
                                <!--@foreach($types as $type)
                                    <option value="{{ $type->id }}" @if($account->account_type_id==$type->id)
                                    selected @endif >{{ $type->name }}</option>
                                @endforeach
								<option value="{{ $account->productName }}"  selected >{{ $account->productName }}</option>-->
								<option value="{{ $account->types->name }}"  selected >{{ $account->types->name }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="types">Branch Name<span class="text-danger">*</span></label>
                            <select name="branch_id" class="form-control form-control-sm" readonly>
                                @foreach($branchs as $branch)
                                    <option value="{{ $branch->id }}" @if($account->branchId==$branch->id)
                                        selected="selected" @endif>{{ $branch->branchName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if(isset($account->institution_id))
                        <div class="col-md-3">
                        <div class="form-group">
                            <label for="types">Institution Name</label>
                            <select name="institution_id" class="form-control form-control-sm" readonly>
                                <option value="0" >Select Institution Name</option>
                                @foreach($institutions as $institution)
                                    <option value="{{ $institution->id }}"  @if($account->institution_id==$institution->id)
                                    selected="selected" @endif>{{ $institution->institute_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-12">
                        <hr/>
                        <input type="submit" name="approve" value="approve" class="btn btn-success btn-sm" value="Approve" />
                        <input type="submit" name="reject" value="reject" class="btn btn-primary btn-sm" value="Reject" />
                        <a href="{{ url('ib/accounts/index') }}" class="btn btn-link btn-sm"><i class="fas fa-arrow-left"></i> Go back</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
