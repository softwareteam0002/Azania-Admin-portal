@extends('layouts.admin')
@section('content')
    <div class="row mb-3">
        <div class="col-md-8">
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
            <li class="breadcrumb-item active">Transactions Approval Log </li>
        </ol>
    </nav>

    <h5>Transactions Approval Log</h5>
    <hr/>
    <div class="card">
        <div class="card-body">
            <h6 class="">All transactions</h6>
            <hr/>

            <div class="table-responsive">
                <table class="table table-striped table-hover datatable">
                    <thead>
                        <tr>
                            <th width="10"></th>
                            <th>Transaction ID</th>
                            <th>Amount</th>
                            <th>Charge</th>
                            <th>Service Type</th>
                            <th>Sender Account</th>
                            <th>Receiver Account</th>
                            <th>Reference No.</th>
                            <th>Mobile Number</th>
                            <th>Sender</th>
                            <th>Transaction Date</th>
                            <th>Transaction Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($transactionApproveLogs as $trxn)
                        <tr data-entry-id="{{$trxn->id}}" data-toggle="modal" data-target="#largeModal{{ $trxn->id }}">
                            <td></td>
                            <td>{{$trxn->transactionable->transactionId ?? 'N/A'}}</td>
                            <td class="text-right">
                                @if(!empty($trxn->transactionable->trxAmount))
                                {{number_format($trxn->transactionable->trxAmount,2) }}
                                @endif
                            </td>
                            <td class="text-right">
                                @if(!empty($trxn->transactionable->charge))
                                {{number_format($trxn->transactionable->charge,2) ?? 'N/A'}} @endif</td>
                            <td>{{$trxn->transactionable->serviceType ?? 'N/A'}}</td>
                            <td>{{$trxn->transactionable->fromAccountID ?? 'N/A'}}</td>
                            <td>{{$trxn->transactionable->toAccountID ?? 'N/A'}}</td>
                            <td>{{$trxn->transactionable->reference_number ?? 'N/A' }}</td>
                            <td>{{$trxn->transactionable->mobile_number ?? 'N/A'}}</td>
                            @if(!empty($trxn->transactionable->users)) <td>{{ $trxn->transactionable->users->name ?? 'N/A'}}</td> @else <td>{{ 'N/A' }}</td> @endif
                            <td>{{$trxn->transactionable->created_at ?? 'N/A'}}</td>
                            <td>
                               

                                   @if(!empty($trxn->transactionable->status_id) == 2)
                                    <span class="badge badge-success"> Success</span>
                                @elseif(!empty($trxn->transactionable->status_id) == 1)
                                    <span class="badge badge-warning"> On Progress</span>
                                @elseif(!empty($trxn->transactionable->status_id) == 3)
                                    <span class="badge badge-danger"> Completed</span>
                                    @elseif(!empty($trxn->transactionable->status_id) == 5)
                                    <span class="badge badge-danger">Rejected</span>
                                    @elseif(!empty($trxn->transactionable->status_id) == 6)
                                    <span class="badge badge-danger"> Failed</span>
                                @endif


{{--                                @if($trxn->transactionable->status->name=="Rejected")--}}
{{--                                    <span class="badge badge-danger">   {{$trxn->transactionable->status->name ?? ''}}</span>--}}
{{--                                @endif--}}

{{--                                @if($trxn->transactionable->status->name=="Sent")--}}
{{--                                    <span class="badge badge-success">   {{$trxn->transactionable->status->name ?? ''}}</span>--}}
{{--                                @endif--}}

{{--                                @if($trxn->transactionable->status->name=="Approved")--}}
{{--                                    <span class="badge badge-success">   {{$trxn->transactionable->status->name ?? ''}}</span>--}}
{{--                                @endif--}}

{{--                                @if($trxn->transactionable->status->name=="Completed")--}}
{{--                                    <span class="badge badge-primary">   {{$trxn->transactionable->status->name ?? ''}}</span>--}}
{{--                                @endif--}}

                            </td>
                            <td>
                                <form action="{{ url('/ib/transactions/auth_matrix/view_transaction_approval_log') }}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <input name="transactionable_id" type="hidden" value="{{$trxn->transactionable_id}}"/>
                                    <input name="transactionable_type" type="hidden" value="{{$trxn->transactionable_type}}"/>
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-eye"></i></button>
                                </form>
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

