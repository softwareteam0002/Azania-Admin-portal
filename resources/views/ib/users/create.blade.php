@extends('layouts.admin')
@section('content')

    <div class="row mb-3">
        <div class="col-md-8">
            <!-- Notifications-->
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{$error}}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(Session::has('message'))
                <div class="alert alert-success alert-dismissible">
                    <ul>
                        <li>{{Session::get('message')}}</li>
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(isset($notification) && isset($color))
            <div class="alert alert-{{ $color }} alert-dismissible fade show" role="alert">
                {{ $notification }}
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
            <li class="breadcrumb-item"><a href="{{ url('ib/user') }}">Manage users</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create user</li>
        </ol>
    </nav>

    <div class="card">

        <div class="card-body">
            <h6 class="">Create user form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ url('ib/user/store') }}" method="POST">
                @csrf
                @method('POST')
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label for="name">{{ trans('global.user.fields.name') }}:<span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control form-control-sm" value="{{ old('name', isset($user) ? $user->name : '') }}" placeholder="Enter the name here.">
                            @if($errors->has('name'))
                                <em class="invalid-feedback">
                                    {{ $errors->first('name') }}
                                </em>
                            @endif
                            <p class="helper-block">
                                {{ trans('global.user.fields.name_helper') }}
                            </p>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                            <label for="email">{{ trans('global.phone') }}:</label>
                            <input type="text" id="phone" name="phone" class="form-control form-control-sm" value="{{ old('phone') }}" placeholder="Enter phone number here.">
                            @if($errors->has('phone'))
                                <em class="invalid-feedback">
                                    {{ $errors->first('phone') }}
                                </em>
                            @endif
                            <p class="helper-block">
                               <!-- {{ trans('global.phone') }} -->
                            </p>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                            <label for="email">{{ trans('global.user.fields.email') }}:<span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" class="form-control form-control-sm" value="{{ old('email', isset($user) ? $user->email : '') }}" placeholder="Enter email address here.">
                            @if($errors->has('email'))
                                <em class="invalid-feedback">
                                    {{ $errors->first('email') }}
                                </em>
                            @endif
                            <p class="helper-block">
                                {{ trans('global.user.fields.email_helper') }}
                            </p>
                        </div>
                    </div>


                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="types">Institutions:</label>
                            <select name="institute_id" class="form-control form-control-sm">
                                <option value="0" >Select institution if registering institution</option>
                                @foreach($institutions as $institute)
                                    <option value="{{ $institute->id }}" >{{ $institute->institute_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Password:<span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control form-control-sm" required>
                            <p class="small">Passwords must have uppercase, lowercase, numbers and symbols.</p>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Confirm Password:<span class="text-danger">*</span></label>
                            <input type="password" name="password_confirm" class="form-control form-control-sm" required>
                            <p class="small">Passwords must have uppercase, lowercase, numbers and symbols.</p>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="types">OTP Options:<span class="text-danger">*</span></label>
                            <select name="option_id" class="form-control  form-control-sm">
                                <option value="0" >Select OTP Option</option>
                                @foreach($options as $option)
                                    <option value="{{ $option->id }}" >{{ $option->option_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="types">No. of Approvers:<span class="text-danger">*</span></label>
                            <select name="no_approvers" class="form-control form-control-sm">
                                    <option value="1" >1</option>
                                    <option value="2" >2</option>
                                    <option value="3" >3</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="roles">User Role:<span class="text-danger">*</span></label>
                            <select name="role_id" class="form-control form-control-sm">
                                @foreach ($roles as $role)
                                <option value="{{ $role->id }}" >
                                    {{$role->display_name}}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{--<br/><div class="form-group">--}}
                        {{--<label for="roles" class="label">Roles*</label>--}}
                        {{--@foreach ($roles as $role)--}}
                        {{--<div class="field">--}}
                        {{--<input type="checkbox" name="roles[]" value="{{$role->id}}"/> {{$role->display_name}} ({{($role->description)}}).--}}
                        {{--</div>--}}
                        {{--@endforeach--}}
                        {{--</div>--}}
                    </div>

                    <div class="col-md-12">
                        <hr/>
                        <!-- <input class="btn btn-success form-control" type="submit" value="{{ trans('global.save') }}"> -->
                        <button type="submit" class="btn btn-success btn-sm">Save</button>
                        <button type="reset" class="btn btn-link btn-sm">Clear form</button>
                    </div>
                </div>
            </form>
        </div>

    </div>




@endsection
