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
        <li class="breadcrumb-item active">Institution Accounts</li>
    </ol>
</nav>

<h5>Institution Accounts</h5>
<hr />
<div class="card mb-5">
    <div class="card-body">
        <h6>Create Institution Account form</h6>
        <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
        <form action="{{ url('ib/institutionaccounts/store') }}" method="POST">
            @csrf
            @method('POST')
            <div class="row">

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Service:<span class="text-danger">*</span></label>
                        <select name="account_type_id" id="" class="form-control form-control-sm">
                            <option value="0">Select Account Type</option>
                            @foreach($account_types as $type)
                            <option value="{{ $type->id}}">{{ $type->name }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Account Number:</label>
                        <input type="text" name="account_number" class="form-control form-control-sm" placeholder="Enter account number here.">
                    </div>
                </div>
                <div class="col-md-12">
                    <hr />
                    <button type="submit" class="btn btn-success btn-sm">Save</button>
                    <button type="reset" class="btn btn-link btn-sm">Clear form</button>
                </div>

            </div>



        </form>
    </div>
</div>


<h5>Registered Institution accounts</h5>
<hr />
<div class="card">
    <div class="card-body">
        <div class=" table-responsive">
            <table class="table table-responsive-sm table-sm table-hover datatable">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>Type</th>
                        <th>Account Number</th>
                       
                        <th>Initiator</th>
                        <th>Approver</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($accounts as $account)
                    <tr>
                        <td></td>
                        <td>{{ $account->accountType->name ?? ''}}</td>
                        <td>{{ $account->account_number ?? ''}}</td>
                        <td>{{ $account->initiator->name ?? '--'}}</td>
                        <td>{{ $account->approver->name ?? '--'}}</td>

                        <td>
						 @if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)
                            <a title="Edit Account" href="{{ url('ib/institutionaccounts/edit/'.$account->id) }}" class="btn btn-sm btn-primary">
                                <span><i class="fas fa-edit"></i></span>
                            </a>
							@endif
                            @if($account->initiator_id != Auth::user()->id && $account->approver_id == null && (Auth::user()->action_id == 2 || Auth::user()->action_id == 3))
                                <form action="{{ url('ib/institutionaccounts/approve') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="account_id" value="{{ $account->id }}">
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