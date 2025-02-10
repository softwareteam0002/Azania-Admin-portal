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
            <li class="breadcrumb-item active">Branchs</li>
        </ol>
    </nav>

    <h5>Branches</h5>
    <hr/>

    <div class="card mb-5">
        <div class="card-body">
            <h6>Create Branch form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ url('ib/view_branch') }}" method="POST">
                @csrf
                @method('POST')
                <div class="row">

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Branch name:</label>
                            <input type="text" name="branchName" class="form-control form-control-sm" placeholder="Enter branch name here.">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Branch Code:</label>
                            <input type="text" name="branch_code" class="form-control form-control-sm" placeholder="Enter branch code here.">
                        </div>
                    </div>
                     <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">BIC/Swift Code:</label>
                            <input type="text" name="swift_code" class="form-control form-control-sm" placeholder="Enter BIC/Swift Code here.">
                        </div>
                    </div>
                     <div class="col-md-3">
                            <div class="form-group">
                                <label for="types">Bank:</label>
                                <select name="bank_code" class="form-control  form-control-sm" >
                                    <option value="" >Select Bank</option>
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->bank_code }}" >{{ $bank->name }}</option>
                                    @endforeach
                                </select>
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
                        <th>BIC/Swift Code</th>
                        <th>Bank Code</th>
                        <th>Description</th>
                        <th>Initiator</th>
                        <th>Approver</th>
                        <th>Approval Status</th>
{{--                        <th>Address</th>--}}
                        <th>Action</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($branchs as $branch)

                        <tr>
                            <td></td>
                            <td>{{$branch->branchName ?? 'N/A'}}</td>
                            <td>{{$branch->branch_code ?? 'N/A'}}</td>
                            <td>{{$branch->swift_code ?? 'N/A'}}</td>
                            <td>{{$branch->bank_code ?? 'N/A'}}</td>
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


                                <form action="{{ url('ib/requests/actions') }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <input type="hidden" name="action" value="Edit Branch">
                                    <input type="hidden" name="request_type" value="ib_branch_settings">
                                    <input type="hidden" name="module" value="IB">
                                    <input type="hidden" name="initiator_id" value="{{ $branch->initiator_id }}">
                                    <input type="hidden" name="approver_id" value="{{ Auth::user()->id }}">

                                    <input type="hidden" name="id" value="{{ $branch->id }}">
                                     @if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)
                                    <a href="{{ url('ib/view_branch/edit/'. $branch->id ) }}" class="btn btn-sm btn-primary">
                                        <span><i class="fas fa-edit text-dark-pastel-green"></i></span>
                                    </a>
                                    @endif
                                  
                                </form>
                                  @if((auth()->user()->id != $branch->initiator_id) && $branch->isWaitingApproval == 1 && $branch->isDisabled == 0)
                                  <a  href="{{ route('ib.branch.approve', $branch->id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-success btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-green"></i></span>
                                  </a>    
                                    @endif
				@if((auth()->user()->id != $branch->disabledBy_id) && $branch->isDisabled == 1 && $branch->isWaitingApproval == 1)
                                  <a  href="{{ route('ib.branch.disableApproval', $branch->id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-warning btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-yellow"></i></span>
                                  </a>    
                                    @endif

                                    @if($branch->isWaitingApproval == 0 && $branch->isDisabled == 0)
                                        <a title="Disable" href="{{ route('ib.branch.disable', $branch->id) }}" name="submit_value" value="disable"  class="btn btn-warning btn-sm"><span><i class="fas fa-remove text-dark-pastel-yellow"></i></span></a>
                                    @endif
                                        @if($branch->isWaitingApproval == 0 && $branch->isDisabled == 2)
                                        <a  href="{{ route('ib.branch.enable', $branch->id) }}" type="submit" name="submit_value" value="enable"  class="btn btn-secondary btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-green"></i></span>
                                  </a>    
                                    @endif
                                      <a title="View" class="btn btn-primary btn-sm" href="{{ route('ib.branch.view',[$branch->id]) }}">
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
            $('.datatable').DataTable()
        });

    </script>
@endsection
@endsection

