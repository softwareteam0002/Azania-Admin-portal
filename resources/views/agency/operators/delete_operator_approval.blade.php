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
            <li class="breadcrumb-item"><a href="{{ url('/agency/users') }}">Agents</a></li>
            <li class="breadcrumb-item active">Operators</li>
        </ol>
    </nav>

    <h5>Agent {{$agent_name}} Operators</h5>
    <hr/>

    <div class="card mb-5">
        <div class="card-body">
            <h6> Delete Agent Operator Approval form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ route('agency.operator.deleteActApproval', $operator->operator_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                   
                   <div class="col-md-3">
                    <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                        <label for="name">First Name:<span class="text-danger">*</span></label>
                        <input type="text" id="name" name="full_name" class="form-control form-control-sm" value="{{ $operator->operator_fullname }}" readonly>
                        <input type="hidden" value="{{ $operator->operator_id }}" name="operator_id">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Mobile Number:<span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control form-control-sm" value="{{ $operator->operator_msisdn}}" readonly>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Location</label>
                        <input type="text" name="location" class="form-control form-control-sm" value="{{ $operator->location}}" readonly>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="types">Device:<span class="text-danger">*</span></label>
                        <select name="device" class="form-control form-control-sm" disabled>
                            <option value="0" >Select a device</option>
                            @foreach($devices as $device)
                                @if($device->device->device_id == $operator->device_id)
                                    <option value="{{ $device->device->device_id }}" selected>{{ $device->device->terminal_ID }}</option>
                                @else
                                    <option value="{{ $device->device->device_id }}">{{ $device->device->terminal_ID }}</option>
                                @endif
                                
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-12">
                        <div class="form-group">
                            <label for="types">Service Menu:<span class="text-danger">*</span></label>
                            <br/>
                            @if(isset($agentservices))
                                @foreach($agentservices as $agentservice)
                                    @if(in_array($agentservice->short_name, explode("~",$operator->operator_menu)))
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="agent_service[]"
                                                   value="{{ $agentservice->short_name }}" checked disabled>
                                            <label class="form-check-label">
                                                {{ $agentservice->agent_serviceName }}
                                            </label>
                                        </div>
                                    @else
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="agent_service[]"
                                                   value="{{ $agentservice->short_name }}" disabled>
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
                        <hr/>
                        <input type="submit" name="approve" value="approve" class="btn btn-success btn-sm" value="Approve" />
                        <input type="submit" name="reject" value="reject" class="btn btn-primary btn-sm" value="Reject" />
                        <a href="{{ url('agency/operators/'.$operator->agent_id) }}" class="btn btn-link btn-sm"><i class="fas fa-arrow-left"></i> Go back</a>
                    </div>

                </div>

            </form>
        </div>
    </div>


    @endsection
