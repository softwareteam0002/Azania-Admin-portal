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
            Update Charges
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

            <form action="{{ url('ib/charges/update',[$charge->charge_id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-xl-3 col-lg-6 col-12 form-group">
                        <label for="name">Charge Amount*</label>
                        <input type="text" name="charge_amount" class="form-control"  value="{{ $charge->charge_amount }}"  required>
                    </div>

                    <br/>

                    <div class="col-xl-3 col-lg-6 col-12form-group">
                        <label for="name">Service Name*</label>
                        <input type="text" name="service_name" class="form-control" value="{{ $charge->service_name }}"  required>
                    </div>


                    <div class="col-12 form-group mg-t-8">
                        <button type="submit" class="btn btn-sm btn-success">Update</button>

                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
