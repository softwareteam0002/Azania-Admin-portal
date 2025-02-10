@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Create Operator
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

        @include('error_handler')


        <form action="{{ url('agency/operator/store', [$id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-12 form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label for="name">First Name*</label>
                    <input type="text" id="name" name="fname" class="form-control" required>
                    @if($errors->has('name'))
                        <em class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('global.user.fields.name_helper') }}
                    </p>
                </div>
                <div class="col-xl-3 col-lg-6 col-12 form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label for="name">Last Name*</label>
                    <input type="text" id="name" name="lname" class="form-control" required>
                    @if($errors->has('name'))
                        <em class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('global.user.fields.name_helper') }}
                    </p>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 form-group">
                    <label for="name">Mobile Number*</label>
                    <input type="text" name="phone" class="form-control" required>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 form-group">
                    <label for="name">National Identity*</label>
                    <input type="text" name="national_id" class="form-control" required>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 form-group">
                    <label for="name">Location</label>
                    <input type="text" name="location" class="form-control" required>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 form-group">
                    <label for="types">Device*</label>
                    <select name="device" class="form-control">
                        <option value="0" >Select a device</option>
                        @foreach($devices as $device)
                            <option value="{{ $device->id }}" >{{ $device['device']->terminal_ID }}</option>
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
