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
        <hr/>
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small" style="background-color:#f9f9f9; padding: .55rem 1rem;">
            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('ib/transactions') }}">Transaction</a></li>
            <li class="breadcrumb-item active">Transactions details</li>
        </ol>
    </nav>


<h5>Transaction Details</h5>
<hr/>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-left">

                    <p>Timestamp:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->transactionTimestamp }}</h4>

                    <p>Refference #:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->reference_number ?? "N/A" }}</h4>

                    <p>Sender Account:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->fromAccountID ?? "N/A" }}</h4>

                    <p>Receiver Account:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->toAccountID ?? "N/A" }}</h4>

                    <p>Mobile #:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->mobile_number ?? "N/A"  }}</h4>


                </div>
                <div class="col-md-4 text-center">
                    <div class="card">
                        <div class="card-body">
                            <p>Transaction ID:</p>
                            <h2>{{ $transaction->transactionId }}</h2>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <p>Transaction Status:</p>
                                @if($transaction->responseCode == "200" || $transaction->responseCode ==200)
                                    <h3 class="text-success"> Success</h3>
                                @elseif($transaction->status_id == 1 && is_null($transaction->responseCode))
                                    <h3 class="text-warning"> On Progress</h3>
                                @else
                                    <h3 class='text-danger'> Failed</h3>
                                @endif
				
                        </div>
                    </div>
                    <hr/>
                    <p class="text-left">Description:<br/>{{ $transaction->trxnDescription ?? "There is no description provided on this transaction."}}</p>
              
                </div>
                <div class="col-md-4 text-right">
                
                    <p>Service Type:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->serviceType ?? "N/A" }}</h4>

                    <p>Serial ID:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->serialID  ?? "N/A" }}</h4>

                    <p>Batch ID:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->batchID ?? "N/A"  }}</h4>

                    <p>Amount:</p>
                    <h4 class="mt-n3 pb-2">{{ number_format($transaction->trxAmount, 2) }}</h4>

                    <p>Charges:</p>
                    <h4 class="mt-n3 pb-2">{{ number_format($transaction->charge, 2) }}</h4>


                </div>
				<div class="col-md-12">
                    <hr/>
					<p class="text-left">Thirdparty Response:<br/>{{ $transaction->thirdpartResponse ?? "N/A"}}</p>

                </div>
                <div class="col-md-12">
                    <hr/>
                <a href="{{ url('ib/transactions') }}" class="btn btn-sm btn-link">Go back</a>
                </div>
                
            </div>
        </div>
    </div>


@endsection
