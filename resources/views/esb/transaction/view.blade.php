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
            <li class="breadcrumb-item"><a href="{{ url('esb/transaction') }}">Transaction</a></li>
            <li class="breadcrumb-item active">Transactions details</li>
        </ol>
    </nav>

    <h5>Transaction Details</h5>
    <hr/>

    <div class="card">
        <div class="card-body">
            <h6 class="">Transaction details ({{$transaction->id}})</h6>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tbody>
                    <tr><td style="width:200px"><b>Channel</b></td><td>{{$transaction->transaction_channel}}</td></tr>
                    <tr><td><b>Transaction Name</b></td><td>{{$transaction->transaction_name}}</td></tr>
                    <tr><td><b>Transaction Amount</b></td><td>{{ number_format($transaction->transaction_amount)}}/=</td></tr>
                    <tr><td><b>Destination Account</b></td><td> @if($transaction->transaction_name == 'UTILITY'){{$service_ac->account_number ?? 'N/A'}}@else --nil-- @endif </td></tr>
                    <tr><td><b>Transaction Date</b></td><td>{{$transaction->transaction_date}}</td></tr>
                    <tr><td><b>Transaction Status Code</b></td><td>{{$transaction->transaction_status}}</td></tr>
                    <tr><td><b>Transaction ID</b></td><td>{{$transaction->transaction_id}}</td></tr>
                    <tr><td><b>Transaction Response</b></td><td>{{$transaction->transaction_response ?? '--nil--'}}</td></tr>
                    <tr><td><b>Transaction Third party status</b></td><td>{{$transaction->transaction_thirdpart_status ?? '--nil--'}}</td></tr>
                    <tr><td><b>Transaction Third party data</b></td><td>{{$transaction->transaction_thirdpart_data ?? '--nil--'}}</td></tr>
                    <tr><td><b>Transaction Third party reference</b></td><td>{{$transaction->transaction_thirdpart_ref ?? '--nil--'}}</td></tr>
                    <tr><td><b>Transaction Serial ID</b></td><td>{{$transaction->transaction_serialID ?? '--nil--'}}</td></tr>
                    <tr><td><b>Transaction Batch ID</b></td><td>{{$transaction->transaction_batchID ?? '--nil--'}}</td></tr>
                    <tr><td><b>Transaction Charge</b></td><td>{{$transaction->transaction_charge ?? '--nil--'}}</td></tr>
                    </tbody>
                </table>
                <hr/>
                <a href="{{ url('esb/transaction') }}" class="btn btn-sm btn-link">Go back</a>
            </div>

        </div>
    </div>





@endsection
