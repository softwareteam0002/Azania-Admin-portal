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
            <li class="breadcrumb-item active">Manage service accounts</li>
        </ol>
    </nav>


    <h5>Manage service accounts</h5>
    <hr/>



    @can('ib_users_create')
        <div class="card">
            <div class="card-body">
                <h6 class="">Create service account form</h6>
                <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
                <form action="{{ url('ib/service_account/store') }}" method="POST">
                    @csrf
                    @method('POST')
			<div class="row">
			<div class="col-md-4">
			   <div class="form-group">
                    <label for="types">Service Name:<span class="text-danger">*</span></label>
                    <select name="service_id" class="form-control" required>
                        <option value="" >Select Service</option>
                        @foreach($services as $service)
			<option value="{{ $service->id }}" >{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>
		</div>

                        <div class="col-md-3">
                            <div class="form-group {{ $errors->has('account_number') ? 'has-error' : '' }}">
                                <label for="account_number">Service Number:</label>
                                <input type="text" id="account_number" name="account_number" class="form-control form-control-sm" value="{{ old('account_number', isset($user) ? $user->account_number : '') }}" placeholder="Enter account numbers here." >
                                @if($errors->has('account_number'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('account_number') }}
                                    </em>
                                @endif
                               </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group {{ $errors->has('commission_account') ? 'has-error' : '' }}">
                                <label for="commission_account">Commission Account:</label>
                                <input type="text" id="commission_account" name="commission_account" class="form-control form-control-sm" value="{{ old('commission_account', isset($user) ? $user->commission_account : '') }}" placeholder="Enter commission account numbe here." >
                                @if($errors->has('commission_account'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('commission_account') }}
                                    </em>
                                @endif
                               </div>
                        </div>
						<div class="col-md-3">
                            <div class="form-group {{ $errors->has('retail_trxn_limit') ? 'has-error' : '' }}">
                                <label for="retail_trxn_limit">Retail Transaction Limit:</label>
                                <input type="number" id="retail_trxn_limit" name="retail_trxn_limit" class="form-control form-control-sm"  placeholder="Enter retail transaction limit here."required>
                                @if($errors->has('retail_trxn_limit'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('retail_trxn_limit') }}
                                    </em>
                                @endif
                               </div>
                        </div>
						<div class="col-md-3">
                            <div class="form-group {{ $errors->has('retail_daily_limit') ? 'has-error' : '' }}">
                                <label for="retail_daily_limit">Retail Daily Limit:</label>
                                <input type="number" id="retail_daily_limit" name="retail_daily_limit" class="form-control form-control-sm"  placeholder="Enter retail daily limit here." required>
                                @if($errors->has('retail_daily_limit'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('retail_daily_limit') }}
                                    </em>
                                @endif
                               </div>
                        </div>
						<div class="col-md-3">
                            <div class="form-group {{ $errors->has('corporate_trxn_limit') ? 'has-error' : '' }}">
                                <label for="corporate_trxn_limit">Corporate Transaction Limit:</label>
                                <input type="number" id="corporate_trxn_limit" name="corporate_trxn_limit" class="form-control form-control-sm"  placeholder="Enter corporate transaction limit here." required>
                                @if($errors->has('corporate_trxn_limit'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('corporate_trxn_limit') }}
                                    </em>
                                @endif
                               </div>
                        </div>
						<div class="col-md-3">
                            <div class="form-group {{ $errors->has('corporate_daily_limit') ? 'has-error' : '' }}">
                                <label for="corporate_daily_limit">Corporate Daily Limit:</label>
                                <input type="number" id="corporate_daily_limit" name="corporate_daily_limit" class="form-control form-control-sm"  placeholder="Enter corporate daily limit here." required>
                                @if($errors->has('corporate_daily_limit'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('corporate_daily_limit') }}
                                    </em>
                                @endif
                               </div>
                        </div>
			</div>
                          <div class="col-md-12">
                            <hr/>
                            <!-- <input class="btn btn-success form-control" type="submit" value="{{ trans('global.save') }}"> -->
                            <button type="submit" class="btn btn-success btn-sm">Save</button>
                            <button type="reset" class="btn btn-link btn-sm">Clear form</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>



    @endcan              


    <h5>Service accounts.</h5>
    <hr/>
    <div class="card">
        <div class="card-body">
            <h6 class="mb-4">All Service accounts.</h6>
            <div class="table-responsive">
                <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th>Service Name</th>
                        <th>Service Account</th>
                        <th>Commission Account</th>
						<th>Retail Transaction Limit</th>
                        <th>Retail Daily Limit</th>
						<th>Corporate Transaction Limit</th>
                        <th>Corporate Daily Limit</th>
						<th>Initiator</th>
						<th>Approver</th>
		                <th>Approval Status</th>
                        <th>Action</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($accounts as $account)
                    <tr>
                        <td>{{$account->service_name}}</td>
                        <td>{{$account->account_number}}</td>
                        <td>{{$account->commission_account}}</td> 
						<td>{{$account->retail_transaction_limit}}</td>
						<td>{{$account->retail_daily_limit}}</td>
						<td>{{$account->corporate_transaction_limit}}</td>
						<td>{{$account->corporate_daily_limit}}</td>
                        <td>{{ $account->initiators->name ?? '--' }}</td>
                        <td>{{$account->approvers->name ?? '--'}}</td>
                         <td>
                            @if($account->isWaitingApproval == 0)
                                <span class="badge badge-success">Approved</span>
                            @elseif($account->isWaitingApproval == 2)
                                <span class="badge badge-danger">Rejected</span>
                            @else
                                <span class="badge badge-danger">Pending Approval</span>
                            @endif
                        </td>
                    <td>
                        <form action="{{ url('ib/requests/actions') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" value="{{ $account->id }}">
                            <input type="hidden" name="approver_id" value="{{ Auth::user()->id }}">
                            <input type="hidden" name="request_type" value="ib_service_account">
							 @if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)
                            <a class="btn btn-primary btn-sm" title="Edit User" href="{{ url('ib/service_account/edit/'.$account->id ) }}">
                                <i class="fas fa-edit text-dark-pastel-green"></i>
                            </a>
                         @endif
                            @if(Auth::user()->id != $account->initiator_id && $account->isWaitingApproval==1)
                             <a  href="{{ url('ib/service_account/approve/'.$account->id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-success btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-green"></i></span>
                                  </a> 
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
@endsection




@endsection
