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
            <li class="breadcrumb-item active">Payments</li>
        </ol>
    </nav>


    <h5>Payments</h5>
    <hr/>
    <div class="card">
        <div class="card-body">
            <h6 class="mb-2">All payments</h6>
            <div class="table-responsive-sm">
                <table class="table table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th width="10"></th>
                        <th>Transaction ID</th>
                        <th>Amount</th>
                        <th>Charge</th>
                        <th>Account Number</th>
                        <th>Reference Number</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($payments as $payment)
                        <tr data-entry-id="1" data-toggle="modal" data-target="#largeModal{{$payment->id}}" id="loadingModel" data="{{$payment->id}}">
                            <td>
                            </td>
                            <td>{{ $payment->transactionId }}</td>
                            <td class="text-right">{{number_format($payment->trxAmount, 2)}}</td>
                            <td class="text-right">{{number_format($payment->charge, 2)}}</td>
                            <td>{{$payment->fromAccountID}}</td>
                            <td>
                                @if(strlen($payment->reference_number)>0)
                                    {{$payment->reference_number ?? 'N/A'}}
                                @elseif(strlen($payment->control_number)>0)
                                    {{$payment->control_number ?? 'N/A'}}
                                @elseif(strlen($payment->meter_number)>0)
                                    {{$payment->meter_number ?? 'N/A'}}
                                @elseif(strlen($payment->mobile_number)>0)
                                    {{$payment->mobile_number ?? 'N/A'}}
                                @endif
                            </td>

                            
                            <td>{{$payment->types->name}}</td>
                            <td>{{$payment->created_at}}</td>
                            <td>
                                @if($payment->status->name=="Pending")
                                    <span class="badge badge-danger">   {{$payment->status->name ?? ''}}</span>
                                @endif

                                @if($payment->status->name=="Rejected")
                                    <span class="badge badge-danger">   {{$payment->status->name ?? ''}}</span>
                                @endif

                                @if($payment->status->name=="Sent")
                                    <span class="badge badge-success">   {{$payment->status->name ?? ''}}</span>
                                @endif

                                @if($payment->status->name=="Approved")
                                    <span class="badge badge-success">   {{$payment->status->name ?? ''}}</span>
                                @endif

                                @if($payment->status->name=="Completed")
                                    <span class="badge badge-primary">   {{$payment->status->name ?? ''}}</span>
                                @endif</td>
                             <td>
                                <a class="btn btn-primary btn-sm" href="{{ url('ib/payments/show',[$payment->id]) }}" title="Show">
                                    <span><i class="fas fa-eye text-dark-pastel-green"></i></span>
                                </a>
			                </td>
                        </tr>

                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.datatable').DataTable();
        } );
    </script>
@endsection

@endsection
