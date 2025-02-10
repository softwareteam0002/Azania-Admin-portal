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
            <li class="breadcrumb-item active">Branches</li>
        </ol>
    </nav>

    <h5>Branches</h5>
    <hr/>

    <div class="card mb-5">
        <div class="card-body">
            <h6>Create Branch form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ url('agency/view_branch') }}" method="POST">
                @csrf
                @method('POST')
                <div class="row">

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Branch name:</label>
                            <input type="text" name="branchName" class="form-control form-control-sm" placeholder="Enter bank name here.">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Branch Code:</label>
                            <input type="text" name="branch_code" class="form-control form-control-sm" placeholder="Enter bank code here.">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Description:</label>
                            <textarea name="description" class="form-control form-control-sm" id="" cols="30" rows="4" placeholder="Enter descriton here."></textarea>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Address:</label>
                            <textarea name="address" class="form-control form-control-sm" id="" cols="30" rows="4" placeholder="Enter address here."></textarea>
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



    <h5>All branches</h5>
    <hr/>

    <div class="card">
        <div class="card-body">
            <div class=" table-responsive-sm">
                <table class="table table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th width="10">
                        </th>
                        <th>Branch name</th>
                        <th>Branch Code</th>
                        <th>Description</th>
                        <th>Initiator</th>
                        <th>Approver</th>
                        <th>Approval Status</th>
                        <th>Action</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($branchs as $branch)

                        <tr>
                            <td></td>
                            <td>{{$branch->branchName ?? 'N/A'}}</td>
                            <td>{{$branch->branch_code ?? 'N/A'}}</td>
                            <td>{{$branch->description ?? 'N/A'}}</td>
                            <td>{{$branch->initiators->name ?? '--'}}</td>
                            <td>{{$branch->approvers->name ?? '--'}}</td>
{{--                            <td>{{$branch->address ?? 'N/A'}}</td>--}}
                            <td>
                                @if($branch->isWaitingApproval == 0)
                                    <span class="badge badge-success">Approved</span>
                                @elseif($branch->isWaitingApproval == 2)
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-danger">Pending Approval</span>
                                @endif
                                 @if($branch->isDisabled == 0 && $branch->status == 'Active')
                                    <span class="badge badge-success">Active</span>
                                @endif
                                @if($branch->isDisabled == 2 && $branch->status == 'Disabled')
                                    <span class="badge badge-danger">Disabled</span>
                                @endif
                            </td>
                            <td>
								<form action="{{ url('agency/requests/actions') }}" method="POST" class="d-inline">
									@csrf
									@method('PUT')
									<input type="hidden" name="action" value="Edit Branch">
									<input type="hidden" name="request_type" value="ab_branch_settings">
									<input type="hidden" name="module" value="AB">
									<input type="hidden" name="initiator_id" value="{{ $branch->initiator_id }}">
									<input type="hidden" name="approver_id" value="{{ Auth::user()->id }}">
									<input type="hidden" name="id" value="{{ $branch->id }}">

									@if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)
									<a href="{{ url('agency/view_branch/edit/'. $branch->id) }}" class="btn btn-sm btn-primary d-inline-block">
										<i class="fas fa-edit text-dark-pastel-green"></i>
									</a>
									@endif

									@if(Auth::user()->id != $branch->initiator_id && $branch->isWaitingApproval == 1 && $branch->isDisabled == 0)
									<a href="{{ route('agency.branch.approve', $branch->id) }}" class="btn btn-success btn-sm d-inline-block">
										<i class="fas fa-check text-dark-pastel-green"></i>
									</a>
									@endif
								</form>

								@if($branch->isWaitingApproval == 0 && $branch->isDisabled == 0)
								<a href="{{ route('agency.branch.disable', $branch->id) }}" class="btn btn-danger btn-sm d-inline-block" title="Disable">
									<i class="fas fa-lock text-dark-pastel-green"></i>
								</a>
								@endif

								<a href="{{ route('agency.branch.view', [$branch->id]) }}" class="btn btn-primary btn-sm d-inline-block" title="View">
									<i class="fas fa-eye text-dark-pastel-green"></i>
								</a>

								@if(auth()->user()->id != $branch->disabledBy_id && $branch->isDisabled == 1 && $branch->isWaitingApproval == 1)
								<a href="{{ route('agency.branch.disableApproval', $branch->id) }}" class="btn btn-warning btn-sm d-inline-block">
									<i class="fas fa-lock text-dark-pastel-danger"></i>
								</a>
								@endif

								@if($branch->isDisabled == 2)
								<a href="{{ route('agency.branch.enable', $branch->id) }}" class="btn btn-secondary btn-sm d-inline-block">
									<i class="fas fa-unlock text-dark-pastel-green"></i>
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

