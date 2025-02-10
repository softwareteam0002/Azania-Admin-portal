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
        <hr/>
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small" style="background-color:#f9f9f9; padding: .55rem 1rem;">
            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('agency/users') }}">Agents</a></li>
            <li class="breadcrumb-item active">Edit Agent Account</li>
        </ol>
    </nav>

    <h5>Edit Agent Accounts</h5>

    <hr/>
    <div class="card">
        <div class="card-body">
            <h6>Edit agent account form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ url('agency/accounts/update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="name">Bank Account:<span class="text-danger">*</span></label>
                            <input type="text" id="bank_account" name="account_number"
                                   class="form-control form-control-sm" value="{{ $account->bank_account }}" required>
                            <input type="hidden" name="account_id" value="{{ $account->account_id}}">
                            <input type="hidden" name="agent_id" value="{{ $account->agent_id}}">
                        </div>
                    </div>


                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="types">Account Type:</label>
                            <select name="account_type_id" class="form-control form-control-sm">
                                <option value="0">Select account type</option>
                                @foreach($accounttypes as $accounttype)
                                    @if($accounttype->type_id == $account->account_type_id)
                                        <option value="{{ $accounttype->type_id }}"
                                                selected>{{ $accounttype->account_type }}</option>
                                    @else
                                        <option value="{{ $accounttype->type_id }}">{{ $accounttype->account_type }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="types">Account Status:</label>
                            <select name="account_status" class="form-control form-control-sm">
                                <option value="0">Select account status</option>
                                @foreach($accountstatuses as $accountstatus)
                                    @if($accountstatus->tbl_status_id == $account->account_status)
                                        <option value="{{ $accountstatus->tbl_status_id}}"
                                                selected>{{ $accountstatus->status}}</option>
                                    @else
                                        <option value="{{ $accountstatus->tbl_status_id}}">{{ $accountstatus->status}}</option>
                                    @endif

                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="col-md-12">
                        <hr/>
                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                        <a href="{{ url('agency/accounts/'.$account->agent_id) }}" class="btn btn-sm btn-link"><i class="fas fa-arrow-left"></i> Go back</a>
                    </div>
                </div>
            </form>

        </div>
    </div>



@endsection
