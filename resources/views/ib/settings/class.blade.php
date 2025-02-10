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
            <li class="breadcrumb-item active">Classes</li>
        </ol>
    </nav>

    <h5>Classes</h5>
    <hr/>

    <div class="card mb-5">
        <div class="card-body">
            <h6>Create Classes form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ url('ib/view_class') }}" method="POST">
                @csrf
                @method('POST')
                <div class="row">

                    <div class="col-md-3">
                        <div class="form-group">
                        <label for="name">Class name:</label>
			<select name="name" class="form-control">
                        <option value="0" >Select Class</option>
                        <option value="A" >A</option>
			<option value="B" >B</option>
			<option value="C" >C</option>
                  </select>
                      </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">From Amount</label>
                            <input type="text" name="fromAmount" class="form-control form-control-sm" placeholder="Enter From Amount here.">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">To Amount</label>
                            <input type="text" name="toAmount" class="form-control form-control-sm" placeholder="Enter To Amount here.">
                        </div>
                    </div>

                    <div class="col-md-3">
                       <div class="form-group">
                    <label for="types">Institution Name</label>
                    <select name="institution_id" class="form-control">
                        <option value="0" >Select Institution Name</option>
                        @foreach($institutions as $institution)
                            <option value="{{ $institution->id }}" >{{ $institution->institute_name }}</option>
                        @endforeach
                    </select>
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



    <h5>All Classes</h5>


    <hr/>
    <div class="card">
        <div class="card-body">
            <div class=" table-responsive-sm">
                <table class="table table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th width="10">
                        </th>
                        <th>Class name</th>
                        <th>Start Amount</th>
                        <th>End Amount</th>
                        <th>Institution</th>
                        <th>Initiator</th>
                        <th>Approver</th>
                        <th>Approval Status</th>
                        <th>Action</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($classes as $class)

                        <tr>
                            <td></td>
                            <td>{{$class->class_name ?? ''}}</td>
                            <td>{{$class->fromAmount ?? ''}}</td>
                            <td>{{$class->toAmount ?? ''}}</td>
                            <td>{{$class->institutions->institute_name ?? ''}}</td>
                            <td>{{$class->initiators->name ?? '--' }}</td>
                            <td>{{$class->approvers->name ?? '--'}}</td>
				   <td>
                                @if($class->isWaitingApproval == 0)
                                    <span class="badge badge-success">Approved</span>
                                @elseif($class->isWaitingApproval == 2)
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-danger">Pending Approval</span>
                                @endif
                            </td>
                             <td>


                                 <form action="{{ url('ib/requests/actions') }}" method="POST">
                                     @csrf
                                     @method('PUT')

                                     <input type="hidden" name="action" value="Edit Class">
                                     <input type="hidden" name="request_type" value="ib_class_settings">
                                     <input type="hidden" name="module" value="IB">
                                     <input type="hidden" name="initiator_id" value="{{ $class->initiator_id }}">
                                     <input type="hidden" name="approver_id" value="{{ Auth::user()->id }}">
                                     <input type="hidden" name="id" value="{{ $class->id }}">
                                      @if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)
                                     <a href="{{ url('ib/view_class/edit/'. $class->id ) }}" class="btn btn-sm btn-primary">
                                         <span><i class="fas fa-edit text-dark-pastel-green"></i></span>
                                     </a>
                                      @endif
                                     @if(Auth::user()->id != $class->initiator_id && $class->isWaitingApproval==1 && $class->isDisabled == 0)
                                     <button type="submit" name="submit_value" value="approve" class="btn btn-success btn-sm"><span><i class="fas fa-check text-dark-pastel-green"></i></span></button>
                                     <button type="submit" name="submit_value" value="reject"  class="btn btn-danger btn-sm"><span><i class="fas fa-remove text-dark-pastel-green"></i></span></button>

                                     @endif

                                 </form>
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
