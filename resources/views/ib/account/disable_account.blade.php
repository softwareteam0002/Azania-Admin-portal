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
            @include('error_handler')
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
            <li class="breadcrumb-item active">Users</li>
        </ol>
    </nav>

    <h5>Users</h5>
    <hr/>

    <div class="card mb-5">
        <div class="card-body">
            <h6>Disable Accounts Approval form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ route('ib.accounts.disableActApproval', $account->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">

                    <input type="hidden" name="action" value="Disable Account">
                    <input type="hidden" name="request_type" value="ib_account_disable">
                    <input type="hidden" name="module" value="IB">
                    <input type="hidden" name="id" value="{{ $account->id }}">
                    <input type="hidden" name="old_details" value="{{ $account }}">
                    <input type="hidden" name="initiator_id" value="{{ Auth::user()->id }}">
                    <input type="hidden" name="approver_id" value="0">
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
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}" @if($account->account_type_id==$type->id)
                                    selected @endif >{{ $type->name }}</option>
                                @endforeach
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
