@extends('layouts.admin')
@section('content')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success btn-sm" href="{{ url('ib/charges') }}">
                Back to Charges
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Add Service
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

            <form action="{{ url('ib/charges/store') }}" method="POST">
                @csrf
                @method('POST')
                <div class="row">
                    <div class="col-xl-3 col-lg-6 col-12 form-group">
                        <label for="name">Charge Amount*</label>
                        <input type="text" name="charge_amount" class="form-control" required>
                    </div>

<br/>

		            <div class="col-xl-3 col-lg-6 col-12form-group">
                        <label for="name">Service Name*</label>
                        <input type="text" name="service_name" class="form-control"  required>
                    </div>


{{--TODO: Add type: Fixed, Tier, Percentage--}}

                    <div class="col-12 form-group mg-t-8">
                        <button type="submit" class="btn-fill-lg btn-gradient-yellow btn-hover-bluedark">Save</button>

                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
