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
            <li class="breadcrumb-item"><a href="{{ url('ib/charges') }}">Service charges</a></li>
            <li class="breadcrumb-item active">Edit service charges</li>
        </ol>
    </nav>

    <h5>Edit service charge</h5>



    <hr/>
    <div class="card mb-5">
        <div class="card-body">
            <h6>Edit service charge form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ url('ib/charges',[$service->charge_id]) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $id }}">
                <input type="hidden" name="charge_id" value="{{ $service->charge_id }}">
                <input type="hidden" name="charge_type" value="{{ $service->charge_type_id }}">
                <input type="hidden" name="service_name" value="{{ $service->service_id }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="types">Charge Type:<span class="text-danger">*</span></label>
                            <select  class="form-control form-control-sm" disabled="true">
                                @foreach($types as $type)
                                    <option value="{{ $type->charges_type_id ?? ''}}" @if($service->charge_type_id==$type->charges_type_id ) selected @endif>{{ $type->charge_type ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="types">Service Name:<span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm" disabled="true">
                                @foreach($agentservices as $agentservice)

                                    <option value="{{ $agentservice->agent_serviceID ?? '' }}" @if($service->service_id==$agentservice->agent_serviceID) selected="selected" @endif>{{ $agentservice->agent_serviceName ?? '' }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">

                    </div>
                </div>

                <p class="small text-danger">Values below are optional, based on charge type selected.</p>
                @if($service->charge_type_id==1 || $service->charge_type_id==3)
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="name">Charge Amount:</label>

                                @if($service->charge_type_id==1)
                                    <input type="text" name="charge_amount" value="{{ $service->charge_amount }}" class="form-control form-control-sm">
                                @endif
                                @if($service->charge_type_id==3)
                                    <input type="text" name="charge_amount" value="{{ $service->charge_amount }}" class="form-control form-control-sm">
                                @endif
                            </div>
                        </div>

                        @endif

                        @if($service->charge_type_id==3 || $service->charge_type_id==5)
                            <div class="col">
                                <div class="form-group">
                                    <label for="name">Start Amount:</label>
                                    <input type="text" name="from_amount" value="{{ $service->from_amount ?? '' }}" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-group">
                                    <label for="name">End Amount:</label>
                                    <input type="text" name="to_amount" value="{{ $service->to_amount ?? '' }}" class="form-control form-control-sm">
                                </div>
                            </div>
                        @endif

                        @if($service->charge_type_id==2 || $service->charge_type_id==5)
                            <div class="col">
                                <div class="form-group">
                                    @if($service->charge_type_id==2)
                                        <label for="name">Percentage:</label>
                                        <input type="text" name="charge_percent" value=" {{ $service->percentage_value }}" class="form-control form-control-sm">
                                    @endif
                                    @if($service->charge_type_id==5)
                                        <label for="name">Percentage:</label>
                                        <input type="text" name="charge_percent" value="{{ $service->charges_percent }}" class="form-control form-control-sm">
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>


                    <div class="col-md-12">
                        {{--<hr/>--}}
                        <button type="submit" class="btn btn-success btn-sm">{{ trans('global.save') }}</button>
                        <a href="{{ url('ib/charges') }}" class="btn btn-link btn-sm">Go back</a>
                    </div>

                    <br>
                    {{--<br>--}}
                    <br>
            </form>
        </div>
    </div>

@endsection
