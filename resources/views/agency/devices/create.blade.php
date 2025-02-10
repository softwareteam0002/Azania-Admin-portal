@extends('layouts.admin')
@section('content')
<div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success btn-sm" href="{{ url('agency/devices', [$id]) }}">
                Back to agent devices
            </a>
        </div>
    </div>
<div class="card">
    <div class="card-header">
        Assign Device
    </div>

    {{ $devices }}

    <div class="card-body">
        @if(Session::has('color'))
            <div class="alert alert-{{ Session::get('color') }} alert-dismissible fade show" role="alert">
                {{ Session::get('notification') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif


        <form action="{{ url('agency/device/store', [$id]) }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-12 form-group">
                    <label for="types">Choose device*</label>
                    <select name="device" class="form-control">
                        @foreach($devices as $device)
                            <option value="{{ $device->device_id }}" >{{ $device->terminal_ID }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 form-group mg-t-8">
                    <button type="submit" class="btn-fill-lg btn-gradient-yellow btn-hover-bluedark">Save</button>
                    <button type="reset" class="btn-fill-lg bg-blue-dark btn-hover-bluedark">Reset</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
