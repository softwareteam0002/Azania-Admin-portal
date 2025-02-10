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

            <!-- Create Action Notifications -->
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    @foreach($errors->all() as $error)
                        <li class="list-group-item">
                            {{ $error }}
                        </li>
                    @endforeach
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

            <li class="breadcrumb-item active">Church Institutions</li>
        </ol>
    </nav>


    <h5>Church Institutions</h5>
    <hr/>
    <div class="card mb-5">
        <div class="card-body">
            <h6>Create Instution form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
               <form action="{{ route('ib.verify.acc_inst') }}" method="POST">
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
            <form action="{{ route('ib.churchinstitution.store') }}" method="POST">
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
                   <div class="col-md-6">
                        <div class="form-group">
                            <label for="prefix">Church Institution Prefix:</label>
                                @if(session()->has('responseCode_acc')) 
                            <input type="text" id="institute_prefix" name="institute_prefix" class="form-control form-control-sm" value="{{ old('prefix', session()->has('prefix') ? session()->get('prefix') : '') }}" placeholder="Enter institution prefix here.">
                            @else
                            <input type="text" id="institute_prefix" name="institute_prefix" class="form-control form-control-sm" value="{{ old('prefix', session()->has('prefix') ? session()->get('prefix') : '') }}" placeholder="Enter institution prefix here.">
                            @endif
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

    <h5>All registered Church Institutions</h5>
    <hr/>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th width="10"></th>
                        <th>Institution Account Name</th>
                        <th>Institution Prefix</th>
                        <th>Account Number</th>
                        <th>Approval Status</th>
                        <th>Initiator</th>
                        <th>Approver</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($churchinstitutions as $institution)
                        <tr>
                            <td>{{ $institution->id }}</td>
                            <td>{{$institution->accountName}}</td>
                            <td>{{$institution->prefix}}</td>
                            <td>{{$institution->account_number}}</td>
                            
                            <td>
                                @if($institution->isWaitingApproval == 0)
                                    <span class="badge badge-success">Approved</span>
                                @elseif($institution->isWaitingApproval == 2)
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-danger">Pending Approval</span>
                                @endif
                            </td>
                            <td>{{ $institution->initiators->name ?? '--' }}</td>
                            <td>{{ $institution->approvers->name ?? '--'}}</td>
                            <td>

                                <!--- Added Button for Payment Service Activation-->
                                @if($institution->status_id!=1)
                                <form action="{{ url('ib/institution/activate') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="institution_id" value="{{ $institution->id }}">
                                    <input type="hidden" name="status_id" value="1">
                                     
                                    <a title="Edit" class="btn btn-primary btn-sm" href="{{ url('ib/parishes/edit',[$institution->id]) }}"><i class="fas fa-edit text-dark-pastel-green"></i> </a>
                                    
                               
                                </form>
                                  <form action="{{ url('ib/requests/actions') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="id" value="{{ $institution->id }}">
                                        <input type="hidden" name="approver_id" value="{{ Auth::user()->id }}">
                                        <input type="hidden" name="request_type" value="ib_institutions">
                                        @if(Auth::user()->id != $institution->initiator_id && $institution->isWaitingApproval==1)
                                           <a  href="{{ route('ib.churchinstitution.approve', $institution->id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-success btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-green"></i></span>
                                  </a>
                                        @endif
                                    </form>

                                 @else
                                    


                                    <form action="{{ url('ib/requests/actions') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="id" value="{{ $institution->id }}">
                                        <input type="hidden" name="approver_id" value="{{ Auth::user()->id }}">
                                        <input type="hidden" name="request_type" value="ib_institutions">
                                        @if(Auth::user()->id != $institution->initiator_id && $institution->isWaitingApproval==1)
                                         <a  href="{{ route('ib.churchinstitution.approve', $institution->id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-success btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-green"></i></span>
                                  </a>
                                        @endif
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


<!--  Create instution Route: {{ url('ib/institution/create') }} -->

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.datatable').DataTable({

                "ordering" : false
            });

        } );
    </script>
@endsection

@endsection
