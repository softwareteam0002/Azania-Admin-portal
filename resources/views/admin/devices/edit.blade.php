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
        <li class="breadcrumb-item"><a href="{{ url('admin/devices') }}">Devices</a></li>
        <li class="breadcrumb-item active">Edit Device</li>
    </ol>
</nav>

<h5>Edit Device</h5>
<hr />

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h6>Edit device form</h6>
                <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
                <form action="{{ url('admin/device/update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group {{ $errors->has('device_imei1') ? 'has-error' : '' }}">
                                <label for="imei1">IMEI 1:<span class="text-danger">*</span></label>
                                <input type="text" id="imei1" name="device_imei1" class="form-control form-control-sm" value="{{ $device->device_imei1 }}" required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group {{ $errors->has('device_imei2') ? 'has-error' : '' }}">
                                <label for="imei2">IMEI 2:</label>
                                <input type="text" id="imei2" name="device_imei2" class="form-control form-control-sm" value="{{ $device->device_imei2 }}">
                                <input type="hidden" name="device_id" class="form-control form-control-sm" value="{{ $device->device_id }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name">Terminal ID:<span class="text-danger">*</span></label>
                                <input type="text" name="terminal" class="form-control form-control-sm" value="{{ $device->terminal_ID }}" required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="types">Branch:<span class="text-danger">*</span></label>
                                <select name="branch_id" class="form-control form-control-sm">
                                    @foreach($branches as $branch)
				 <option value="{{ $branch->id }}" @if($branch->id == $device->branch_id) selected @endif>{{ $branch->branchName }}</option>
                                     @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <hr />
                            <button type="submit" class="btn btn-sm btn-success">Save</button>
                            <a href="{{ url('admin/devices') }}" class="btn btn-sm btn-link"><i class="fas fa-arrow-left"></i> Go back</a>
                        </div>

                    </div>
                </form>
            </div>
        </div>


    </div>
</div>

@endsection
