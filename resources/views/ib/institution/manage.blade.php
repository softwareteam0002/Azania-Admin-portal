@extends('layouts.admin')
@section('content')

    <div class="row mb-3">
        <div class="col-md-8">
            <!-- Notifications-->
            @if(session('notification') && session('color'))
                <div class="alert alert-{{ session('color') }} alert-dismissible fade show" role="alert">
                    {{ session('notification') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Create Action Notifications -->
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    @foreach($errors->all() as $error)
                        <li class="list-group-item">
                            {{ $error }}
                        </li>
                    @endforeach
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
            <li class="breadcrumb-item"><a href="{{ url('ib/institutions/index') }}">Institutions</a></li>
            <li class="breadcrumb-item active">Manage Institution</li>
        </ol>
    </nav>


    <h5>Manage Institutions</h5>
    <hr/>
    <div class="card">
        <div class="card-body">
            {{ $institution }}
        </div>
    </div>

    <h6>Institution users</h6>
    <hr/>
    <div class="card">
            <div class="card-body">
                <h6 class="">Create institution user form</h6>
                <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
                <form action="{{ url('ib/institution/user_store') }}" method="POST">
                    @csrf
                    @method('POST')

                    <div class="row">
                        <div class="col-md-3">
                            <div id="name_input_group" class="form-group has-error {{ $errors->has('name') ? 'has-error' : '' }}">
                                <label for="name">Full Name:<span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control form-control-sm" value="{{ old('name', isset($user) ? $user->name : '') }}" placeholder="Enter the full name here.">
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

                        <div class="col-md-2">
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


                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="types">OTP Options:<span class="text-danger">*</span></label>
                                <select name="option_id" class="form-control  form-control-sm">
                                    <option value="0" >Select OTP Option</option>
                                    @foreach($options as $option)
                                        <option value="{{ strtoupper($option->option_name) }}" >{{ strtoupper($option->option_name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="roles">User Role:<span class="text-danger">*</span></label>
                                <select name="role_id" class="form-control form-control-sm">
                                    <option value="0" >Select Role</option>
                                    @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" >
                                        {{$role->display_name}}
                                    </option>

                                    @endforeach
                                </select>
                            </div>


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


        <h6>Registered  institution users.</h6>
        <hr/>
        <div class="card">
            <div class="card-body">
                <h6 class="mb-4">All registered users.</h6>
                <div class="table-responsive">
                    <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                        <thead>
                        <tr><th width="10"> </th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile Number</th>
                            <th>Verified</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $key => $user)
                        <tr data-entry-id="1" data-toggle="modal" data-target="#largeModal{{$user->id}}" id="loadingModel" data="{{$user->id}}">
                            <td></td>
                            <td>{{$user->name}}</td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->mobile_phone ?? 'N/A'}}</td>
                            <td>
                                @if($user->isVerified == 1)
                                    <span class="badge badge-danger">Not Verified</span>
                                @else
                                    <span class="badge badge-success">Verified</span>
                                @endif
                            </td>
                        <td>
						 @if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)
                            <a class="btn btn-primary btn-sm" href="{{ url('ib/institution/user_edit/'.$user->id ) }}">
                                <i class="fas fa-edit text-dark-pastel-green"></i>Edit
                            </a>
							@endif
                        </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>




<!--  Create instution Route: {{ url('ib/institution/create') }} -->

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.datatable').DataTable();
        } );
    </script>
@endsection

@endsection
