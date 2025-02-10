@extends('layouts.admin')
@section('content')

<div class="row mb-3">
    <div class="col-md-8">
        <!-- Notifications-->

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
        <li class="breadcrumb-item"><a href="{{ url('agency/transactions') }}">Transaction</a></li>
        <li class="breadcrumb-item active">Transactions details</li>
    </ol>
</nav>
<hr />

<!--

{
    "txn_id": "155",
    "operator_id": "100004",
    "trans_agent_terminal_id": "MKCB00002",
    "trxn_name": "Deposit Cash",
    "trxn_type": "DC",
    "amount": "2,500",
    "charges": "200.00",
    "trans_datetime": "2020-07-24-11:33:02",
    "transactionID": "AB.1595579582.59398400",
    "date": "2020-07-24-11:33:02",
    "response_code": "",
    "responseMessage": "",
    "trxn_status": "",
    "customer_account": "00110100830301",
    "trxn_start_time": "2020-07-24-11:33:02",
    "trxn_end_time": "",
    "transaction_reversed": "1",
    "initiator_id": null,
    "approver_id": null,
    "reprinting_value": null,
    "serialID": null,
    "stan": null,
    "rowIdCharge": null,
    "serialIDCharge": null,
    "batchID": null,
    "batchIDCharge": null,
    "rowId": null,
    "third_party_response": null
}

 -->
<h5>Transaction Details</h5>
<hr />

<div class="card">

    <div class="card-body">


        <div class="row">
            <div class="col-md-4 text-left">
                <p>Timestamp:</p>
                <h4 class="mt-n3 pb-2">{{ $transaction->trans_datetime }}</h4>

                <p>Terminal ID:</p>
                <h4 class="mt-n3 pb-2">{{ $transaction->trans_agent_terminal_id ?? "N/A" }}</h4>

                <p>Operator ID:</p>
                <h4 class="mt-n3 pb-2">{{ $transaction->operator_id  ?? "N/A" }}</h4>

                <p>Customer Account:</p>
                <h4 class="mt-n3 pb-2">{{ $transaction->customer_account ?? "N/A" }}</h4>



            </div>
            <div class="col-md-4 text-center">

                <div class="card">
                    <div class="card-body">
                        <p class="">Transaction ID:</p>
                        <h2>{{ $transaction->transactionID }}</h2>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p>Transaction Status:</p>
                        @if($transaction->response_code == "200" || $transaction->response_code == "00")
                        <h3 class="text-success"> Success</h3>
                        @elseif($transaction->response_code == null)
                        <h3 class="text-warning"> On Progress</h3>
                        @else
                        <h3 class='text-danger'> Failed</h3>
                        @endif




                    </div>

                </div>

            </div>
            <div class="col-md-4 text-right">


                <p>Service Type:</p>
                <h4 class="mt-n3 pb-2">{{ $transaction->trxn_name ?? "N/A" }}</h4>


                <p>Batch ID:</p>
                <h4 class="mt-n3 pb-2">{{ $transaction->batchID ?? 'N/A' }}</h4>

                <p>Amount:</p>
                <h4 class="mt-n3 pb-2">{{ number_format($transaction->amount/100,2) }}</h4>

                <p>Charges:</p>
                <h4 class="mt-n3 pb-2">{{ number_format($transaction->charges, 2) }}</h4>


            </div>
        </div>
        <div class="col-md-12">
            <hr />
	 <form action="{{ route('agency.transaction.reverse') }}" method="POST">
                    {!! csrf_field() !!}
                <input type="hidden" name="serviceAccountId" value="00129900153101">
                <input type="hidden" name="mobile" value="255654896656">
                <input type="hidden" name="charge" value="{{ $transaction->charges }}">
                <input type="hidden" name="transactionId" value="{{ $transaction->transactionID }}">
		<input type="hidden" name="accountID" value="{{ $transaction->customer_account }}">
		<input type="hidden" name="trxAmount" value="{{ $transaction->amount }}">
		<input type="hidden" name="trxnDescription" value="Transaction reversal">

		  <!-- @if($transaction->response_code == "200")
            <input title="Reverse Transaction" type="submit" name="submit" value="Reverse" class="btn btn-warning btn-sm" value="Reverse" />
             @endif -->
		<a href="{{ url('agency/transactions') }}" class="btn btn-sm btn-link">Go back</a>
                </form>

        </div>

    </div>
</div>




@endsection
