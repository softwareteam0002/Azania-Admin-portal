@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('global.user.title_singular') }}
    </div>

    <div class="card-body">
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
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            @method('POST')
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-12 form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label for="fname">{{ trans('global.first_name') }}*</label>
                    <input type="text" id="fname" name="fname" class="form-control" value="{{ old('fname', isset($user) ? $user->name : '') }}" required>
                    @if($errors->has('fname'))
                        <em class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('global.user.fields.name_helper') }}
                    </p>
                </div>
                <div class="col-xl-3 col-lg-6 col-12 form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label for="lname">{{ trans('global.last_name') }}*</label>
                    <input type="text" id="lname" name="lname" class="form-control" value="{{ old('lname', isset($user) ? $user->name : '') }}" required>
                    @if($errors->has('name'))
                        <em class="invalid-feedback">
                            {{ $errors->first('lname') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('global.user.fields.name_helper') }}
                    </p>
                </div>
                <div class="col-xl-3 col-lg-6 col-12 form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                    <label for="email">{{ trans('global.user.fields.email') }}*</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email', isset($user) ? $user->email : '') }}" required>
                    @if($errors->has('email'))
                        <em class="invalid-feedback">
                            {{ $errors->first('email') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('global.user.fields.email_helper') }}
                    </p>
                </div>
                {{--<div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                    <label for="password">{{ trans('global.user.fields.password') }}</label>
                    <input type="password" id="password" name="password" class="form-control" disabled>
                    @if($errors->has('password'))
                        <em class="invalid-feedback">
                            {{ $errors->first('password') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('global.user.fields.password_helper') }}
                    </p>
                </div>--}}
                <div class="col-xl-3 col-lg-6 col-12 form-group {{ $errors->has('roles') ? 'has-error' : '' }}">
                    <label for="roles">{{ trans('global.user.fields.roles') }}*
                        {{--<span class="btn btn-info btn-sm select-all">Select all</span>
                        <span class="btn btn-info btn-sm deselect-all">Deselect all</span></label>
                    <select name="roles[]" id="roles" class="form-control select2" multiple="multiple">
                        @foreach($roles as $id => $roles)
                            <option value="{{ $id }}" {{ (in_array($id, old('roles', [])) || isset($user) && $user->roles->contains($id)) ? 'selected' : '' }}>
                                {{ $roles }}
                            </option>
                        @endforeach
                    </select>--}}
                        @foreach($roles as $role)
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="roles[]"
                                       id="roles{{ $role->id }}" value="{{ $role->id }}">&nbsp; {{$role->display_name}}
                            </div>
                        @endforeach
                    @if($errors->has('roles'))
                        <em class="invalid-feedback">
                            {{ $errors->first('roles') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('global.user.fields.roles_helper') }}
                    </p>
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
