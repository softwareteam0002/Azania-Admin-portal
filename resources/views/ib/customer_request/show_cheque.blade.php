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
            <li class="breadcrumb-item"><a href="{{ url('ib/cheque/request') }}">Cheque requests</a></li>
            <li class="breadcrumb-item active">Cheque requests details</li>
        </ol>
    </nav>


    <h5>Cheque requests details</h5>
    <hr/>

    <div class="card">
        <div class="card-body">
            <h6 class="mb-3">Request Details</h6>
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm">
                        <tbody>
                            <tr><td width="200px" class="font-weight-bold">Number of books:</td><td> {{$request->number_of_books ?? ''}}</td></tr>
                            <tr><td  class="font-weight-bold">Number of leaves:</td><td> {{$request->number_of_leaves ?? ''}}</td></tr>
                            <tr><td  class="font-weight-bold">Request status:</td><td> 
                                @if($request->status_id == 1)

                                    <h5><span class="badge badge-danger">Submitted</span></h5>

                                @endif
                                @if($request->status_id == 2)

                                    <h5><span class="badge badge-warning">On Progress</span></h5>

                                @endif
                                @if($request->status_id == 4)

                                    <h5><span class="badge badge-success">Success</span></h5>

                                @endif
                                @if($request->status_id == 6)

                                    <h5><span class="badge badge-danger">Failed</span></h5>

                                @endif</td></tr>
                            <tr><td  class="font-weight-bold">Currency:</td>
                     
                          <td>{{$request->currency_id ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Description:</td><td>{{$request->status->description ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Transaction ID:</td><td>{{$request->transactionId ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Account Number:</td><td>{{$request->account_number ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Date Created:</td><td>{{$request->created_at ?? 'N/A'}}</td></tr>

                        </tbody>
                    </table>
                    <hr/>
                    <a href="{{ url('ib/cheque/request') }}" class="btn btn-sm btn-link">Go back</a>
                </div>
            </div>
        </div>
    </div>

@endsection
