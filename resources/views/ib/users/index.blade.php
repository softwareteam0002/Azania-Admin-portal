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
            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Internet Banking</a></li>
            <li class="breadcrumb-item active">Manage users</li>
        </ol>
    </nav>


    <h5>Manage users</h5>
    <hr/>



    @can('ib_users_create')
        <div class="card">
            <div class="card-body">
                <h6 class="">Create user form</h6>
                <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
                <form action="{{ route('ib.verify.account') }}" method="POST">
                    {!! csrf_field() !!}
                <div class="col-md-4">
                        <div class="form-group">
                            <label for="name">Account Number:<span class="text-danger">*</span></label>
                             @if(session()->has('responseCode_acc')) 
                            <input type="text" id="account_number" name="account_number"  class="form-control form-control-sm" value="{{session()->get('accountID')}}" />
                            @else 
           
                            <input type="text" id="account_number" name="account_number"  class="form-control form-control-sm" required>

                            @endif
                        </div>
                    </div>
                    <input type="submit" class="btn btn-info btn-sm" name="verify" id="verify" value="Verify"/>  
                </form>    
                <form action="{{ route('ib.user.store') }}" method="POST">
                    @csrf
                    @method('POST')

                    <div class="row">

                        <div class="col-md-4">
                            <div id="name_input_group" class="form-group has-error {{ $errors->has('name') ? 'has-error' : '' }}">
                                <label for="name">Full Name:<span class="text-danger">*</span></label>
                                @if(session()->has('responseCode_acc')) 
                                <input type="text" id="name" name="name" class="form-control form-control-sm" value="{{ old('name', session()->has('clientName') ? session()->get('clientName') : '') }}" placeholder="Enter the full name here."  readonly="" />

                                @else
                                  <input type="text" id="name" name="name" class="form-control form-control-sm" value="{{ old('name', session()->has('clientName') ? session()->get('clientName') : '') }}" placeholder="Enter the full name here." readonly="" />
                                @endif
                                @if($errors->has('name'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('name') }}
                                    </em>
                                @endif
                                <p class="helper-block">
                                    {{ trans('global.user.fields.name_helper') }}
                                </p>
                            </div>
                        </div>
						<div class="col-md-4">
                            <div id="name_input_group" class="form-group has-error {{ $errors->has('id_type') ? 'has-error' : '' }}">
                                <label for="name">Type of ID:<span class="text-danger">*</span></label>
                                @if(session()->has('responseCode_acc')) 
                                <input type="text" id="id_type" name="id_type" class="form-control form-control-sm" value="{{ old('name', session()->has('id_type') ? session()->get('id_type') : '') }}" placeholder=""  readonly="" />
                                @else
								<select name="id_type" class="form-control  form-control-sm" value="{{ old('id_type', session()->has('id_type') ? session()->get('id_type') : '')}}" required readonly=""/>
									<option value="">Select Type of ID</option>
										<option value="National ID" >National ID</option>
										<option value="Voters ID"  >Voters ID</option>
										<option value="Empoyee ID" >Empoyee ID</option>
										<option value="Driving Licence" >Driving Licence</option>
										<option value="Passport" >Passport</option>
								</select>
								@endif
                                @if($errors->has('id_type'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('id_type') }}
                                    </em>
                                @endif
                                <p class="helper-block">
                                    {{ trans('global.user.fields.name_helper') }}
                                </p>
                            </div>
                        </div>
                       <div class="col-md-4">
                            <div class="form-group has-error {{ $errors->has('name') ? 'has-error' : '' }}">
                                <label for="name">ID Number:<span class="text-danger">*</span></label>
                                <input type="text" id="id_number" name="id_number" minlength="3" maxlength="25" class="form-control form-control-sm" value="{{ old('id_number', session()->has('id_number') ? session()->get('id_number') : '') }}" placeholder="Enter ID Number." required readonly="" / >
                                @if($errors->has('id_number'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('id_number') }}
                                    </em>
                                @endif
                                <p class="helper-block">
                                </p>
                            </div>
                        </div>

                         
                        <div class="col-md-3">
                            <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                                <label for="email">{{ trans('global.user.fields.email') }}:<span class="text-danger">*</span></label>
                                <input type="email" id="email" name="email" class="form-control form-control-sm" value="{{ old('email', session()->has('emailID') ? session()->get('emailID') : '') }}" placeholder="Enter email address here." required>
                                @if($errors->has('email'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('email') }}
                                    </em>
                                @endif
                                <p class="helper-block">
                                    {{ trans('global.user.fields.email_helper') }}
                                </p>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                                <label for="email">{{ trans('global.phone') }}:</label>
                             
                             @if(session()->has('responseCode_acc')) 
                                 <input type="text" id="phone" name="phone" minlength="10" maxlength="12" class="form-control form-control-sm" value="{{ old('phone', session()->has('phone') ? session()->get('phone') : '') }}" placeholder="Enter phone number here." readonly="">
 
                               @else
                                <input type="text" id="phone" name="phone" minlength="10" maxlength="12" class="form-control form-control-sm" value="{{ old('phone', session()->has('phone') ? session()->get('phone') : '') }}" placeholder="Enter phone number here." readonly=""/>
                                @endif
                                @if($errors->has('phone'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('phone') }}
                                    </em>
                                @endif
                                <p class="helper-block">
                                <!-- {{ trans('global.phone') }} -->
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="types">OTP Options:<span class="text-danger">*</span></label>
                                <select name="option_id" id="option_id" class="form-control  form-control-sm">
                                    <option value="0" >Select OTP Option</option>
                                    @foreach($options as $option)
                                        <option value="{{ strtoupper($option->option_name) }}" >{{ strtoupper($option->option_name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr/>
                            <!-- <input class="btn btn-success form-control" type="submit" value="{{ trans('global.save') }}"> -->
                            <button type="submit" class="btn btn-success btn-sm">Save</button>
                            <button type="button" class="btn btn-link btn-sm" onclick="eraseText()">Clear form</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>



    @endcan


    <h5>Registered users.</h5>
    <hr/>
    <div class="card">
        <div class="card-body">
            <h6 class="mb-4">All registered users.</h6>
            <div class="table-responsive">
                <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                    <thead>
                    <tr><th width="10"> </th>
                        <th>Name</th>
						<th>Username</th>
                        <th>Email</th>
                        <th>Mobile Number</th>
                        <th>Verified</th>
                        <th>User Status</th>
                        <th>Approval Status</th>
                        <th>Initiator</th>
                        <th>Approver</th>
                        <th>Action</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($subscribers as $key => $subscriber)
                    <tr data-entry-id="1" data-toggle="modal" data-target="#largeModal{{$subscriber->id}}" id="loadingModel" data="{{$subscriber->id}}">
						<td></td>
						<td>{{$subscriber->display_name}}</td>
                        <td>{{$subscriber->name}}</td>
                        <td>{{$subscriber->email}}</td>
                        <td>{{$subscriber->mobile_phone ?? 'N/A'}}</td>
                        <td>
                            @if($subscriber->isVerified == 0)
                                <span class="badge badge-danger">Not Verified</span>
                            @else
                                <span class="badge badge-success">Verified</span>
                            @endif
                        </td>
                        <td>
                            @if(strtoupper($subscriber->status)=="ACTIVE")
                            <span class="badge badge-success">{{ $subscriber->status }}</span>
                            @endif

                            @if(strtoupper($subscriber->status)=="BLOCKED")
                                <span class="badge badge-danger">{{ $subscriber->status }}</span>
                            @endif

                        </td>
                        <td>
                            @if($subscriber->isWaitingApproval == 0)
                                <span class="badge badge-success">Approved</span>
               @elseif($subscriber->isWaitingApproval == 2)
                                <span class="badge badge-danger">Rejected</span>
                            @else
                                <span class="badge badge-danger">Pending Approval</span>
                            @endif
                        </td>
                        <td>{{ $subscriber->initiators->name ?? '--' }}</td>
                        <td>{{$subscriber->approvers->name ?? '--'}}</td>
                    <td>
                         
                        <form action="{{ url('ib/requests/actions') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" value="{{ $subscriber->id }}">
                            <input type="hidden" name="approver_id" value="{{ Auth::user()->id }}">
                            <input type="hidden" name="request_type" value="ib_users">
                              @if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)
                            <a class="btn btn-primary btn-sm" title="Edit User" href="{{ url('ib/user/edit/'.$subscriber->id ) }}">
                                <i class="fas fa-edit text-dark-pastel-green"></i>
                            </a>
                             @endif
                            <a class="btn btn-success btn-sm" title="Add Account" href="{{ url('ib/user/add_account/'.$subscriber->id ) }}">
                                <i class="fas fa-plus text-dark-pastel-green"></i>
                            </a>
                            @if($subscriber->status=="Active" && $subscriber->isWaitingApproval==0)                     
                                <input type="hidden" id="submit_value" name="submit_value" value="approved" />
                            @endif
                            @if(Auth::user()->id != $subscriber->initiator_id && $subscriber->isWaitingApproval==1 && $subscriber->isBlocked == 0)
                             
                                <a  href="{{ route('ib.user.approve', $subscriber->id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-success btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-green"></i></span>
                                  </a>    

                            @endif

                        </form>

                        @if($subscriber->status=="Active" && $subscriber->isWaitingApproval == 0 && $subscriber->isBlocked < 1)
                                <button class="btn btn-danger btn-sm" title="Block" data-toggle="modal" onclick="handleActivateBlock('{{ url('ib/updateUserStatus') }}','{{$subscriber->name}}','{{$subscriber->mobile_phone}}','{{$subscriber->email}}','Blocked',{{ $subscriber->id }})"   data-target="#block_user">
                                    <i class="fas fa-warning text-dark-pastel-green"></i>
                                </button>
                                @if($subscriber->isWaitingApproval==0)                     
                                <input type="hidden" id="submit_value" name="submit_value" value="approved" />
                                @endif
                            @endif
                 @if((auth()->user()->id != $subscriber->disabledBy_id) && $subscriber->isBlocked == 1 && $subscriber->isWaitingApproval == 1)
                                  <a  href="{{ route('ib.user.disableApproval', $subscriber->id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-warning btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-yellow"></i></span>
                                  </a>    
                                    @endif

                            @if($subscriber->status=="Blocked" && $subscriber->isWaitingApproval == 0 && $subscriber->isBlocked < 1)
                                <button class="btn btn-primary btn-sm" title="Activate" data-toggle="modal" onclick="handleActivateBlock('{{ url('ib/updateUserStatus') }}','{{$subscriber->name}}','{{$subscriber->mobile_phone}}','{{$subscriber->email}}','Active',{{ $subscriber->id }})"  data-target="#block_user">
                                    <i class="fas fa-check text-dark-pastel-blue"></i>
                                </button>
                            @endif
                            <form action="{{ route('ib.user.reset') }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" class="d-inline" >
                                <input type="hidden" name="_method" value="POST">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="user_id" value="{{ $subscriber->id }}">
                                <button type="submit" class="btn btn-warning btn-sm" title="Reset password">
                                    <i class="fas fa-user-lock"></i>
                                </button>
                            </form>
                    </td>

                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    @include('ib.dialogs.user_block_dialog')



@section('scripts')
    @parent
    <script>
        $(function () {
            $('.datatable').DataTable();
        });
        //figure out how to validate the full name input
        $("#name").change(function(){
            //get the value of the field
            var v = $(this).val();
            if((v.split(" ").length-1) < 1){
                //there is a single field
                $("#name").removeClass("is-invalid");
                $("#name").removeClass("is-valid");
                $("#name").addClass("is-invalid");
            }else{
                //the field has multiple names
                $("#name").removeClass("is-invalid");
                $("#name").removeClass("is-valid");
                $("#name").addClass("is-valid");
            }
        });

    </script>

    <script>
        function handleActivateBlock(url,username,mobile,email,status_value,id)
        {
            var form = document.getElementById('blockUser');
            var action_plan="";
            if(status_value=="Blocked")
            {
                action_plan="BLOCKING";
            }
            else if(status_value=="Active")
            {
                action_plan="ACTIVATION";
            }
            else{
                action_plan="";
            }
            var htmlBody = "<h5>CONFIRM "+action_plan+"</h5>\n" +
                "    <hr/>\n" +
                "\n" +
                "    <div class=\"card\">\n" +
                "        <div class=\"card-body\">\n" +
                "            <h6 class=\"mb-3\">USER DETAILS</h6>\n" +
                "            <div class=\"row\">\n" +
                "                <div class=\"col-md-6\">\n" +
                "                    <table class=\"table table-sm\">\n" +
                "                        <tbody>\n" +
                "                            <tr><td>NAME:</td><td>"+ username +"</td></tr>\n" +
                "                            <tr><td>EMAIL:</td><td>"+ email +"</td></tr>\n" +
                "                            <tr><td>PHONE:</td><td>"+ mobile +"</td></tr>\n" +
                "                        </tbody>\n" +
                "                    </table>\n" +
                "                    <hr/>\n" +
                "                </div>\n" +
                "            </div>\n" +
                "        </div>\n" +
                "    </div>"
            var text = document.getElementById('blockingText').innerHTML = htmlBody;
            var status = document.getElementById('status').value = status_value;
            var id = document.getElementById('user_id').value=id;

            form.action = url;
        }
    </script>
	
	<!-- added by Costa, script to clear form with session default values -->
	
	<script>
		function eraseText() {
			document.getElementById('account_number').value = '';
			document.getElementById('id_type').value = '';
			document.getElementById('id_number').value = '';
			document.getElementById('phone').value = '';
			document.getElementById('email').value = '';
			document.getElementById('name').value = '';
			document.getElementById('option_id').value = '';
		};
	</script>
@endsection



@php session()->forget('responseCode_acc') @endphp
@endsection
