@extends('layouts.admin')
@section('content')

    <div class="row mb-3">
        <div class="col-md-8">
            <!-- Notifications-->

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
            <li class="breadcrumb-item"><a href="{{ url('ib/payments/index') }}">Payments</a></li>
            <li class="breadcrumb-item active">Payments details</li>
        </ol>
    </nav>




    <div class="card">
        <div class="card-body">
            <h6 class="">Payment details ({{$payment->id}})</h6>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tbody>
                    <tr><td style="width:200px"><b>Account</b></td><td>{{$payment->fromAccountID}}</td></tr>
                    <tr><td><b>Reference Number</b></td><td>{{$payment->reference_number}}</td></tr>
                    <tr><td><b>Payment Amount</b></td><td>{{ number_format($payment->amount)}}</td></tr>
                    <tr><td><b>Mobile Number</b></td><td>{{$payment->mobile_number ?? 'N/A'}}</td></tr>
                    <tr><td><b>Control Number</b></td><td>{{$payment->control_number ?? 'N/A'}} </td></tr>
                    <tr><td><b>Status</b></td><td>{{$payment->status->name ?? ''}}</td></tr>
                    </tbody>
                </table>
                <hr/>
                <a href="{{ url('ib/payments/index') }}" class="btn btn-sm btn-link">Go back</a>
            </div>

        </div>
    </div>

@endsection
