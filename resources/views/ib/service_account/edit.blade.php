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
            <li class="breadcrumb-item active">Manage service accounts</li>
        </ol>
    </nav>

    <h5>Manage service accounts</h5>
    <hr/>

    <div class="card mb-5">
        <div class="card-body">
            <h6>Create Manage service account form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ url('ib/service_account/update', $serviceAccount->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">

                    <input type="hidden" name="action" value="Edit Manage service accounts">
                    <input type="hidden" name="request_type" value="ib_service_account">
                    <input type="hidden" name="module" value="IB">
                    <input type="hidden" name="id" value="{{ $serviceAccount->id }}">
                    <input type="hidden" name="old_details" value="{{ $serviceAccount }}">
                    <input type="hidden" name="initiator_id" value="{{ Auth::user()->id }}">
                    <input type="hidden" name="approver_id" value="0">

            <div class="col-md-4">
               <div class="form-group">
                    <label for="types">Service Name:<span class="text-danger">*</span></label>
                    <select name="service_id" class="form-control" required>
                        @foreach($services as $service)
            <option value="{{ $service->id }}" @if($serviceAccount->service_code == $service->id) selected @endif>{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>
        </div>

                        <div class="col-md-3">
                            <div class="form-group {{ $errors->has('account_number') ? 'has-error' : '' }}">
                                <label for="account_number">Account Number:</label>
                                <input type="text" id="account_number" name="account_number" class="form-control form-control-sm" value="{{ old('account_number', isset($serviceAccount) ? $serviceAccount->account_number : '') }}" placeholder="Enter account numbers here." >
                                @if($errors->has('account_number'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('account_number') }}
                                    </em>
                                @endif
                               </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{ $errors->has('commission_account') ? 'has-error' : '' }}">
                                <label for="commission_account">Commission Account:</label>
                                <input type="text" id="commission_account" name="commission_account" class="form-control form-control-sm" value="{{ old('commission_account', isset($serviceAccount) ? $serviceAccount->commission_account : '') }}" placeholder="Enter commission account numbe here." >
                                @if($errors->has('commission_account'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('commission_account') }}
                                    </em>
                                @endif
                               </div>
                        </div>
						<div class="col-md-3">
                            <div class="form-group {{ $errors->has('retail_trxn_limit') ? 'has-error' : '' }}">
                                <label for="retail_trxn_limit">Retail Transaction Limit:</label>
                                <input type="number" id="retail_trxn_limit" name="retail_trxn_limit" class="form-control form-control-sm" value="{{ old('retail_trxn_limit', isset($serviceAccount) ? $serviceAccount->retail_transaction_limit : '') }}" placeholder="Enter retail transaction limit here." >
                                @if($errors->has('retail_trxn_limit'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('retail_trxn_limit') }}
                                    </em>
                                @endif
                               </div>
                        </div>
						<div class="col-md-3">
                            <div class="form-group {{ $errors->has('retail_daily_limit') ? 'has-error' : '' }}">
                                <label for="retail_daily_limit">Retail Daily Limit:</label>
                                <input type="number" id="retail_daily_limit" name="retail_daily_limit" class="form-control form-control-sm" value="{{ old('retail_daily_limit', isset($serviceAccount) ? $serviceAccount->retail_daily_limit : '') }}" placeholder="Enter retail daily limit here." >
                                @if($errors->has('retail_daily_limit'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('retail_daily_limit') }}
                                    </em>
                                @endif
                               </div>
                        </div>
						<div class="col-md-3">
                            <div class="form-group {{ $errors->has('corporate_trxn_limit') ? 'has-error' : '' }}">
                                <label for="corporate_trxn_limit">Corporate Transaction Limit:</label>
                                <input type="number" id="corporate_trxn_limit" name="corporate_trxn_limit" class="form-control form-control-sm" value="{{ old('corporate_trxn_limit', isset($serviceAccount) ? $serviceAccount->corporate_transaction_limit : '') }}" placeholder="Enter corporate transaction limit here." >
                                @if($errors->has('corporate_trxn_limit'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('corporate_trxn_limit') }}
                                    </em>
                                @endif
                               </div>
                        </div>
						<div class="col-md-3">
                            <div class="form-group {{ $errors->has('corporate_daily_limit') ? 'has-error' : '' }}">
                                <label for="corporate_daily_limit">Corporate Daily Limit:</label>
                                <input type="number" id="corporate_daily_limit" name="corporate_daily_limit" class="form-control form-control-sm" value="{{ old('corporate_daily_limit', isset($serviceAccount) ? $serviceAccount->corporate_daily_limit : '') }}" placeholder="Enter corporate daily limit here." >
                                @if($errors->has('corporate_daily_limit'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('corporate_daily_limit') }}
                                    </em>
                                @endif
                               </div>
                        </div>

                    <div class="col-md-12">
                        <hr/>
                        <button type="submit" class="btn btn-success btn-sm">Update</button>
                        <a href="{{ url('ib/service_account/index') }}" class="btn btn-link btn-sm"><i class="fas fa-arrow-left"></i> Go back</a>
                    </div>

                </div>



            </form>
        </div>
    </div>


    @endsection
