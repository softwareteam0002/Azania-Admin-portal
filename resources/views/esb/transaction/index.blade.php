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
            <li class="breadcrumb-item active">ESB Transactions</li>
        </ol>
    </nav>

    <h5>Transaction Filter</h5>
    <hr/>

    <div class="card">
        <div class="card-body">
            <form action="{{url('esb/transaction')}}" method="GET">
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
                        <a href="{{url('esb/transaction')}}" class="btn btn-danger">Clear Filter <i class="fa
                        fa-times-circle-o"></i></a>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <h5>ESB Transactions</h5>
    <hr/>
    <div class="card">
        <div class="card-body">
            <h6 class="mb-3">All transactions</h6>
            <div class="table-responsive">
                <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th width="10"></th>
                        <th>Transaction ID</th>
                        <th>Transaction Channel</th>
                        <th>Name</th>
                        <th>Destination Account</th>
                        <th>Amount</th>
                        <th>Transaction Date</th>
                        <!--   <th>
                            Transaction Response
                        </th> -->
                        <th>Status Description</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($trnxs as $trxn)
                        <tr data-entry-id="{{$trxn->id}}" data-toggle="modal" data-target="#largeModal{{ $trxn->id }}">
                            <td>

                            </td>
                            <td>
                                {{$trxn->transaction_id}}
                            </td>
                            <td>
                                {{$trxn->transaction_channel}}
                            </td>
                            <td>
                                {{$trxn->transaction_name}}
                            </td>
                            <td>
                                @if($trxn->transaction_name == 'UTILITY')
                                    {{$service_ac->account_number ?? 'N/A'}}
                                @else
                                    --nil--
                                @endif
                            </td>
                            <td>
                                {{$trxn->transaction_amount ?? '0'}}
                            </td>
                            <td>
                                {{$trxn->transaction_date}}
                            </td>

                            <!--   <td>
                                        {{$trxn->transaction_response}}
                            </td> -->
                            <td>
                                @if($trxn->transaction_status=='9999')
                                    <h5><span class="badge badge-warning">On Progress</span></h5>

                                @elseif($trxn->transaction_status=='200')
                                    <h5><span class="badge badge-success">Success</span></h5>

                                @else($trxn->transaction_status!='200' || $trxn->transaction_status!='9999')
                                    <h5><span class="badge badge-danger">Failed</span></h5>
                                @endif

                            </td>

                            <td>
                                <a class="btn btn-primary btn-sm"
                                   href="{{ url('esb/view_esb_transactions',[$trxn->id]) }}">
                                    <i class="fas fa-eye text-dark-pastel-green"></i>
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
            $(function () {
                $('.datatable').DataTable({
                    "order": [[1, "desc"]]
                });
            })
        </script>
    @endsection
@endsection