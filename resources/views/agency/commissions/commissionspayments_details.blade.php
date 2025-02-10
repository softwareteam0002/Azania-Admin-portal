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
        <li class="breadcrumb-item active">Agent Commission Payments</li>
    </ol>
</nav>

<h5>Agent Commission Batches</h5>
<hr />

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h6>Generated Batches</h6>
                <p class="small">Select a commission batch:</p>
                <div class="form-group">
                    <select name="batch_id" class="form-control form-control-sm" id="commissionBatchID">
                        @foreach($batches as $batch_)
                        <option value="{{ $batch_->batch_id ?? ''}}">
                            Batch # {{ $batch_->batch_id }}, From : {{$batch_->from_date}} To : {{$batch_->to_date}}
                            @if($batch_->status == 1)
                            [ Initiated ]
                            @elseif($batch_->status == 2)
                            [ Approved - Paid ]
                            @elseif($batch_->status == 3)
                            [ Disapproved ]
                            @else
                            [ nil ]
                            @endif
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <hr />
                    <button onclick="attrClick()" class="btn btn-success btn-sm">Get Commissions</button>
                </div>
            </div>

            <script>
                function attrClick() {
                    var v = $("#commissionBatchID").val();
                    window.location.replace("{{ url('agency/agentcommissions/batch/') }}/" + v);;
                }
            </script>


            <div class="col-md-4">
                <h6>Generate Commission Payments</h6>
                <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
                <form action="{{ url('agency/agentcommissions/generate') }}" method="POST">
                    @csrf
                    @method('POST')


                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">From:</span>
                                            </div>
                                            <input type="date" name="from_date" id='from_date' class="form-control form-control-sm" required>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">To &nbsp;&nbsp;&nbsp;&nbsp;:</span>
                                        </div>
                                        <input type="date" name="to_date" id='to_date' class="form-control form-control-sm" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <hr />
                            <button type="submit" class="btn btn-success btn-sm">Generate</button>
                            <button type="reset" class="btn btn-link btn-sm">Clear form</button>
                        </div>

                    </div>


                </form>
            </div>
        </div>
    </div>
</div>


<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 text-left">
                <p>Initiator:</p>
                <h3 class="mt-n3">{{ $batch->initiator->name }}</h3>
                <br />
                <p class="mt-n3">From Date:</p>
                <h3 class="mt-n3">{{ $batch->from_date }}</h3>
                <br />
                <p class="mt-n3">Status:</p>
                @if($batch->status == 1)
                <h3 class="text-primary mt-n3">Initiated</h3>
                @elseif($batch->status == 2)
                <h3 class="text-success mt-n3">Approved - Paid</h3>
                @elseif($batch->status == 3)
                <h3 class="text-danger mt-n3">Disapproved</h3>
                @else
                <h3 class="text-primary mt-n3">----</h3>
                @endif

            </div>

            <div class="col-md-4  mt-2 text-center">
                <div class="card">
                    <div class="card-body">
                        <h3>Batch#: {{ $batch->batch_id }}</h3>
                        <hr/>
                    @if($batch->initiator_id != Auth::user()->id && $batch->approver_id == null)
                        <form action="{{ url('agency/agentcommissions/batch') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="batch_id" value="{{ $batch->batch_id  ?? ''}}">
                            <input type="hidden" name="op" value="1">
                            <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                        @endif

                        @if($batch->status != 3 && $batch->approver_id == null )
                        <form action="{{ url('agency/agentcommissions/batch') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="batch_id" value="{{ $batch->batch_id  ?? ''}}">
                            <input type="hidden" name="op" value="2">
                            <button type="submit" class="btn btn-danger btn-sm" title="Dis Approve">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                        @endif
                        <form action="{{ url('agency/agentcommissions/batch') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="batch_id" value="{{ $batch->batch_id ?? '' }}">
                            <input type="hidden" name="op" value="4">
                            <button type="submit" class="btn btn-warning btn-sm" title="Export">
                                <i class="fas fa-file-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4 text-right">
                <p>Approver:</p>
                <h3 class="mt-n3">{{ $batch->approver->name ?? '--' }}</h3>
                <br />
                <p class="mt-n3">To Date:</p>
                <h3 class="mt-n3">{{ $batch->to_date }}</h3>
                <br />
                <p class="mt-n3">Issue Date:</p>
                <h3 class="mt-n3">{{  $batch->issued_date ?? '--'}}</h3>
            </div>
        </div>
        <br />


        <h5 class='mt-5'>Commission Distributions</h5>
        <hr/>
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-commission" role="tab" aria-controls="pills-home" aria-selected="true">Commission</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-transactions" role="tab" aria-controls="pills-profile" aria-selected="false">Transaction</a>
            </li>
        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-commission" role="tabpanel" aria-labelledby="pills-home-tab">
                <h6>Distributions</h6>
                <table class="table table-striped table-sm table-hover datatable">
                    <thead>
                        <tr>
                            <th>Agent ID</th>
                            <th>Agent Name</th>
                            <th>Commission Account</th>
                            <th>#. of Transactions</th>
                            <th class="text-right">Amount</th>
                            <th>Posting Date</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr>
                            <td>{{ $payment->agent_id}}</td>
                            <td>{{ $payment->agent['agent_full_name'] ?? '--'}}</td>
                            <td>{{ $payment->commission_account ?? '--'}}</td>
                            <td>{{ $noOfTrans }}</td>
                            <td class="text-right">{{ $total }}</td>

                            <td>{{ $payment->posting_date ?? '--'}}</td>
                        </tr>
                            @php $commission_account = $payment->commission_account; @endphp
                        @endforeach
                    </tbody>
                </table>


            </div>
            <div class="tab-pane fade" id="pills-transactions" role="tabpanel" aria-labelledby="pills-profile-tab">
                <h6>Distribution Details</h6>
                <hr />
                <div class="table-responsive">
                <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Transaction</th>
                            <th>Type</th>
                            <th>Terminal ID</th>
                            <th class="text-right">Agent Amount</th>
                            <th class="text-right">Bank Amount</th>
                            <th class="text-right">Thirdparty Amount</th>
                            <th class="text-right">Total Amount</th>
                            <th>Agent</th>
                            <th>Agent Commision Account</th>
                            <th>Paid</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($commissions as $commision)
                        <tr>
                            <td>{{ $commision->commission_id}}</td>
                            <td>{{ $commision->transactionID}}</td>
                            <td>{{ $commision->transaction->trxn_type}}</td>
                            <td>{{ $commision->transaction->trans_agent_terminal_id}}</td>
                            <td class="text-right">{{ number_format($commision->agent_amount,2) }}</td>
                            <td class="text-right">{{ number_format($commision->bank_amount,2)}}</td>
                            <td class="text-right">{{ number_format($commision->third_party_amount,2)}}</td>
                            <td class="text-right">{{ number_format($commision->commission_amount,2)}}</td>
                            <td>{{ $commision->agent['agent_full_name']}}</td>
                            <td>
                                {{ $commission_account }}
                        
                            </td>
                            <td>
                                @if($commision->is_paid == 1)
                                Yes
                                @elseif($commision->is_paid == 0)
                                No
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
    $(function() {
        $('.datatable').DataTable()
    });
</script>
@endsection
@endsection