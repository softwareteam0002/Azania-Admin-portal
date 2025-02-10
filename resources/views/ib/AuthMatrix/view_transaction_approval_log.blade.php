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
            <li class="breadcrumb-item"><a href="{{ url('ib/view_transactions_approval_log') }}">Transactions Approval Log </a></li>
            <li class="breadcrumb-item active">View Transaction Approval Log </li>
        </ol>
    </nav>

    <h5>Transaction Approval Log </h5>
    <hr/>

    <div class="card mb-5">
        <div class="card-body">
            <div class ="row">
                <div class="col-md-6 matrix-role-data">
                                <h6>Matrix Role Applied : {{ $matrixRole->name }}</h6>
                                <dl>
                                    <dt>Is Sequencial</dt>
                                    <dd>- <span >@if($matrixRole->is_sequencial == 1) {{ 'YES' }} @else {{ 'NO' }} @endif </span></dd>
                                    <dt>Is Range</dt>
                                    <dd>- <span >@if($matrixRole->is_range == 1) {{ 'YES' }} @else {{ 'NO' }} @endif </span</dd>
                                    <dt>Is Any Of</dt>
                                    <dd>- <span >@if($matrixRole->is_any_of == 1) {{ 'YES' }} @else {{ 'NO' }} @endif </span</dd>
                                    <dt>Signatories</dt>
                                    <dd>- <span >{{ $matrixRole->signatories }}</span</dd>
                                    <dt>Min Amount</dt>
                                    <dd>- <span >{{ $matrixRole->min_amount }}</span</dd>
                                    <dt>Max Amount</dt>
                                    <dd>- <span >{{ $matrixRole->max_amount }}</span</dd>
                                </dl>     
                            </div>
                            <div class="col-md-6">
                                <div class="table-responsive">
                                        <table class="table table-bordered">
                                        <thead>
                                            <tr style="background-color: #f9f9f9;">
                                                <th class="text-left" width="30%">Approvers</th>
                                                <th class="text-left" width="30%">Approve Status</th>
                                                <th class="text-left" width="30%">Approve Comment</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($transactionApproveLogs as $user)
                                                <tr class="approver-1">
                                                    <td>
                                                        {{ $user->user->name }} &nbsp - &nbsp @if($user->my_turn == 4) <span class="badge badge-pill badge-info">approved previously</span> @elseif($user->my_turn == 0) <span class="badge badge-pill badge-info">waiting</span> @elseif($user->my_turn == 1) <span class="badge badge-pill badge-danger">pending</span> @elseif($user->my_turn == 2) <span class="badge badge-pill badge-success">already work on it</span> @elseif($user->my_turn == 3) <span class="badge badge-pill badge-warning">previous rejected</span> @endif
                                                    </td>
                                                    <td>
                                                        @if(is_null($user->approve_status))
                                                            <span class="badge badge-pill badge-info">waiting to respond</span>
                                                        @elseif($user->approve_status == 1)
                                                            <span class="badge badge-pill badge-success">approved</span>
                                                        @elseif($user->approve_status == 0)
                                                            <span class="badge badge-pill badge-info">disapproved</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(is_null($user->approve_comment))
                                                            N/A
                                                        @else
                                                            {{ $user->approve_comment }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        </table>
                                </div>
                        </div>
            </div>
        </div>
    </div>
@section('scripts')
    @parent
    <script>
        $(function () {
            $('.datatable').DataTable();
        });
    </script>
@endsection
@endsection

