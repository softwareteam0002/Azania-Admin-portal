@extends('layouts.admin')
@section('content')
    <div class="row mb-3">
        <div class="col-md-8">
            <!-- Notifications-->
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{$error}}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

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

            @if(isset($notification) && isset($color))
                <div class="alert alert-{{ $color }} alert-dismissible fade show" role="alert">
                    {{ $notification }}
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
            <li class="breadcrumb-item active">Transactions</li>
        </ol>
    </nav>
    <h5>Transaction Filter</h5>
    <hr/>
    <div class="card">
        <div class="card-body">
            <form action="{{url('ib/transactions')}}" method="GET">
                <div class="row">
                    <div class="col-4 form-group">
                        <label for="account">Transaction ID:</label>
                        <input type="text" name="transaction_id" class="form-control">
                    </div>
                    <div class="col-4 form-group">
                        <label for="account">From Date: </label>
                        <input type="date" name="start_date" class="form-control">
                    </div>
                    <div class="col-4 form-group">
                        <label for="account">To Date: </label>
                        <input type="date" name="end_date" class="form-control">
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12 form-group mg-t-8">
                        <button type="submit" class="btn btn-primary">Filter <i class="fa fa-filter"></i></button>
                        <a href="{{url('ib/transactions')}}" class="btn btn-danger">Clear Filter <i class="fa
                        fa-times-circle-o"></i></a>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <h5>Transactions</h5>
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
                    @foreach($trxns as $trxn)
                        <tr data-entry-id="{{$trxn->id}}" data-toggle="modal" data-target="#largeModal{{ $trxn->id }}">
                            <td></td>
                            <td>{{$trxn->transactionId}}</td>
                            <td class="text-right">{{number_format($trxn->trxAmount,2)}}</td>
                            <td>{{$trxn->serviceType}}</td>
                            <td>{{$trxn->fromAccountID}}</td>
                            <td>{{$trxn->toAccountID ?? 'N/A'}}</td>
                            <td>{{$trxn->reference_number ?? 'N/A' }}</td>
                            <td>{{$trxn->mobile_number ?? 'N/A'}}</td>
                            @if($trxn->users)
                                <td>{{ $trxn->users->name}}</td>
                            @else
                                <td>{{ 'N/A' }}</td>
                            @endif
                            <td>{{$trxn->created_at}}</td>
                            <td>
                                @if($trxn->responseCode == "200" || $trxn->responseCode ==200)
                                    <span class="badge badge-success"> Success</span>
                                @elseif($trxn->status_id == 1 && is_null($trxn->responseCode))
                                    <span class="badge badge-warning"> On Progress</span>
                                @else
                                    <span class="badge badge-danger"> Failed</span>

                                @endif
                            </td>
                            <td>
                                <a class="btn btn-primary" title="View More"
                                   href="{{ url('ib/view_ib_transactions',[$trxn->id]) }}"><b><i
                                            class="fas fa-eye "></i></b></a>
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
            $(document).ready(function () {
                $('.datatable').DataTable();
            });
        </script>
    @endsection
@endsection
