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
    <hr />
</div>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb small" style="background-color:#f9f9f9; padding: .55rem 1rem;">
        <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
        <li class="breadcrumb-item active">Agent</li>
    </ol>
</nav>

<h5>Agents</h5>
<hr />
<div class="card">
    <div class="card-body">
        <h6>Create agent form</h6>
        <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
         <form action="{{ route('agency.verify.account') }}" method="POST">
                    {!! csrf_field() !!}
                <div class="col-md-4">
                        <div class="form-group">
                            <label for="name">Account Number:<span class="text-danger">*</span></label>
                            @if(session()->has('responseCode'))
                            <input type="text" id="account_number" name="account_number" value="{{session()->get('accountID')}}"  class="form-control form-control-sm" required>
                             @else 
                             <input type="text" id="account_number" name="account_number"  class="form-control form-control-sm" required>
                             @endif
                        </div>
                    </div>
                    <input type="submit" class="btn btn-info btn-sm" name="verify" id="verify" value="Verify"/> 

                </form> 
                
               <p id="coordinateMessage"></p>
  <button class="btn btn-info btn-sm" onclick="getLocation()">Get Coordinates</button>
        <form action="{{ url('agency/user/store') }}" method="POST">
            @csrf
            @method('POST')
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                        <label for="name">Full Name:<span class="text-danger">*</span></label>
                                @if(session()->has('responseCode'))
                        <input type="text" id="name" name="name" class="form-control form-control-sm" value="{{ old('name', session()->has('clientName') ? session()->get('clientName') : '') }}" placeholder="Enter the full name here."  readonly/>
                        
                                @else
                                  <input type="text" id="name" name="name" class="form-control form-control-sm" value="{{ old('name', session()->has('clientName') ? session()->get('clientName') : '') }}" placeholder="Enter the full name here."  />
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
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Mobile Number:<span class="text-danger">*</span></label>
                        <!-- <input type="text" name="mobile_number" class="form-control form-control-sm"  value="{{ old('phone', session()->has('mobile') ? session()->get('mobile') : '') }}" placeholder="Enter the mobile number" required> -->
                         @if(session()->has('responseCode')) 
                                 <input type="text" id="phone" name="mobile_number" minlength="10" maxlength="12" class="form-control form-control-sm" value="{{ old('phone', session()->has('phone') ? session()->get('phone') : '') }}" placeholder="Enter mobile number here." readonly="">
 
                               @else
                                <input type="text" id="phone" name="mobile_number" minlength="10" maxlength="12" class="form-control form-control-sm" value="{{ old('phone', session()->has('phone') ? session()->get('phone') : '') }}" placeholder="Enter mobile number here.">
                                @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Email Address:</label>
                        <input type="text" name="email" class="form-control form-control-sm" placeholder="Ent Email Address.">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">TIN Number:<span class="text-danger">*</span></label>
                        <input type="text" name="tin_number" class="form-control form-control-sm" placeholder="Enter the TIN number." required>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Business Licence:<span class="text-danger">*</span></label>
                        <input type="text" name="business_licence" class="form-control form-control-sm" placeholder="Enter the business licence." required>
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Address:<span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control form-control-sm" placeholder="Enter the address." required>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Location:<span class="text-danger">*</span></label>
                        <input type="text" name="location" class="form-control form-control-sm" placeholder="Enter the location." required>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Maximum Float Limit:<span class="text-danger">*</span></label>
                        <input type="text" name="float_limit" class="form-control form-control-sm" placeholder="Enter the maximum float limit." required>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Daily Limit:<span class="text-danger">*</span></label>
                        <input type="text" name="daily_limit" class="form-control form-control-sm" placeholder="Enter the daily limit." required>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="types">Dual Control:</label>
                        <select name="dual_control" class="form-control form-control-sm">
                            <option value="0">None</option>
                            <option value="initiator">Initiator</option>
                            <option value="approver">Approver</option>
                        </select>
                    </div>
                </div> 

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="types">Languages:</label>
                        <select name="language_id" class="form-control form-control-sm">
                            @foreach($languages as $language)
                            <option value="{{ $language->language_id }}">{{ $language->language_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="types">Status:<span class="text-danger">*</span></label>
                        <select name="status_id" class="form-control form-control-sm">

                            @foreach($statuses as $status)
                            <option value="{{ $status->tbl_status_id }}">{{ $status->status }}</option>
                            @endforeach

                        </select>
                    </div>
                </div>
				
				@if(session()->has('responseCode')) 
				<input type="hidden" id="clientId" name="clientId" class="form-control form-control-sm" value="{{ old('clientId', session()->has('clientId') ? session()->get('clientId') : '') }}" placeholder="Enter mobile number here." readonly="">

			    @else
				<input type="hidden" id="clientId" name="clientId" class="form-control form-control-sm" value="{{ old('clientId', session()->has('clientId') ? session()->get('clientId') : '') }}" placeholder="Enter mobile number here.">
				@endif
            

				<div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Branch Name:<span class="text-danger">*</span></label>
                        @if(session()->has('responseCode'))
                        <input type="text" name="branchName" class="form-control form-control-sm" value="{{ session()->get('branchName') }}" placeholder="Enter Branch Name." required>
                       <!-- <input type="hidden" name="branchID" value="{{ session()->get('branchId') }}" class="form-control form-control-sm">
                        <input type="hidden" name="clientId" value="{{ session()->get('clientId') }}" class="form-control form-control-sm">-->
                        @else
                        <input type="text" name="branchName" class="form-control form-control-sm" value="{{ session()->get('branchName') }}" placeholder="Enter Branch Name." required>
                        @endif
                    </div>
                </div>
				<div class="col-md-3">
                        <div class="form-group">
                            <label for="types">Regions:</label>
                            <select name="region" class="form-control form-control-sm" id="regions" required>
                                <option selected>Select Region</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->region_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="types">District:</label>
                            <select name="district" class="form-control form-control-sm" id="districts" required>
                                <option selected>Select District</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}">{{ $district->district_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Ward:<span class="text-danger">*</span></label>
                        <input type="text" name="ward" class="form-control form-control-sm" placeholder="Enter ward." required>
                    </div>
                </div>
				<div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Street:<span class="text-danger">*</span></label>
                        <input type="text" name="street" class="form-control form-control-sm" placeholder="Enter street." required>
                    </div>
                </div>
				<div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Area Famous Name:<span class="text-danger">*</span></label>
                        <input type="text" name="famous_name" class="form-control form-control-sm" placeholder="Enter area famous name." required>
                    </div>
                </div>
				<div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Latitude:<span class="text-danger">*</span></label>
                        <input type="text" name="latitude" class="form-control form-control-sm" placeholder="Enter the latitude." required>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Longitude:<span class="text-danger">*</span></label>
                        <input type="text" name="longitude" class="form-control form-control-sm" placeholder="Enter the longitude." required>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="types">Service Menu:<span class="text-danger">*</span></label>
                        <br />
                        @if(isset($agentservices))
                        @foreach($agentservices as $agentservice)
                            @if($agentservice->short_name == "BI")
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="agent_service[]" value="{{ $agentservice->short_name }}" checked>
                                <label class="form-check-label">
                                    {{ $agentservice->agent_serviceName }}
                                </label>
                            </div>
                            @elseif($agentservice->short_name == "DC")
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="agent_service[]" value="{{ $agentservice->short_name }}" checked>
                                <label class="form-check-label">
                                    {{ $agentservice->agent_serviceName }}
                                </label>
                            </div>
                            @elseif($agentservice->short_name == "WC")
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="agent_service[]" value="{{ $agentservice->short_name }}" checked>
                                <label class="form-check-label">
                                    {{ $agentservice->agent_serviceName }}
                                </label>
                            </div>
                            @elseif($agentservice->short_name == "FT")
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="agent_service[]" value="{{ $agentservice->short_name }}" checked>
                                <label class="form-check-label">
                                    {{ $agentservice->agent_serviceName }}
                                </label>
                            </div>
                            @elseif($agentservice->short_name == "AS")
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="agent_service[]" value="{{ $agentservice->short_name }}" checked>
                                <label class="form-check-label">
                                    {{ $agentservice->agent_serviceName }}
                                </label>
                            </div>
                            @elseif($agentservice->short_name == "MS")
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="agent_service[]" value="{{ $agentservice->short_name }}" checked>
                                <label class="form-check-label">
                                    {{ $agentservice->agent_serviceName }}
                                </label>
                            </div>
                            @elseif($agentservice->short_name == "UP")
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="agent_service[]" value="{{ $agentservice->short_name }}" checked>
                                <label class="form-check-label">
                                    {{ $agentservice->agent_serviceName }}
                                </label>
                            </div>
                            @elseif($agentservice->short_name == "BW")
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="agent_service[]" value="{{ $agentservice->short_name }}" checked>
                                <label class="form-check-label">
                                    {{ $agentservice->agent_serviceName }}
                                </label>
                            </div>
                            @elseif($agentservice->short_name == "SC")
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="agent_service[]" value="{{ $agentservice->short_name }}" checked>
                                <label class="form-check-label">
                                    {{ $agentservice->agent_serviceName }}
                                </label>
                            </div>
                            @elseif($agentservice->short_name == "IP")
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="agent_service[]" value="{{ $agentservice->short_name }}" checked>
                                <label class="form-check-label">
                                    {{ $agentservice->agent_serviceName }}
                                </label>
                            </div>
                            @else
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="agent_service[]" value="{{ $agentservice->short_name }}">
                                <label class="form-check-label">
                                    {{ $agentservice->agent_serviceName }}
                                </label>
                            </div>
                            @endif
                        @endforeach
                        @endif


                    </div>
                </div>

                <div class="col-md-12">
                    <hr />
                    <button type="submit" class="btn btn-sm btn-success">Save</button>
                    <button type="reset" class="btn btn-sm btn-link">Reset</button>
                </div>
            </div>
        </form>

    </div>
</div>


{{-- @can('user_create')--}}
{{-- <div style="margin-bottom: 10px;" class="row">--}}
{{-- <div class="col-lg-12">--}}
{{-- <a class="btn btn-success btn-sm" href="{{ url('agency/user/create') }}">--}}
{{-- Add Agent--}}
{{-- </a>--}}
{{-- </div>--}}
{{-- </div>--}}
{{-- @endcan--}}

<h5>All agents</h5>
<hr/>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>Full Name</th>
                        <th>Agent Number</th>
                        <th>Mobile Number</th>
                        <th>Email</th>
                        <th>Business License</th>
                        <th>TIN Number</th>
                        <th>Branch</th>
                        <th>Daily limit</th>
                        <th>Maximum Float limit</th>
                        <th class="d-none">Registered date</th>
                         <th>Initiator</th>
                        <th>Approver</th>
                        <th>Status</th>
                        <th>Actions</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($agents as $agent)
                    <tr>
                        <td></td>
                        <td>{{$agent->agent_full_name}}</td>
                        <td>{{$agent->agent_id}}</td>
                        <td>{{$agent->agent_msisdn ?? 'N/A'}}</td>
                        <td>{{$agent->email ?? 'N/A'}}</td>
                        <td>{{$agent->agent_tin_number ?? 'N/A'}}</td>
                        <td>{{$agent->business_licence_number ?? 'N/A'}}</td>
                        <td>{{ $agent->branchName }}</td>
                        <td class="text-right">{{number_format($agent->agent_daily_limit,2) ?? 'N/A'}}</td>
                        <td class="text-right">{{number_format($agent->agent_float_limit,2) ?? 'N/A'}}  </td>
                        
                        <td class="d-none">{{ $agent->agent_date_registered ?? $agent->created_at }}</td>
                        <td>{{$agent->initiators->name ?? '--' }}</td>
                            <td>{{$agent->approvers->name ?? '--'}}</td>
                        <td>
                            @if($agent->isWaitingApproval == 0)
                                    <span class="badge badge-success">Approved</span>
                                @elseif($agent->isWaitingApproval == 2)
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-danger">Pending Approval</span>
                                @endif
                            @if($agent->agent_status == 1)
                                <span class="badge badge-success">Active</span>
                                @elseif($agent->agent_status == 2)
                                <span class="badge badge-danger">In active</span>
                                @elseif($agent->agent_status == 4)
                                <span class="badge badge-danger">Suspended</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ url('agency/user/resetpin') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="agent_id" value="{{ $agent->agent_id }}">
                                <button type="submit" class="btn btn-primary btn-sm" title="Reset PIN">
                                <i class="fas fa-user-lock"></i>
                                </button>
                            </form> 
							@if($agent->agent_status == 1)
                                    <form action="{{ url('agency/user/status') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="agent_id" value="{{ $agent->agent_id }}">
                                        <input type="hidden" name="status" value="2">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Deactivate agent">
                                        <i class="fas fa-times"></i>
                                        </button>
                                    </form> 
                                @elseif($agent->agent_status == 2)
                                    <form action="{{ url('agency/user/status') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="agent_id" value="{{ $agent->agent_id }}">
                                        <input type="hidden" name="status" value="1">
                                        <button type="submit" class="btn btn-success btn-sm" title="Actvate agent">
                                        <i class="fas fa-check"></i>
                                        </button>
                                    </form> 
                                @endif
                             @if(Auth::user()->id != $agent->initiator_id && $agent->isWaitingApproval==1)
                                     <a  href="{{ route('agency.agent.approve', $agent->agent_id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-success btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-green"></i></span>
                                     @endif
                            <a href="{{ url('agency/accounts', [$agent->agent_id]) }}" title="Accounts"  class="btn btn-sm btn-primary">
                                <span><i class="fas fa-money-check-alt"></i></span>
                            </a>
                             @if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)
                            <a href="{{ url('agency/user/edit',[$agent->agent_id]) }}" title="Edit agent" class="btn btn-sm btn-primary">
                                <span><i class="fas fa-edit text-dark-pastel-green"></i></span>
                            </a>
                            @endif
                            <a href="{{ url('agency/devices', [$agent->agent_id]) }}" title="Devices" class="btn btn-sm btn-primary">
                                <span><i class="fas fa-mobile-alt"></i></span>
                            </a>
                            <a href="{{ url('agency/operators', [$agent->agent_id]) }}" title="Operators" class="btn btn-sm btn-primary">
                                <span><i class="fas fa-users"></i></span>
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
        $('.datatable').DataTable({

        });
		 $('#regions').change(function () {
                    var region_id = $(this).val();
                    $.ajax({
                        url: "/api/agency/districts/"+region_id,
                        method: "GET",
                        success: function(response) {
                            //console.log(response);
                            var districts = $("#districts");
                            districts.empty();

                            $.each(response.districts, function(index, district) {
                                var option = $("<option>", { value: district.id, text: district.district_name });
                                districts.append(option);
                            });
                        },
                        error: function(xhr, status, error) {
                            // Handle the error here
                            console.error("Error:", error);
                        }
                    });


                });
</script>
@endsection


@endsection
