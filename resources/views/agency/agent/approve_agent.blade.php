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
            <li class="breadcrumb-item active">Agents
</li>
        </ol>
    </nav>

    <h5>Agents
</h5>
    <hr/>

    <div class="card mb-5">
        <div class="card-body">
            <h6>Approve Agent form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ route('agency.agent.approveAct', $agent->agent_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <input type="hidden" name="id" value="{{ $agent->agent_id }}">
     
                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label for="name">Full Name:<span class="text-danger">*</span></label>
                            <input type="text" id="name" name="agent_name" class="form-control form-control-sm"
                                   value="{{ $agent->agent_full_name }}" readonly>
                        </div>
                    </div>


                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Mobile Number:<span class="text-danger">*</span></label>
                            <input type="text" name="mobile_number" class="form-control form-control-sm"
                                   value="{{ $agent->agent_msisdn ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Email Address:<span class="text-danger">*</span></label>
                        <input type="text" name="email" class="form-control form-control-sm" value="{{ $agent->email ?? '' }}" readonly>
                    </div>
                </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Business Licence(TIN):<span class="text-danger">*</span></label>
                            <input type="text" name="business_licence" class="form-control form-control-sm"
                                   value="{{ $agent->agent_business_license_number ?? '' }}" readonly>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Registration Number:<span class="text-danger">*</span></label>
                            <input type="text" name="registration_number" class="form-control form-control-sm"
                                   value="{{ $agent->business_certificate_registration_number ?? '' }}" readonly>
                        </div>
                    </div>


                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Address:<span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control form-control-sm"
                                   value="{{ $agent->agent_address ?? '' }}" readonly>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Location:<span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control form-control-sm"
                                   value="{{ $agent->agent_location ?? '' }}" readonly>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Maximum Float Limit:<span class="text-danger">*</span></label>
                            <input type="text" name="float_limit" class="form-control form-control-sm"
                                   value="{{ $agent->agent_float_limit ?? '' }}" readonly>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Daily Limit:<span class="text-danger">*</span></label>
                            <input type="text" name="daily_limit" class="form-control form-control-sm"
                                   value="{{ $agent->agent_daily_limit ?? '' }}" readonly>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="types">Dual Control:</label>
                            <select name="dual_control" class="form-control form-control-sm" readonly>
                                <option value="initiator">Initiator</option>
                                <option value="approver">Approver</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="types">Languages:</label>
                            <select name="language_id" class="form-control form-control-sm" readonly>
                                <option value="0">Select language</option>
                                @foreach($languages as $language)
                                    @if($language->language_id == $agent->agent_language)
                                        <option value="{{ $language->language_id }}"
                                                selected>{{ $language->language_name }}</option>
                                    @else
                                        <option value="{{ $language->language_id }}">{{ $language->language_name }}</option>
                                    @endif
                                @endforeach
                            </select>
                    </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="types">Status:<span class="text-danger">*</span></label>
                            <select name="status_id" class="form-control form-control-sm" readonly>

                                @foreach($statuses as $status)
                                    @if($status->tbl_status_id == $agent->agent_status)
                                        <option value="{{ $status->tbl_status_id }}"
                                                selected>{{ $status->status }}</option>
                                    @else
                                        <option value="{{ $status->tbl_status_id }}">{{ $status->status }}</option>
                                    @endif
                                @endforeach

                            </select>
                    </div>
                    </div>

<div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Branch Name:<span class="text-danger">*</span></label>
                        <input type="text" name="branchName" class="form-control form-control-sm" value="{{ $agent->branchName }}" placeholder="Enter Branch Name." readonly>
                        <input type="hidden" name="branchID" value="{{ $agent->cbsbranchID }}" class="form-control form-control-sm" readonly>
                    </div>
                </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="types">Service Menu:<span class="text-danger">*</span></label>
                            <br/>
                            @if(isset($agentservices))
                                @foreach($agentservices as $agentservice)
                                    @if(in_array($agentservice->short_name, explode("~",$agent->agent_menu)))
                                        <div class="form-check form-check-inline" >
                                            <input class="form-check-input" type="checkbox" name="agent_service[]"
                                                   value="{{ $agentservice->short_name }}" checked disabled>
                                            <label class="form-check-label">
                                                {{ $agentservice->agent_serviceName }}
                                            </label>
                                        </div>
                                    @else
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="agent_service[]"
                                                   value="{{ $agentservice->short_name }}">
                                            <label class="form-check-label" disabled>
                                                {{ $agentservice->agent_serviceName }}
                                            </label>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-12">
                        <hr/>
                        <input type="submit" name="approve" value="approve" class="btn btn-success btn-sm" value="Approve" />
                        <input type="submit" name="reject" value="reject" class="btn btn-primary btn-sm" value="Reject" />
                        <a href="{{ url('agency/users') }}" class="btn btn-link btn-sm"><i class="fas fa-arrow-left"></i> Go back</a>
                    </div>

                </div>



            </form>
        </div>
    </div>


    @endsection
