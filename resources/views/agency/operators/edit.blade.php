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
            <li class="breadcrumb-item"><a href="{{ url('/agency/users') }}">Agents</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/agency/operators/'.$operator->agent_id) }}">Agent Operators</a></li>
            <li class="breadcrumb-item active">Edit Operator</li>
        </ol>
    </nav>

    <h5>Edit Operators</h5>
    <hr/>
    <hr/>
    <!--

    @can('user_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success btn-sm" href="{{ url('agency/operator/create') }}">
                    Add Operator
                </a>
            </div>
        </div>
    @endcan

    -->


    <div class="card">
        <div class="card-body">
            <h6>Edit operator form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>

        <form action="{{ url('agency/operator/update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                        <label for="name">First Name:<span class="text-danger">*</span></label>
                        <input type="text" id="name" name="full_name" class="form-control form-control-sm" value="{{ $operator->operator_fullname }}" required>
                        <input type="hidden" value="{{ $operator->operator_id }}" name="operator_id">
                        <input type="hidden" value="{{ $operator->agent_id }}" name="agent_id">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Mobile Number:<span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control form-control-sm" value="{{ $operator->operator_msisdn}}" required>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Location</label>
                        <input type="text" name="location" class="form-control form-control-sm" value="{{ $operator->location}}" required>
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
                                                   value="{{ $agentservice->short_name }}" checked>
                                            <label class="form-check-label">
                                                {{ $agentservice->agent_serviceName }}
                                            </label>
                                        </div>
                                    @else
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="agent_service[]"
                                                   value="{{ $agentservice->short_name }}">
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
                    <button type="submit" class="btn btn-sm btn-success">Save</button>
                    <a href="{{ url('agency/operators/'.$operator->agent_id) }}" class="btn btn-sm btn-link"><i class="fas fa-arrow-left"></i> Go back</a>
                </div>
            </div>
        </form>
        </div>
    </div>


@section('scripts')
    @parent
    <script>
        $(function () {
            $('.datatable').DataTable()
        })
    </script>
@endsection


@endsection
