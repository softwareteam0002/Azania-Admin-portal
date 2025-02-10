@extends('layouts.admin')
@section('content')
<div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success btn-sm" href="{{ url('agency/users') }}">
                Back to agents
            </a>
        </div>
    </div>
<div class="card">
    <div class="card-header">
        Create Agent
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


        <form action="{{ url('agency/user/store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-12 form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label for="name">Full Name*</label>
                    <input type="text" id="name" name="agent_name" class="form-control" required>
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
                    <label for="account">Account number*</label>
                    <input type="text" id="account" name="account_number" class="form-control" required>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 form-group">
                    <label for="name">Valid Agent Number*</label>
                    <input type="text" name="agent_number" class="form-control" required>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 form-group">
                    <label for="name">Mobile Number*</label>
                    <input type="text" name="mobile_number" class="form-control" required>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 form-group">
                    <label for="name">Email Address</label>
                    <input type="email" name="email_address" class="form-control">
                </div>

                <div class="col-xl-3 col-lg-6 col-12 form-group">
                    <label for="name">Business Licence(TIN)*</label>
                    <input type="text" name="business_licence" class="form-control" required>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 form-group">
                    <label for="name">Registration Number*</label>
                    <input type="text" name="registration_number" class="form-control" required>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 form-group">
                    <label for="name">Address</label>
                    <input type="text" name="address" class="form-control" required>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 form-group">
                    <label for="name">Location</label>
                    <input type="text" name="location" class="form-control" required>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 form-group">
                    <label for="name">Float Limit*</label>
                    <input type="text" name="float_limit" class="form-control" required>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 form-group">
                    <label for="name">Daily Limit*</label>
                    <input type="text" name="daily_limit" class="form-control" required>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 form-group">
                    <label for="types">Languages</label>
                    <select name="language_id" class="form-control">
                        @foreach($languages as $language)
                            <option value="{{ $language->language_id }}" >{{ $language->language_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 form-group">
                    <label for="types">Status*</label>
                    <select name="status_id" class="form-control">
                        @foreach($statuses as $status)
                            <option value="{{ $status->status_id }}" >{{ $status->status }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xl-3 col-lg-6 col-12 form-group">
                    <label for="types">Branch Identity*</label>
                    <select name="branch_id" class="form-control">
                        @foreach($branches as $branch)
                            <option value="{{ $branch->branch_id }}" >{{ $branch->branch_name }}</option>
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
