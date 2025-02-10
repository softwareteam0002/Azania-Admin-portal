@extends('layouts.admin')
@section('content')
    <div class="row mb-3">
        <div class="col-md-8">
            <!-- Notifications-->
            @if(session('notification') && session('color'))
                <div class="alert alert-{{ session('color') }} alert-dismissible fade show" role="alert">
                    {{ session('notification') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif


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
            <li class="breadcrumb-item"><a href="{{ url('esb/parishes/index') }}">Church Institutions</a></li>
            <li class="breadcrumb-item active">Assign church institution account</li>
        </ol>
    </nav>


    <h5>Assign church institution account</h5>
    <hr/>


    <div class="card">
        <div class="card-body">
            <h6>Assign church institution account form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ route('esb.verify.acc_inst') }}" method="POST">
                    {!! csrf_field() !!}
                <div class="col-md-4">
                        <div class="form-group">
                            <label for="name">Account Number:<span class="text-danger">*</span></label>
                            @if(session()->has('responseCode_acc')) 
                            <input type="text" id="account_number" name="account_number" value="{{session()->get('accountID')}}"  class="form-control form-control-sm" >
                            @else 
                            <input type="text" id="account_number" name="account_number"  class="form-control form-control-sm" required>

                            @endif
                        </div>
                    </div>
                    <input type="submit" class="btn btn-info btn-sm" name="verify" id="verify" value="Verify"/>  
                </form>  
            <form action="{{ route('esb.churchinstitution.addaccount') }}" method="POST">
                @csrf
                @method('POST')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Institution Account Name:</label>
                                @if(session()->has('responseCode_acc')) 
                            <input type="text" id="institute_name" name="institute_name" class="form-control form-control-sm" value="{{ old('clientName', session()->has('clientName') ? session()->get('clientName') : '') }}" placeholder="Enter institution name here." readonly>
                            @else
                            <input type="text" id="institute_name" name="institute_name" class="form-control form-control-sm" value="{{ old('clientName', session()->has('clientName') ? session()->get('clientName') : '') }}" placeholder="Enter institution name here.">
                            @endif
                        </div>
                    </div>
					<input type="hidden" id="institute_name" name="institute_id" class="form-control form-control-sm" value="{{$institution->id}}">
					<div class="col-md-6">
                    <div class="form-group">
                        <label for="types">Is Default Account?:<span class="text-danger"></span></label>
                        <select name="isDefaulAccount" class="form-control form-control-sm">
                           <option value="yes">Yes</option>
							<option value="no" selected>No</option>
                        </select>
                    </div>
                </div>
					
					<div class="col-md-12">
                        <h6 class="pt-6"><strong>Sadaka Transaction Types</strong></h6>
                                <hr />
                                <div class="row pt-6">
                        @foreach($sadakatrantypes as $sadakatrantype)
                           
                            <div class="col-md-6 role-list">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="sadakatrantypes[]" value="{{ $sadakatrantype->id}}">
                                    <label class="form-check-label">{{ $sadakatrantype->name ?? '--' }}</label>
                                </div>
                            </div>                           
                        @endforeach
                    </div>
                        </div>
                    <div class="col-md-12">
                        <hr/>
                        <button type="submit" class="btn btn-success btn-sm">{{ trans('global.save') }}</button>
                        <button type="reset" class="btn btn-link btn-sm">Clear form</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <h5>Internet Banking accounts</h5>
    <hr/>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th width="10"></th>
                        <th>Account Number</th>
                        <th>Account Name</th>
                        <th>Transaction Types</th>
						<th>Transaction Code</th>
						<th>Approval Status</th>
                        <th>Initiator</th>
                        <th>Approver</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($institution_accounts as $trxn)
					{{$trxn->isWaitingApproval}}
                        <tr data-entry-id="{{$trxn->id}}" data-toggle="modal" data-target="#largeModal{{ $trxn->id }}">
                            <td></td>
                            <td>{{$trxn->account_number}}</td>
                            <td>{{$trxn->account_name}}</td>
                            <td>{{$trxn->type->name ?? ''}}</td>
							<td>{{$trxn->type->code ?? ''}}</td>
							<td>
							
                                @if($trxn->isWaitingApproval == 0)
                                    <span class="badge badge-success">Approved</span>
                                @elseif($trxn->isWaitingApproval == 2)
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-danger">Pending Approval</span>
                                @endif
                            </td>
                            <td>{{ $trxn->initiator->name ?? '--' }}</td>
                            <td>{{ $trxn->approver->name ?? '--'}}</td>
                            <td>
							<form action="{{ url('esb/parishes/account/delete') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="parish_account_id" value="{{  $trxn->id }}">
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
							@if(Auth::user()->id != $trxn->initiator_id && $trxn->isWaitingApproval==1)
                                         <a  href="{{ route('esb.churchinstitution.account.approve', $trxn->id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-success btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-green"></i></span>
                                  </a>
                                        @endif
                               <!-- <a class="btn btn-primary btn-sm" href="{{ url('ib/accounts/edit',[$trxn->id]) }}">
                                    <i class="fas fa-edit text-dark-pastel-green"></i> Edit
                                </a>-->
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
