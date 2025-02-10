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
        <li class="breadcrumb-item active">Devices</li>
    </ol>
</nav>

<h5>Devices</h5>
<hr />

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h6>Create device form</h6>
                <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
                <form action="{{ url('admin/device/store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group {{ $errors->has('device_imei1') ? 'has-error' : '' }}">
                                <label for="imei1">IMEI 1:<span class="text-danger">*</span></label>
                                <input type="text" id="imei1" name="device_imei1" class="form-control form-control-sm" placeholder="Enter device first IMEI here." required>
                                @if($errors->has('device_imei1'))
                                <em class="invalid-feedback">
                                    {{ $errors->first('device_imei1') }}
                                </em>
                                @endif
                                <p class="helper-block">
                                    {{ trans('global.user.fields.name_helper') }}
                                </p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group {{ $errors->has('device_imei2') ? 'has-error' : '' }}">
                                <label for="imei2">IMEI 2:</label>
                                <input type="text" id="imei2" name="device_imei2" class="form-control form-control-sm" placeholder="Enter device second IMEI here.">
                                @if($errors->has('device_imei2'))
                                <em class="invalid-feedback">
                                    {{ $errors->first('imei2') }}
                                </em>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name">Terminal ID:<span class="text-danger">*</span></label>
                                <input type="text" name="terminal" class="form-control form-control-sm" placeholder="Enter device terminal ID number here" required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="types">Branch:<span class="text-danger">*</span></label>
                                <select name="branch_id" class="form-control form-control-sm">
                                    @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->branchName }}</option>
                                    @endforeach
                                </select>
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


    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6>Upload devices</h6>
                <p class="small text-danger">All fields with an asterisk are mandatory, file must be .csv and less than 2MB.</p>
                <form action="{{ url('admin/device/store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Devices file:<span class="text-danger">*</span></label>
                                <input type="file" name="devices_file" class="form-control form-control-sm">
                                <input type="hidden" name="bulk_upload" value="bulk_upload">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="types">Branch Identity:<span class="text-danger">*</span></label>
                                <select name="branch_id" class="form-control form-control-sm">
                                    @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->branchName }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <div class="col-md-12">
                            <hr />
                            <button type="submit" class="btn btn-sm btn-success">Upload</button>
                            <button type="reset" class="btn btn-sm btn-link">Reset</button>
                            <a href="{{ asset('devices_bulk_upload_template.csv') }}" class="float-right btn btn-sm btn-link" target="_blank" rel="noopener noreferrer">Download Template</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<!--


{{--    @can('user_create')--}}
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success btn-sm" href="{{ url('admin/device/create') }}">
                    Add Device
                </a>
            </div>
        </div>
{{--    @endcan--}}

-->


<h5>All Devices</h5>
<hr/>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>Device IMEI 1</th>
                        <th>Device IMEI 2</th>
                        <th>Device Terminal ID</th>
                        <th>Branch</th>
                        <th>Status</th>
                        <th>Registered by</th>
                        <th>Actions</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($devices as $device)
                    <tr>
                        <td>
                        </td>
                        <td>{{$device->device_imei1}}</td>
                        <td>{{$device->device_imei2}}</td>
                        <td>{{$device->terminal_ID ?? 'N/A'}}</td>
                        <td>
                           {{$device->branches->branchName ?? ''}}
                        </td>

                        <td>
                        @if($device->device_status == 1)
                            <span class="badge badge-success">Active</span>
                        @elseif($device->device_status == 2)
                            <span class="badge badge-warning">InActive</span>
                        @elseif($device->device_status == 3)
                            <span class="badge badge-danger">Blocked</span>
                        @elseif($device->device_status == 4)
                            <span class="badge badge-secondary">Suspended</span>
                            @else
                            <span class="badge badge-dark">Unassigned</span>
                        @endif


                        </td>

                        <td>{{$device->users->name ?? ''}}</td>
                        <td>
						 @if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)
                            <a href="{{ url('admin/devices/'.$device->device_id) }}" title="Edit devices"  class="btn btn-sm btn-primary">
                                <i class="fas fa-edit text-dark-pastel-green"></i>
                            </a>
							@endif
                            @if($device->device_status == 1)
                                <form action="{{ url('admin/deactivate') }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('POST')
                                    <input type="hidden" name="device_id" value="{{ $device->device_id }}">
                                    <button type="submit" title="Deactivate device" class="btn btn-sm btn-danger">
                                        <span><i class="fas fa-power-off danger"></i></span>
                                    </button>
                                </form>
                            @endif

                            @if($device->device_status == 2)
                                <form action="{{ url('admin/activate') }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('POST')
                                    <input type="hidden" name="device_id" value="{{ $device->device_id }}">
                                    <button type="submit" title="Activate device"  class="btn btn-sm btn-success">
                                        <span><i class="fas fa-check"></i></span>
                                    </button>
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
@section('scripts')
@parent
<script>
    $(document).ready(function() {
        $('.datatable').DataTable();
    });
</script>
@endsection
@endsection
