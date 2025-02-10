@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            Assign Device
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


            <form action="{{ url('agency/device/store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-xl-3 col-lg-6 col-12 form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                        <label for="imei">Imei number*</label>
                        <input type="text" id="imei" name="imei" class="form-control" required>
                        @if($errors->has('imei'))
                            <em class="invalid-feedback">
                                {{ $errors->first('imei') }}
                            </em>
                        @endif
                        <p class="helper-block">
                            {{ trans('global.user.fields.name_helper') }}
                        </p>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-12 form-group">
                        <label for="name">Terminal ID*</label>
                        <input type="text" name="terminal" class="form-control" required>
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
