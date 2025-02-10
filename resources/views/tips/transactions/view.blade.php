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

                    <p>Completed Timestamp:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->completedTimestamp }}</h4>

                    <p>Payer Name:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->payer_fullName ?? "N/A" }}</h4>
					 <p>Payee Name:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->payee_fullName ?? "N/A" }}</h4>

                    <p>Sender Account:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->payer_identifier ?? "N/A" }}</h4>

                    <p>Receiver Account:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->payee_identifier ?? "N/A" }}</h4>
					<p>Payer Account Category:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->payer_accountCategory ?? "N/A"  }}</h4>
					
					<p>Payer Account Type:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->payer_accountType ?? "N/A"  }}</h4>
                    <p>Switch Reversal Refference #:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->switchReversalRef ?? "N/A"  }}</h4>
					<p>Payer Identity:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->payer_identity_type ?? "N/A"  }}</h4>
					
					<p>Payer Identity Value:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->payer_identity_value ?? "N/A"  }}</h4>
					
					
                </div>
                <div class="col-md-4 text-center">
                    <div class="card">
                        <div class="card-body">
                            <p>Transaction ID:</p>
                            <h2>{{ $transaction->payerRef }}</h2>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <p>Transaction Status:</p>

								 @if($transaction->transferState == 'COMMITTED')
												 <h3 class="text-success">{{$transaction->transferState}}</h3>
                                                @elseif($transaction->transferState == 'INPROGRESS' || empty($transaction->transferState))
													<h3 class="text-warning">{{$transaction->transferState ?? 'INPROGRESS'}}</h3>
                                                @elseif($transaction->transferState == 'ABORTED')
													<h3 class='text-danger'> {{$transaction->transferState ?? 'ABORTED'}}</h3>
                                                @else
												<h3 class='text-danger'> {{$transaction->transferState}}</h3>
                                                @endif
				
                        </div>
                    </div>
					<div class="card">
                        <div class="card-body">
                            <p>Switch Refference ID:</p>
                            <h2>{{ $transaction->switchRef ?? "N/A" }}</h2>
                        </div>
                    </div>
                    <hr/>
                    <p class="text-left">Transaction Description:<br/>{{ $transaction->description  ?? "N/A" }}</p>
             
                </div>
                <div class="col-md-4 text-right">
                
                    <p>Service Type:</p>
                    <h4 class="mt-n3 pb-2">TIPS-{{ $transaction->payee_identifierType ?? "N/A" }}</h4>

                    <p>Payee Refference #:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->payeeRef  ?? "N/A" }}</h4>

                    <p>Amount:</p>
                    <h4 class="mt-n3 pb-2">{{$transaction->currency}}{{ number_format($transaction->amount, 2) }}</h4>

                    <p>Charges:</p>
                    <h4 class="mt-n3 pb-2">{{ number_format($transaction->endUserFee_amount, 2) }}</h4>
					
					<p>Payee Account Category:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->payee_accountCategory ?? "N/A"  }}</h4>
					
					<p>Payee Account Type:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->payee_accountType ?? "N/A"  }}</h4>
					
                    <p>Payee Identity:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->payee_identity_type ?? "N/A"  }}</h4>
					
					<p>Payee Identity Value:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->payee_identity_value ?? "N/A"  }}</h4>
					
					 <p>Reversal Reason:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->reversalReason ?? "N/A"  }}</h4>
					
					<p>Reversal State:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->reversalState ?? "N/A"  }}</h4>
					<p>Hold Status:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->holdStatus ?? "N/A"  }}</h4>

                </div>
				<!--<div class="col-md-12">
                    <hr/>
					<p class="text-left">Thirdparty Response:<br/>{{ $transaction->response_message ?? "N/A"}}</p>

                </div>-->
                <div class="col-md-12">
                    <hr/>
                <a href="{{ url('tips/transactions') }}" class="btn btn-sm btn-link">Go back</a>
                </div>
                
            </div>
        </div>
    </div>


@endsection
