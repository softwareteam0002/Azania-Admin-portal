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
            @if (session()->has('exchange_rate_message'))
                 <div class="alert alert-success">
                     <li>{{ session()->get('exchange_rate_message') }}</li>
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

            @if(Session::has('notification') && Session::has('color'))
                <div class="alert alert-{{ session('color') }} alert-dismissible fade show" role="alert">
                    {{ session('notification') }}
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
            <li class="breadcrumb-item"><a href="{{ url('ib/exchange_rate/index') }}">Manage exchange rates</a></li>
            <li class="breadcrumb-item active">Edit Exchange Rates</li>
        </ol>
    </nav>


    <h5>Edit user</h5>
    <hr/>

    <div class="card">
        <!--
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('global.user.title_singular') }} Internet banking user
        </div>
        -->
            
            {{-- $user --}}
{{--
        <div class="card-body">
            <h6>Edit Exchange Rates</h6>
            <p class="small text-danger mb-2">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ url('ib/user/update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $user->id }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label for="name">{{ trans('global.user.fields.name') }}:<span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control form-control-sm" value="{{ old('name', isset($user) ? $user->name : '') }}">
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
                            <input type="email" id="email" name="email" class="form-control form-control-sm" value="{{ old('email', isset($user) ? $user->email : '') }}">
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
                            <input type="text" id="phone" name="phone" class="form-control form-control-sm" value="{{ $user-> mobile_phone}}">
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
                            <select name="option_id" class="form-control form-control-sm">
                                <option value="0" >Select OTP Option</option>
                                @foreach($options as $option)

                                    <option value="{{ strtoupper($user->otp_option) ?? '' }}"   @if(strtoupper($user->otp_option)==strtoupper($option->option_name)) selected @endif>{{ strtoupper($option->option_name) ?? '' }}</option>

                                @endforeach
                            </select>
                        </div>
                    </div>
                    <input type="hidden" value="{{ $user->role_id }}" name="role_id">
                    <div class="col-md-12">
                        <hr/>
                        <button type="submit" class="btn btn-success btn-sm">{{ trans('global.save') }}</button>
                        <a href="{{ url('ib/user') }}" class="btn btn-link btn-sm"><i class="fas fa-arrow-left"></i> Go back</a>
                    </div>
                </div>
            </form>--}}
            <form action="{{ route('ib.exchange_rate.update', $exchangeRate->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
            
                        <div class="col-md-4">
                            <div id="name_input_group" class="form-group has-error {{ $errors->has('foreign_currency') ? 'has-error' : '' }}">
                                <label for="foreign_currency">Foreign Currency:<span class="text-danger">*</span></label>
                                <input type="text" id="foreign_currency" name="foreign_currency" class="form-control form-control-sm" value="{{ old('foreign_currency', isset($exchangeRate) ? $exchangeRate->foreign_currency : '') }}" placeholder="Enter the full foreign_currency here.">
                                @if($errors->has('foreign_currency'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('foreign_currency') }}
                                    </em>
                                @endif
                                <p class="helper-block">
                                    {{ trans('global.exchange_rate.fields.foreign_currency_helper') }}
                                </p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group {{ $errors->has('currency_code') ? 'has-error' : '' }}">
                                <label for="currency_code">{{ trans('global.exchange_rate.fields.currency_code') }}:<span class="text-danger">*</span></label>
                                <input type="text" id="currency_code" name="currency_code" class="form-control form-control-sm" value="{{ old('currency_code', isset($exchangeRate) ? $exchangeRate->currency_code : '') }}" placeholder="Enter currency code here.">
                                @if($errors->has('currency_code'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('currency_code') }}
                                    </em>
                                @endif
                                <p class="helper-block">
                                    {{ trans('global.exchange_rate.fields.currency_code_helper') }}
                                </p>
                            </div>
                        </div>


                        <div class="col-md-3">
                            <div class="form-group {{ $errors->has('mean_rate') ? 'has-error' : '' }}">
                                <label for="mean_rate">{{ trans('global.exchange_rate.fields.mean_rate') }}:</label>
                                <input type="text" id="mean_rate" name="mean_rate" class="form-control form-control-sm number" value="{{ old('mean_rate', $exchangeRate->mean_rate ?? '') }}" placeholder="Enter mean rate number here.">
                                @if($errors->has('mean_rate'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('mean_rate') }}
                                    </em>
                                @endif
                                <p class="helper-block">
                                <!-- {{ trans('global.phone') }} -->
                                </p>
                            </div>
                        </div>
                             <div class="col-md-3">
                            <div class="form-group {{ $errors->has('buying_price') ? 'has-error' : '' }}">
                                <label for="buying_price">{{ trans('global.exchange_rate.fields.buying_price') }}:</label>
                                <input type="text" id="buying_price" name="buying_price" class="form-control form-control-sm number" value="{{ old('buying_price', $exchangeRate->buying_price ?? '') }}" placeholder="Enter buying_price number here.">
                                @if($errors->has('buying_price'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('buying_price') }}
                                    </em>
                                @endif
                                <p class="helper-block">
                                <!-- {{ trans('global.phone') }} -->
                                </p>
                            </div>
                        </div>

                             <div class="col-md-3">
                            <div class="form-group {{ $errors->has('selling_price') ? 'has-error' : '' }}">
                                <label for="selling_price">{{ trans('global.exchange_rate.fields.selling_price') }}:</label>
                                <input type="text" id="selling_price" name="selling_price" class="form-control form-control-sm number" value="{{ old('selling_price', $exchangeRate->selling_price ?? '') }}" placeholder="Enter selling_price number here.">
                                @if($errors->has('selling_price'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('selling_price') }}
                                    </em>
                                @endif
                                <p class="helper-block">
                                <!-- {{ trans('global.phone') }} -->
                                </p>
                            </div>
                        {{--<div class="col-md-2">
                            <div class="form-group">
                                <label for="types">OTP Options:<span class="text-danger">*</span></label>
                                <select name="option_id" class="form-control  form-control-sm">
                                    <option value="0" >Select OTP Option</option>
                                    @foreach($options as $option)
                                        <option value="{{ strtoupper($option->option_name) }}" >{{ strtoupper($option->option_name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>--}}

                        {{-- Since all non cooperate users are customers , so no need to select just bind the value--}}
                        <input type="hidden" name="role_id" value="3">

                        <div class="col-md-12">
                            <hr/>
                           <button type="submit" class="btn btn-success btn-sm">{{ trans('global.update') }}</button>
                           <a href="{{ route('ib.exchange_rate.index') }}" class="btn btn-link btn-sm"><i class="fas fa-arrow-left"></i> Go back</a>
                        </div>
                    </div>
                </form>
        </div>
    </div>

@endsection
