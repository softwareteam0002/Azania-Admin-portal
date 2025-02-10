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
            <li class="breadcrumb-item active">Banks</li>
        </ol>
    </nav>

    <h5>Banks</h5>
    <hr/>

    <div class="card mb-5">
        <div class="card-body">
            <h6>Create Bank form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ url('ib/view_bank') }}" method="POST">
                @csrf
                @method('POST')
                <div class="row">

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Bank name:</label>
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="Enter bank name here.">
                        </div>
                    </div>
                     <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Bank Code:</label>
                            <input type="text" name="bank_code" class="form-control form-control-sm" placeholder="Enter bank code here.">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">BIC/Swift Code:</label>
                            <input type="text" name="swift_code" class="form-control form-control-sm" placeholder="Enter swift code here.">
                        </div>
                    </div>
                     <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Short Name:</label>
                            <input type="text" name="shortName" class="form-control form-control-sm" placeholder="Enter bank short name here.">
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



    <h5>All banks</h5>


    <hr/>
    <div class="card">
        <div class="card-body">
            <div class=" table-responsive-sm">
                <table class="table table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th width="10">
                        </th>
                        <th>Bank name</th>
                        <th>Bank Code</th>
                        <th>BIC/Swift Code</th>
                        <th>Description</th>
                        <th>Address</th>
                        <th>Initiator</th>
                        <th>Approver</th>
                        <th>Approval Status</th>
                        <th>Action</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($banks as $bank)

                        <tr>
                            <td></td>
                            <td>{{$bank->name ?? ''}}</td>
                            <td>{{$bank->bank_code ?? ''}}</td>
                            <td>{{$bank->swift_code ?? ''}}</td>
                            <td>{{$bank->description ?? ''}}</td>
                            <td>{{$bank->address ?? ''}}</td>
                            <td>{{$bank->initiators->name ?? '--' }}</td>
                            <td>{{$bank->approvers->name ?? '--'}}</td>
				   <td>
                                @if($bank->isWaitingApproval == 0)
                                    <span class="badge badge-success">Approved</span>
                                @elseif($bank->isWaitingApproval == 2)
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-danger">Pending Approval</span>
                                @endif
				@if($bank->isDisabled == 0)
                                    <span class="badge badge-success">Active</span>
                                @endif
                                @if($bank->isDisabled == 2)
                                    <span class="badge badge-danger">Disabled</span>
                                @endif

                            </td>
                             <td>


                                 <form action="{{ url('ib/requests/actions') }}" method="POST">
                                     @csrf
                                     @method('PUT')

                                     <input type="hidden" name="action" value="Edit Bank">
                                     <input type="hidden" name="request_type" value="ib_bank_settings">
                                     <input type="hidden" name="module" value="IB">
                                     <input type="hidden" name="initiator_id" value="{{ $bank->initiator_id }}">
                                     <input type="hidden" name="approver_id" value="{{ Auth::user()->id }}">
                                     <input type="hidden" name="id" value="{{ $bank->id }}">
                                     @if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)

                                     <a href="{{ url('ib/view_bank/edit/'. $bank->id ) }}" class="btn btn-sm btn-primary">
                                         <span><i class="fas fa-edit text-dark-pastel-green"></i></span>
                                     </a>
                                      @endif
                                     @if(Auth::user()->id != $bank->initiator_id && $bank->isWaitingApproval==1 && $bank->isDisabled == 0)
                                     <a  href="{{ route('ib.bank.approve', $bank->id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-success btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-green"></i></span>
                                     @endif

                                 </form>
                                @if((auth()->user()->id != $bank->disabledBy_id) && $bank->isDisabled == 1 && $bank->isWaitingApproval == 1)
                                  <a  href="{{ route('ib.bank.disableApproval', $bank->id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-warning btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-yellow"></i></span>
                                  </a>    
                                    @endif
                                 
                                  @if($bank->isWaitingApproval == 0 && $bank->isDisabled == 0)
                                        <a title="Disable" href="{{ route('ib.bank.disable', $bank->id) }}" name="submit_value" value="disable"  class="btn btn-warning btn-sm"><span><i class="fas fa-remove text-dark-pastel-green"></i></span></a>
                                    @endif
                                     @if($bank->isWaitingApproval == 0 && $bank->isDisabled == 2)
                                        <a title="Enable" href="{{ route('ib.bank.enable', $bank->id) }}" type="submit" name="submit_value" value="enable"  class="btn btn-secondary btn-sm">
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
