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
        <li class="breadcrumb-item"><a href="{{ url('agency/users') }}">Agents</a></li>
        <li class="breadcrumb-item active">Agent Accounts</li>
    </ol>
</nav>

<h5>Agent {{ $agent->agent_full_name }} Accounts</h5>
<hr /> 
<div class="card">
    <div class="card-body">
        <h6>Create agent account form</h6>
        <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
        <form action="{{ url('agency/accounts/store') }}" method="POST">
            @csrf
            @method('POST')
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="name">Bank Account:<span class="text-danger">*</span></label>
                        <input type="text" id="account_number" name="account_number" class="form-control form-control-sm" placeholder="Enter the account here." required>
                        <input type="hidden" name="agent_id" value="{{ $agent->agent_id}}">
                    </div>
                </div>


                <div class="col-md-2">
                    <div class="form-group">
                        <label for="types">Account Type:</label>
                        <select name="account_type_id" class="form-control form-control-sm">
                            <option value="0">Select account type</option>
                            @foreach($accounttypes as $accounttype)
                            <option value="{{ $accounttype->type_id }}">{{ $accounttype->account_type }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <div class="col-md-2">
                    <div class="form-group">
                        <label for="types">Account Status:</label>
                        <select name="account_status" class="form-control form-control-sm">
                            <option value="0">Select account status</option>
                            @foreach($accountstatuses as $accountstatus)
                            <option value="{{ $accountstatus->tbl_status_id}}">{{ $accountstatus->status}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>



                <div class="col-md-12">
                    <hr />
                    <button type="submit" class="btn btn-sm btn-success">Save</button>
                    <button type="reset" class="btn btn-sm btn-link">Reset</button>
                    <a href="{{ url('agency/users') }}" class="btn btn-sm btn-link">Go back</a>
                </div>
            </div>
        </form>

    </div>
</div>


<h5>All agent accounts</h5>
<hr />
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>Bank Account</th>
                        <th>Account Type</th>
                        <th>Account Status</th>
                        <th>Registration Status</th>
                        <th>Initiator</th>
                        <th>Approver</th>
                        <th>Approval Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accounts as $account)
                    <tr>
                        <td></td>
                        <td>{{$account->bank_account}}</td>
                        <td>{{$account->accountType->account_type ?? "--"}}</td>
                        <td>{{$account->accountStatus->status ?? "--"}}</td>
                        <td>{{$account->registrationStatus->status ?? "--"}}</td>
                        <td>{{$account->initiator->name ?? "--" }}</td>
                        <td>{{$account->approver->name ?? "--" }}</td>
                         <td>
                                @if($account->isWaitingApproval == 0)
                                    <span class="badge badge-success">Approved</span>
                                @elseif($account->isWaitingApproval == 2)
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-danger">Pending Approval</span>
                                @endif
                            </td>
                        <td>
						 @if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)
                            <a href="{{ url('agency/accounts/edit', [$account->account_id]) }}" title="Edit" class="btn btn-sm btn-primary">
                                <span><i class="fas fa-edit"></i></span>
                            </a>
							@endif
                            @if($account->initiator_id != Auth::user()->id && $account->isWaitingApproval == 1 && (Auth::user()->action_id == 2 || Auth::user()->action_id == 3))
                            <form action="{{ url('agency/accounts/approve') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="account_id" value="{{ $account->account_id }}">
                                <input type="hidden" name="op" value="1">
                                <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>

                            <form action="{{ url('agency/accounts/approve') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="account_id" value="{{ $account->account_id }}">
                                <input type="hidden" name="op" value="0">
                                <button type="submit" class="btn btn-danger btn-sm" title="Disapprove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                            @endif
                          

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
    $(function() {
        $('.datatable').DataTable({

        })
    })
</script>
@endsection


@endsection