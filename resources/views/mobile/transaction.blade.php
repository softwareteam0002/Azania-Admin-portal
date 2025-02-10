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
    <hr />
</div>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb small" style="background-color:#f9f9f9; padding: .55rem 1rem;">
        <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
        <li class="breadcrumb-item active">Mobile Banking Transactions</li>
    </ol>
</nav>

<h5>Mobile Banking Transactions</h5>
<hr />

<div class="card">
    <div class="card-body">
        <h6 class="mb-3">All transactions</h6>
        <div class="table-responsive">
            <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Transaction Channel</th>
                        <th>Name</th>
                        <th>Amount</th>
                        <th>Transaction Date</th>
                        <th>Status Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trnxs as $trxn)
                    <tr data-entry-id="{{$trxn->id}}" data-toggle="modal" data-target="#largeModal{{ $trxn->id }}">
                        <td>{{$trxn->transaction_id}}</td>
                        <td>{{$trxn->transaction_channel}}</td>
                        <td>{{$trxn->transaction_name}}</td>
                        <td>{{number_format($trxn->transaction_amount,2) ?? '0.00'}}</td>
                        <td>{{$trxn->transaction_date}}</td>

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
                            @can('mb_view_transactions_details')
                                <form action="{{ url('mb/view_mb_transactions') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="transaction_id" value="{{ $trxn->id }}">
                                    <button type="submit" class="btn btn-primary btn-sm" title="Activate">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>




@section('scripts')
@parent
<script>
    $(function() {


        $('.datatable').DataTable({
            "order": [
                [1, "desc"]
            ]
        });
    })
</script>
@endsection
@endsection