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
            <li class="breadcrumb-item"><a href="{{ url('ib/accounts/index') }}">Accounts</a></li>
            <li class="breadcrumb-item active">User Approval</li>
        </ol>
    </nav>


    <h5>User Approval</h5>
    <hr/>

    <div class="card">

        <div class="card-body">
            <h6>User Approval form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>

            <form action="{{ route('ib.user.approveAct', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                     <div class="col-md-4">
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label for="name">{{ trans('global.user.fields.name') }}:<span class="text-danger">*</span></label>
                            <input type="text" id="name_" name="name" class="form-control form-control-sm" value="{{ old('name', isset($user) ? $user->name : '') }}" readonly>
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
                        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                            <label for="email">{{ trans('global.user.fields.email') }}:<span class="text-danger">*</span></label>
                            <input type="email" id="email_" name="email" class="form-control form-control-sm" value="{{ old('email', isset($user) ? $user->email : '') }}" readonly>
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
                        <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                            <label for="email">{{ trans('global.phone') }}:<span class="text-danger">*</span></label>
                            <input type="text" id="phone" name="phone" class="form-control form-control-sm" value="{{ $user-> mobile_phone}}" readonly>
                            @if($errors->has('phone'))
                                <em class="invalid-feedback">
                                    {{ $errors->first('phone') }}
                                </em>
                            @endif

                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="types">OTP Options:<span class="text-danger">*</span></label>
                            <select name="option_id" class="form-control form-control-sm" readonly>
                                @foreach($options as $option)

                                    <option value="{{ strtoupper($user->otp_option) ?? '' }}"   @if(strtoupper($user->otp_option)==strtoupper($option->option_name)) selected @endif>{{ strtoupper($option->option_name) ?? '' }}</option>

                                @endforeach
                            </select>
                        </div>
                    </div>
                     
                     <div class="row">
                <div class="col-md-12">
                   <table class="table table-responsive-sm table-striped table-sm">
                    <thead>
                    <tr>
                        <th width="10"></th>
                        <th>Account Number</th>
                        <th>Account Name</th>
                        <th>Account Type</th>
                        <th>Branch</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($accounts as $trxn)
                        <tr data-entry-id="{{$trxn->id}}" data-toggle="modal" data-target="#largeModal{{ $trxn->id }}">
                            <td></td>
                            <td>{{$trxn->accountID}}</td>
                            <td>{{$trxn->accountName}}</td>
                            <!--<td>{{$trxn->types->name ?? ''}}</td>-->
							<td>{{$trxn->productName ?? ''}}</td>
                            <td>{{$trxn->branchName ?? ''}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
            </div>
              <div class="col-md-12">
                        <hr/>
                        <input type="submit" name="approve" value="approve" class="btn btn-success btn-sm" value="Approve" />
                        <input type="submit" name="reject" value="reject" class="btn btn-primary btn-sm" value="Reject" />
                        <a href="{{ url('ib/user') }}" class="btn btn-link btn-sm"><i class="fas fa-arrow-left"></i> Go back</a>
                    </div>
                </div>

            </form>
        </div>
    </div>

@endsection
