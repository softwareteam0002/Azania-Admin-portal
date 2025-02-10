@extends('layouts.admin')
@section('content')
<div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success btn-sm" href="{{ url('admin/devices') }}">
                Back to devices
            </a>
        </div>
    </div>
<div class="card">
    <div class="card-header">
        Create Device
    </div>

    <div class="card-body">
        @if(Session::has('color'))
            <div class="alert alert-{{ Session::get('color') }} alert-dismissible fade show" role="alert">
                {{ Session::get('notification') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif


        <form action="{{ url('admin/device/store') }}" method="POST">
            @csrf
            <div class="row">
<div class="col-xl-3 col-lg-6 col-12 form-group {{ $errors->has('device_imei1') ? 'has-error' : '' }}">
                    <label for="imei1">Imei number 1*</label>
                    <input type="text" id="imei1" name="device_imei1" class="form-control" required>
                    @if($errors->has('device_imei1'))
                        <em class="invalid-feedback">
                            {{ $errors->first('device_imei1') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('global.user.fields.name_helper') }}
                    </p>
                </div>
                <div class="col-xl-3 col-lg-6 col-12 form-group {{ $errors->has('device_imei2') ? 'has-error' : '' }}">
                    <label for="imei2">Imei number 2</label>
                    <input type="text" id="imei2" name="device_imei2" class="form-control" required>
                    @if($errors->has('device_imei2'))
                        <em class="invalid-feedback">
                            {{ $errors->first('imei2') }}
                        </em>
                    @endif
                </div>
                <div class="col-xl-3 col-lg-6 col-12 form-group">
                    <label for="name">Terminal ID*</label>
                    <input type="text" name="terminal" class="form-control" required>
                </div>

                <div class="col-12 form-group mg-t-8">
                    <button type="submit" class="btn btn-sm btn-success">Save</button>
                    <button type="reset" class="btn btn-sm btn-link">Reset</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
