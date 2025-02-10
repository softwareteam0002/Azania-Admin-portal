@extends('layouts.admin')
@section('content')
    <div class="row mb-3">
        <div class="col-md-8">
            <!-- Notifications-->
            @if(Session::has('color'))
                <div class="alert alert-{{ Session::get('color') }} alert-dismissible fade show" role="alert">
                    {{ Session::get('notification') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @include('error_handler')
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
            <li class="breadcrumb-item active">Commissions</li>
        </ol>
    </nav>

    <h5>Commissions</h5>
    <hr/>

    <div class="card mb-5">
        <div class="card-body">
            <h6>Approve Commission form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ route('agency.commission.approveAct', $commission->commision_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">

                    <input type="hidden" name="id" value="{{ $commission->commision_id }}">

                    <div class="col-md-3 form-group">
                    <label for="types">Service<span class="text-danger">*</span></label>
                    <input text="number"   name="service" class="form-control" value="{{$commission->service->agent_serviceName}}" disabled>
                    <input type="hidden" name="id" value="{{ $commission->commision_id }}">
                </div>

                <div class="col-md-3 form-group">
                    <label for="name">Bank Rate</label>
                    <input type="number" min="0" id="bank_rate" max="100" name="bank_rate" class="form-control" value="{{ $commission->bank_rate_value }}" readonly>
                </div>

                <div class="col-md-3 form-group">
                    <label for="name">Agent Rate</label>
                    <input type="number" min="0" id="agent_rate" max="100" name="agent_rate" class="form-control" value="{{ $commission->agent_rate_value }}" readonly>
                </div>

                <div class="col-md-3 form-group">
                    <label for="name">Third Party Rate</label>
                    <input type="number" min="0" id="third_party_rate" max="100" name="third_party_rate" class="form-control" value="{{ $commission->third_parties }}" readonly>
                </div>


                    <div class="col-md-12">
                        <hr/>
                        <input type="submit" name="approve" value="approve" class="btn btn-success btn-sm" value="Approve" />
                        <input type="submit" name="reject" value="reject" class="btn btn-primary btn-sm" value="Reject" />
                        <a href="{{ url('agency/commissions') }}" class="btn btn-link btn-sm"><i class="fas fa-arrow-left"></i> Go back</a>
                    </div>

                </div>



            </form>
        </div>
    </div>


    @endsection
