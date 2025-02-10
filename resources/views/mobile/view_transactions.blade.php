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
            <li class="breadcrumb-item"><a href="{{ url('mb/transaction') }}">Transaction</a></li>
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
                    <h4 class="mt-n3 pb-2">{{ $transaction->transaction_date }}</h4>

                    <p>Serial ID:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->transaction_serialID  ?? "N/A" }}</h4>

                    <p>Batch ID:</p>
                    <h4 class="mt-n3 pb-2">{{ $transaction->transaction_batchID ?? "N/A"  }}</h4>

                </div>
                <div class="col-md-4 text-center">
                    <div class="card">
                        <div class="card-body">
                            <p>Transaction ID:</p>
                            <h2>{{ $transaction->transaction_id }}</h2>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <p>Transaction Status:</p>
                                @if($transaction->transaction_status == "200")
                                    <h3 class="text-success"> Success</h3>
                                @elseif($transaction->transaction_status == null)
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
                    <h4 class="mt-n3 pb-2">{{ $transaction->transaction_name ?? "N/A" }}</h4>

                    <p>Amount:</p>
                    <h4 class="mt-n3 pb-2">{{ number_format($transaction->transaction_amount, 2) }}</h4>

                    <p>Charges:</p>
                    <h4 class="mt-n3 pb-2">{{ number_format($transaction->transaction_charge, 2) }}</h4>


                </div>
                <div class="col-md-12">
                    <hr/>
                <a href="{{ url('mb/transaction') }}" class="btn btn-sm btn-link"><i class="fas fa-arrow-left"></i> Go back</a>
                </div>
                
            </div>
        </div>
    </div>


@endsection
