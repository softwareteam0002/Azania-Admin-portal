@extends('layouts.admin')
@section('title', 'Transactions')
@section('content')
    <div class="sl-mainpanel">
        <nav class="breadcrumb sl-breadcrumb">
            <a class="breadcrumb-item" href="{{url('/home')}}">Dashboard</a>
            <a class="breadcrumb-item" href="{{url('/tips/transactions')}}">Transactions</a>
            <span class="breadcrumb-item active">List</span>
        </nav>

        <div class="sl-pagebody">
            <!-- <div class="sl-page-title">
            </div> -->
            <div class="row">
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="float-left">
                                    <!-- <a href="{{url('transactions/create')}}" class="btn btn-sm btn-primary">Add Transaction</a>-->
                                </div>
                            </div>
                            <div class="float-right">
                                <form>
                                    <div class="input-group">
                                        <input type="text" name="search" id="search" class="form-control" placeholder="Search" >

                                        <div class="input-group-btn">
                                            <button class="btn btn-secondary" id="searchBtn"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- <h4>Permissions</h4> -->
                        </div>
                        <div class="card-body p-1">
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif
                            <div class="table-responsive" style="overflow-x:auto;">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>PAYER REFERENCE</th>
                                        <!--<th>LOOKUP ID</th>-->
                                        <th>PAYER FSPID</th>
                                        <th>PAYER FULLNAME</th>
                                       <th>PAYER IDENTIFIER</th>
                                        <th>PAYER IDENTIFIER TYPE</th>
                                         <!--<th>PAYER IDENTITY</th>
                                        <th>PAYER IDENTITY TYPE</th>-->
                                        <th>AMOUNT</th>
                                        <!--<th>FEE</th>-->
                                        <th>SWITCH REFERENCE</th>
                                        <th>COMPLETION TIME</th>
                                        <th>TRANSFER STATE</th>
                                        <!--<th>TRANSFER RESPONSE</th>-->
                                        <th>PAYEE FSPID</th>
                                        <th>PAYEE FULLNAME</th>
                                        <th>PAYEE REFERENCE</th>
                                        <th>PAYEE IDENTIFIER</th>
                                        <th>PAYEE IDENTIFIER TYPE</th>
                                        <!--<th>PAYEE IDENTITY</th>
                                        <th>PAYEE IDENTITY TYPE</th>-->
                                        <th>HOLD STATUS</th>
                                        <th>REVERSAL STATE</th>
                                        <th>REVERSAL REASON</th>
                                       <!-- <th>SWITCH REVERSAL REFERENCE</th>
                                        <th>PAYER REVERSAL REFERENCE</th>
                                        <th>PAYEE REVERSAL REFERENCE</th>
                                        <th>REVERSAL TIME</th>-->
                                        <th width="15%">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="tableData">
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{$transaction->payerRef}}</td>
                                            <!--<td>{{$transaction->lookup_id}}</td>-->
                                            <td>{{$transaction->payer_fspId}}</td>
                                            <td>{{$transaction->payer_fullName}}</td>
                                            <td>{{$transaction->payer_identifier}}</td>
                                            <td>{{$transaction->payer_identifierType}}</td>
                                            <!--<td>{{$transaction->payer_identity_value}}</td>
                                            <td>{{$transaction->payer_identity_type}}</td>-->
                                            <td>{{$transaction->currency}}{{$transaction->amount}}</td>
                                           <!-- <td>{{$transaction->endUserFee_currency}}{{$transaction->endUserFee_amount}}</td>-->
                                            <td>{{$transaction->switchRef}}</td>
                                            <td>{{$transaction->completedTimestamp}}</td>
                                            <td>
                                                @if($transaction->transferState == 'COMMITTED')
                                                    <span class="badge badge-success">{{$transaction->transferState}}</span>
                                                @elseif($transaction->transferState == 'INPROGRESS' || empty($transaction->transferState))
                                                    <span class="badge badge-warning">{{$transaction->transferState ?? 'INPROGRESS'}}</span>
                                                @elseif($transaction->transferState == 'ABORTED')
                                                    <span class="badge badge-danger">{{$transaction->transferState ?? 'ABORTED'}}</span>
                                                @else
                                                    <span class="badge badge-danger">{{$transaction->transferState}}</span>
                                                @endif
                                            </td>
                                            <!--<td>{{$transaction->response_message}}</td>-->
                                            <td>{{$transaction->payee_fspId}}</td>
                                            <td>{{$transaction->payee_fullName}}</td>
                                            <td>{{$transaction->payeeRef}}</td>
                                            <td>{{$transaction->payee_identifier}}</td>
                                            <td>{{$transaction->payee_identifierType}}</td>
                                            <!--<td>{{$transaction->payee_identity_value}}</td>
                                            <td>{{$transaction->payee_identity_type}}</td>-->
                                            <td> 
                                                @if($transaction->holdStatus == 'CONFIRMED_HOLD')
                                                    <span class="badge badge-success">CONFIRMED HOLD</span>
                                                @elseif($transaction->holdStatus == 'CONFIRMED_WITHDRAW')
                                                    <span class="badge badge-warning">CONFIRMED WITHDRAW</span>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>

                                                @if($transaction->reversalState == 'REVERSED')
                                                    <span class="badge badge-success">{{$transaction->reversalState}}</span>
                                                @elseif($transaction->reversalState == 'INPROGRESS')
                                                    <span class="badge badge-warning">{{$transaction->reversalState ?? 'INPROGRESS'}}</span>
                                                @elseif($transaction->reversalState == 'CONFIRMED_WITHDRAW')
                                                    <span class="badge badge-warning">CONFIRMED WITHDRAW</span>
                                                @elseif($transaction->reversalState == 'CONFIRMED_HOLD')
                                                    <span class="badge badge-success">CONFIRMED HOLD</span>
                                                @elseif($transaction->reversalState == 'CANCELLED' )
                                                    <span class="badge badge-danger">{{$transaction->reversalState ?? 'CANCELLED'}}</span>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{$transaction->reversalReason ?? 'N/A'}}</td>
                                           <!-- <td>{{$transaction->switchReversalRef ?? 'N/A'}}</td>
                                            <td>{{$transaction->payerReversalRef ?? 'N/A'}}</td>
                                            <td>{{$transaction->payeeReversalRef ?? 'N/A'}}</td>
                                            <td>{{$transaction->reversal_completedTimestamp ?? 'N/A'}}</td>-->
                                            <td>
                                                <!--<a href="{{ url('transactions/'. $transaction->id)}}" class="btn btn-success btn-sm">View</a>-->
                                                @if(substr($transaction->payerRef,0,3) == '012')
                                                    @if($transaction->transferState == 'COMMITTED' && empty($transaction->reversalState))
                                                        <a href="{{ url('tips/transactions/'. $transaction->id .'/reverse')}}" class="btn btn-primary btn-sm">Reverse</a>
                                                    @endif
                                                @endif
                                                @if(substr($transaction->payerRef,0,3) != '012')
                                                    @if(($transaction->reversalState == 'INPROGRESS' || $transaction->reversalState == 'CONFIRMED_HOLD') && !empty($transaction->reversalReason) && $transaction->holdStatus !="CONFIRMED_WITHDRAW")
                                                        <a href="{{ url('tips/transactions/'. $transaction->id .'/reverseincoming')}}" class="btn btn-warning btn-sm">Incoming Reversal</a>
                                                    @endif
                                                @endif
												
												 <a class="btn btn-primary" title="View More" href="{{ url('tips/view_tips_transactions',[$transaction->id]) }}"><b><i class="fas fa-eye "></i></b></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
								
                     
                     
                            </div>
                        </div>
                        <div class="card-footer text-right">
						{{ $transactions->links() }} 
						</div>
                    </div>
                </div>
            </div>

        </div><!-- sl-pagebody -->
@endsection
