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
            <li class="breadcrumb-item active">Loan Types</li>
        </ol>
    </nav>

    <h5>Loan Types</h5>
    <hr/>



    <div class="card mb-5">
        <div class="card-body">
            <h6>Create Loan Type form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ url('ib/view_loan_type') }}" method="POST">
                @csrf
                @method('POST')
                <div class="row">

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Loan Type name:</label>
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="Enter loan type name here.">
                        </div>
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



    <h5>All Loan Types</h5>
    <hr/>
  
    <hr/>
    <div class="card">
        <div class="card-body">
            <div class=" table-responsive-sm">
                <table class="table table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>Loan type name</th>
                        <th>Description</th>
                        <th>Initiator</th>
                        <th>Approver</th>
                        <th>Approval Status</th>
                        <th>Action</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($loantypes as $loantype)
                        <tr>
                            <td></td>
                            <td width="200px">{{$loantype->name ?? ''}}</td>
                            <td>{{$loantype->description ?? ''}}</td>
                            <td>{{$loantype->initiators->name ?? '--'}}</td>
                            <td>{{$loantype->approvers->name ?? '--'}}</td>
                            <td>
                                @if($loantype->isWaitingApproval == 0)
                                    <span class="badge badge-success">Approved</span>
                                @elseif($loantype->isWaitingApproval == 2)
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-danger">Pending Approval</span>
                                @endif
                                @if($loantype->isDisabled == 0)
                                    <span class="badge badge-success">Active</span>
                                @endif
                                @if($loantype->isDisabled == 2)
                                    <span class="badge badge-danger">Disabled</span>
                                @endif
                            </td>

                             <td width="200px">
                                     <input type="hidden" name="id" value="{{ $loantype->id }}">
									  @if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)
                                    <a href="{{ url('ib/view_loan_type/edit/'. $loantype->id ) }}" class="btn btn-sm btn-primary">
                                        <span><i class="fas fa-edit text-dark-pastel-green"></i></span>
                                    </a>
										@endif
                                     @if(Auth::user()->id != $loantype->initiator_id && $loantype->isWaitingApproval == 1 && $loantype->isDisabled == 0)
                                     <a  href="{{ route('ib.view_loan_type.approve', $loantype->id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-success btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-green"></i></span>
                                     @endif
                                     @if((auth()->user()->id != $loantype->disabledBy_id) && $loantype->isDisabled == 1 && $loantype->isWaitingApproval == 1)
                                  <a  href="{{ route('ib.view_loan_type.disableApproval', $loantype->id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-warning btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-yellow"></i></span>
                                  </a>    
                                    @endif

                                    @if($loantype->isWaitingApproval == 0 && $loantype->isDisabled == 0)
                                        <a title="Disable" href="{{ route('ib.view_loan_type.disable', $loantype->id) }}" name="submit_value" value="disable"  class="btn btn-warning btn-sm"><span><i class="fas fa-remove text-dark-pastel-yellow"></i></span></a>
                                    @endif
                                        @if($loantype->isWaitingApproval == 0 && $loantype->isDisabled == 2)
                                        <a  href="{{ route('ib.view_loan_type.enable', $loantype->id) }}" type="submit" name="submit_value" value="enable"  class="btn btn-secondary btn-sm">
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
            $('.datatable').DataTable({
                "ordering" : false
            });
        });

    </script>
@endsection
@endsection
