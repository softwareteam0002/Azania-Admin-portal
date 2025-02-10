@extends('layouts.admin')
@section('content')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success btn-sm" href="{{ url('agency/commissions') }}">
                Back to commissions
            </a>
        </div>
    </div>
  {{--  <div class="card">
        <div class="card-header">
            Adding commission
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

            <form action="{{ url('agency/commission/store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-xl-3 col-lg-6 col-12 form-group">
                        <label for="types">Select service*</label>
                        <select name="service" class="form-control">
                            <option value="0">Please select service</option>
                            @foreach($services as $service)
                                <option value="{{ $service->agent_serviceID }}" >{{ $service->agent_serviceName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-12 form-group {{ $errors->has('device_imei2') ? 'has-error' : '' }}">
                        <label for="party">Party name*</label>
                        <input type="text" id="party" name="party" class="form-control" required>
                        @if($errors->has('party'))
                            <em class="invalid-feedback">
                                {{ $errors->first('party_name') }}
                            </em>
                        @endif
                    </div>

                    <div class="col-xl-3 col-lg-6 col-12 form-group">
                        <label for="name">Rate value*</label>
                        <input type="text" name="rate" class="form-control" required>
                    </div>

                    <div class="col-12 form-group mg-t-8">
                        <button type="submit" class="btn-fill-lg btn-gradient-yellow btn-hover-bluedark">Save</button>
                        <button type="reset" class="btn-fill-lg bg-blue-dark btn-hover-bluedark">Reset</button>
                    </div>
                </div>
            </form>
        </div>
    </div>--}}

    <div class="card">
        <div class="card-header">
            Adding commission
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

            <form action="{{ url('agency/commission/store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-xl-3 col-lg-6 col-12 form-group">
                        <label for="types">Select service*</label>
                        <select name="service" class="form-control">
                            <option value="0">Please select service</option>
                            @foreach($services as $service)
                                <option value="{{ $service->agent_serviceID }}" >{{ $service->agent_serviceName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-12 form-group">
                        <label for="types">Select party*</label>
                        <select name="party" class="form-control">
                            <option value="0">Please select party</option>
                            @foreach($parties as $party)
                                <option value="{{ $party->id }}" >{{ $party->party_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-12 form-group">
                        <label for="name">Rate value*</label>
                        <input type="text" name="rate" class="form-control" required>
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
