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
            <li class="breadcrumb-item active">MNO`s</li>
        </ol>
    </nav>

    <h5>MNO`s</h5>
    <hr/>

    <div class="card mb-5">
        <div class="card-body">
            <h6>Create MNO form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ url('ib/view_mno') }}" method="POST">
                @csrf
                @method('POST')
                <div class="row">

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">MNO name:</label>
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="Enter MNO name here.">
                        </div>
                    </div>
					<div class="col-md-3">
						<label for="mno_id">Type:<span
								class="text-danger text-rlg text-bolder">*</span></label>
						<select name="type_name" class="form-control input-sm" required>
							<option value="" selected>Choose Type</option>
							<option value="ISP">Internet Service Provider (ISP)</option>
							<option value="TOPUP">Airtime Topup</option>
						</select>
					</div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Description:</label>
                            <textarea name="description" class="form-control form-control-sm" id="" cols="30" rows="4" placeholder="Enter descriton here."></textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <hr/>
                        <button type="submit" class="btn btn-success btn-sm">Save</button>
                        <button type="reset" class="btn btn-link btn-sm">Clear form</button>
                    </div>

                </div>



            </form>
        </div>
    </div>



    <h5>All MNO`s</h5>
    <hr/>

    <div class="card">
        <div class="card-body">
            <div class=" table-responsive-sm">
                <table class="table table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>MNO name</th>
						<th>Type</th>
                        <th>Description</th>
                        <th>Initiator</th>
                        <th>Approver</th>
                        <th>Approval Status</th>
                        <th>Action</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($mnos as $mno)
                        <tr>
                            <td></td>
                            <td>{{$mno->name ?? ''}}</td>
							<td>{{$mno->type ?? ''}}</td>
                            <td>{{$mno->description ?? ''}}</td>
                            <td>{{$mno->initiators->name ?? '--' }}</td>
                            <td>{{$mno->approvers->name ?? '--'}}</td>
                            <td>
                                @if($mno->isWaitingApproval == 0)
                                    <span class="badge badge-success">Approved</span>
                                @elseif($mno->isWaitingApproval == 2)
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-danger">Pending Approval</span>
                                @endif
				@if($mno->isDisabled == 0)
                                    <span class="badge badge-success">Active</span>
                                @endif
                                @if($mno->isDisabled == 2)
                                    <span class="badge badge-danger">Disabled</span>
                                @endif

                            </td>
                             <td>
                                 <form action="{{ url('ib/requests/actions') }}" method="POST">
                                     @csrf
                                     @method('PUT')

                                     <input type="hidden" name="request_type" value="ib_mno_settings">
                                     <input type="hidden" name="id" value="{{ $mno->id }}">
				                     @if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)
                                    <a title="Edit" href="{{ url('ib/view_mno/edit/'. $mno->id ) }}" class="btn btn-sm btn-primary">
                                        <span><i class="fas fa-edit text-dark-pastel-green"></i></span>
                                    </a>
                                      @endif
                                     @if(Auth::user()->id != $mno->initiator_id && $mno->isWaitingApproval == 1 && $mno->isDisabled == 0)
                                    <a  href="{{ route('ib.mno.approve', $mno->id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-success btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-green"></i></span>
                                     @endif
                                 </form>
                                    @if((auth()->user()->id != $mno->disabledBy_id) && $mno->isDisabled == 1 && $mno->isWaitingApproval == 1)
                                  <a  href="{{ route('ib.mno.disableApproval', $mno->id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-warning btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-yellow"></i></span>
                                  </a>    
                                    @endif

                                        @if($mno->isWaitingApproval == 0 && $mno->isDisabled == 0)
                                        <a title="Disable" href="{{ route('ib.mno.disable', $mno->id) }}" name="submit_value" value="disable"  class="btn btn-warning btn-sm"><span><i class="fas fa-remove text-dark-pastel-green"></i></span></a>
                                    @endif
                                     @if($mno->isWaitingApproval == 0 && $mno->isDisabled == 2)
                                        <a title="Enable" href="{{ route('ib.mno.enable', $mno->id) }}" type="submit" name="submit_value" value="enable"  class="btn btn-secondary btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-green"></i></span>
                                  </a>    
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
        $(function () {
            $('.datatable').DataTable()
        });

    </script>
@endsection
@endsection
