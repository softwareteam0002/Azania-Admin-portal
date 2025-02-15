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
        <li class="breadcrumb-item active">Service Accounts</li>
    </ol>
</nav>

<h5>Service Accounts</h5>
<hr />
<div class="card mb-5">
    <div class="card-body">
        <h6>Create Service Account form</h6>
        <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
        <form action="{{ url('agency/account/store') }}" method="POST">
            @csrf
            @method('POST')
            <div class="row">

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Service:<span class="text-danger">*</span></label>
                        <select name="bank_service_ID" id="" class="form-control form-control-sm">
                            <option value="0">Select service here</option>
                            @foreach($services as $service)
                            <option value="{{ $service->agent_serviceID}}">{{ $service->agent_serviceName }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
				
				<div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Bank Agency Expense:</label>
                        <input type="text" name="agency_expenses_deposit" class="form-control form-control-sm" placeholder="Enter agency expense here.">
                    </div>
                </div>
				
				<!--<div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Disbursement Account:</label>
                        <input type="text" name="disbursement_account" class="form-control form-control-sm" placeholder="Enter disbursement account here.">
                    </div>
                </div>
				
				<div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Agency Deposit Commission Account:</label>
                        <input type="text" name="agency_deposit_commision_account" class="form-control form-control-sm" placeholder="Enter agency commission deposit account here.">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Bank Agency Commision Account:</label>
                        <input type="text" name="agency_commision_account" class="form-control form-control-sm" placeholder="Enter bank agency commision account here.">
                    </div>
                </div>
				
				<div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Agency Payable Commision Account:</label>
                        <input type="text" name="agency_payable_commision_account" class="form-control form-control-sm" placeholder="Enter agency payable commision account here.">
                    </div>
                </div>
       


				
				<div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Collection Account:</label>
                        <input type="text" name="colection_account" class="form-control form-control-sm" placeholder="Enter agency collection account here.">
                    </div>
                </div>-->

     

                <div class="col-md-12">
                    <hr />
                    <button type="submit" class="btn btn-success btn-sm">Save</button>
                    <button type="reset" class="btn btn-link btn-sm">Clear form</button>
                </div>

            </div>



        </form>
    </div>
</div>


<h5>Registered Service accounts</h5>
<hr />
<div class="card">
    <div class="card-body">
        <div class=" table-responsive">
            <table class="table table-responsive-sm table-sm table-hover datatable">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>Service</th>
                      
                        <!--<th>Agency Commision Account</th>
                        <th>Agency Expense Deposit</th>-->
						
						<th>Bank Agency Expense</th>
                      
                        <th>Initiator</th>
                        <th>Approver</th>
						<th>Status</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($accounts as $account)
                    <tr>
                        <td></td>
                        <td>{{ $account->services->agent_serviceName ?? ''}}</td>
                    
                        <!--<td>{{ $account->agency_commision_account ?? ''}}</td>-->
               
                        <td>{{ $account->agency_expenses_deposit ?? ''}}</td>
                 
                        <td>{{ $account->initiator->name ?? '--'}}</td>
                        <td>{{ $account->approver->name ?? '--'}}</td>
						<td>
                            @if($account->isWaitingApproval == 0)
                                    <span class="badge badge-success">Approved</span>
                                @elseif($account->isWaitingApproval == 2)
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-danger">Pending Approval</span>
                            @endif
                            <!--<span class="badge badge-success">Active</span>-->
                        </td>
                        <td>
                            <a title="Edit Account" href="{{ url('agency/account/service/edit/'.$account->bank_account_id) }}" class="btn btn-sm btn-primary">
                                <span><i class="fas fa-edit"></i></span>
                            </a>

                            @if($account->initiator_id != Auth::user()->id && $account->approver_id == null && (Auth::user()->action_id == 2 || Auth::user()->action_id == 3))
                                <form action="{{ url('agency/account/approve') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="account_id" value="{{ $account->bank_account_id }}">
                                    <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                        <i class="fas fa-check"></i>
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
        $('.datatable').DataTable()
    });
</script>
@endsection

@endsection