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
        <li class="breadcrumb-item active">Transactions Reverse</li>
    </ol>
</nav>
<hr />

<h5>Transaction Reverse</h5>
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
                        @if($transaction->response_code == "200")
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
                <h4 class="mt-n3 pb-2">{{ $transaction->amount }}</h4>

                <p>Charges:</p>
                <h4 class="mt-n3 pb-2">{{ number_format($transaction->charges, 2) }}</h4>


            </div>
        </div>
        <div class="col-md-12">
            <hr />
		
	    <input title="Reverse" type="submit" name="reverse" value="Reverse" class="btn btn-warning btn-sm" value="Reverse" />
            <a href="{{ url('agency/transactions') }}" class="btn btn-sm btn-link">Go back</a>
        </div>

    </div>
</div>




@endsection