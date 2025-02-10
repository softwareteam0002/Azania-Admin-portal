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
        <li class="breadcrumb-item"><a href="{{ url('agency/account/service') }}">Service Accounts</a></li>
        <li class="breadcrumb-item active">Edit Service Accounts</li>
    </ol>
</nav>

<h5>Edit Service Accounts</h5>
<hr />
<div class="card mb-5">
    <div class="card-body">
        <h6>Edit Service Account form</h6>
        <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
        <form action="{{ url('agency/account/update') }}" method="POST">
            @csrf
            @method('POST')
            <div class="row">

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Service:<span class="text-danger">*</span></label>
                        <select name="bank_service_ID" id="" class="form-control form-control-sm" disabled>
                            <option value="{{ $account->bank_service_ID }}">{{ $account->services->agent_serviceName }}</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Collection Account:<span class="text-danger">*</span></label>
                        <input type="text" name="colection_account" class="form-control form-control-sm" value="{{ $account->colection_account }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Disbursement Account:</label>
                        <input type="text" name="disbursement_account" class="form-control form-control-sm" value="{{ $account->disbursement_account }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name"> Bank Agency Commision Account:</label>
                        <input type="text" name="agency_commision_account" class="form-control form-control-sm" value="{{ $account->agency_commision_account }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Bank Agency Payable Commision Account:</label>
                        <input type="text" name="agency_payable_commision_account" class="form-control form-control-sm" value="{{ $account->agency_payable_commision_account }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Bank Agency Expense Deposit:</label>
                        <input type="text" name="agency_expenses_deposit" class="form-control form-control-sm" value="{{ $account->agency_expenses_deposit }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Bank Agency Deposit Commision Account:</label>
                        <input type="text" name="agency_deposit_commision_account" class="form-control form-control-sm" value="{{ $account->agency_deposit_commision_account }}">
                        <input type="hidden" name="id" value="{{ $account->bank_account_id }}">
                    </div>
                </div>

                <div class="col-md-12">
                    <hr />
                    <button type="submit" class="btn btn-success btn-sm">Save</button>
                    <a href="{{ url('agency/account/service') }}" class="btn btn-sm btn-link"><i class="fas fa-arrow-left"></i> Go back</a>
                </div>

            </div>



        </form>
    </div>
</div>

@endsection
