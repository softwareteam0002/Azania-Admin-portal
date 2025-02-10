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
    <hr />
</div>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb small" style="background-color:#f9f9f9; padding: .55rem 1rem;">
        <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ url('agency/commissions') }}">Commsion</a></li>
        <li class="breadcrumb-item active">Edit Commisions</li>
    </ol>
</nav>


<div class="card">
    <div class="card-body">
        <h6>Edit commission form</h6>
        <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
        <form action="{{ url('agency/commission/update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-3 form-group">
                    <label for="types">Service<span class="text-danger">*</span></label>
                    <input text="number"   name="service" class="form-control" value="{{$commission->service->agent_serviceName}}" disabled>
                    <input type="hidden" name="id" value="{{ $commission->commision_id }}">
                </div>

                <div class="col-md-3 form-group">
                    <label for="name">Bank Rate</label>
                    <input type="number" min="0" id="bank_rate" max="100" name="bank_rate" class="form-control" value="{{ $commission->bank_rate_value }}">
                </div>

                <div class="col-md-3 form-group">
                    <label for="name">Agent Rate</label>
                    <input type="number" min="0" id="agent_rate" max="100" name="agent_rate" class="form-control" value="{{ $commission->agent_rate_value }}">
                </div>

                <div class="col-md-3 form-group">
                    <label for="name">Third Party Rate</label>
                    <input type="number" min="0" id="third_party_rate" max="100" name="third_party_rate" class="form-control" value="{{ $commission->third_parties }}">
                </div>

                <div class="col-12">
                    <hr/>
                    <button type="submit" class="btn btn-sm btn-success">Save</button>
                    <a href="{{ url('agency/commissions') }}" class="btn btn-sm btn-link"><i class="fas fa-arrow-left"></i> Go back</a>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
