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
            <li class="breadcrumb-item active">Agent Devices</li>
        </ol>
    </nav>

    <h5>{{ $agent->agent_full_name }} Devices
</h5>
    <hr/>

    <div class="card mb-5">
        <div class="card-body">
            <h6>Approve Agent form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ route('agency.device.approveAct', $device->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <input type="hidden" name="id" value="{{ $device->id }}">
                     <div class="col-md-4">
                        <div class="form-group">
                            <label for="types">Choose device:<span class="text-danger">*</span></label>
                            <select name="device" class="form-control form-control-sm" readonly>
                                @foreach($approvedevices as $devices)
									<option value="{{ $device->device_id }}" @if($devices->device_id == $device->device_id) selected @endif >{{ $devices->terminal_ID }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="types">Trading Account:<span class="text-danger">*</span></label>
                            <select name="trading_ac" class="form-control form-control-sm" readonly>
                                @foreach($tradingacs as $tradingac)
                                    <option value="{{ $tradingac->account_id }}" @if($tradingac->account_id == $connecteddevice->trading_account_id) selected @endif>{{ $tradingac->bank_account }} -  {{ $tradingac->accountStatus->status  }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="types">Commision Account:<span class="text-danger">*</span></label>
                            <select name="commision_ac" class="form-control form-control-sm" readonly>
                                @foreach($commisionacs as $commisionac)
                                    <option value="{{ $commisionac->account_id }}" @if($commisionac->account_id == $connecteddevice->commision_account_id) selected @endif>{{ $commisionac->bank_account  }} -  {{ $commisionac->accountStatus->status  }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                  
                    <div class="col-md-12">
                        <hr/>
                        <input type="submit" name="approve" value="approve" class="btn btn-success btn-sm" value="Approve" />
                        <input type="submit" name="reject" value="reject" class="btn btn-primary btn-sm" value="Reject" />
                        <a href="{{ route('agency.devices', $agent->agent_id) }}" class="btn btn-link btn-sm"><i class="fas fa-arrow-left"></i> Go back</a>
                    </div>

                </div>



            </form>
        </div>
    </div>


    @endsection
