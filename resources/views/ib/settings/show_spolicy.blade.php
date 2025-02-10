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
            <li class="breadcrumb-item"><a href="{{ route('ib.sPolicy.index') }}">Password Policy details</a></li>
            <li class="breadcrumb-item active">Password Policy details</li>
        </ol>
    </nav>


    <h5>Password Policy Details</h5>
    <hr/>

    <div class="card">
        <div class="card-body">
            <h6 class="mb-3">Password Policy Details</h6>
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm">
                        <tbody>
                            <tr><td width="200px" class="font-weight-bold">Min Length:</td><td> {{$passwordPolicy->min_length}}</td></tr>
                            <tr><td  class="font-weight-bold">Maximum Length:</td><td> {{$passwordPolicy->max_length}}</td></tr>
                            <tr><td  class="font-weight-bold">Upper Case Char Count:</td><td>{{$passwordPolicy->uppercase_char_count ?? ''}}</td></tr>
                            <tr><td  class="font-weight-bold">Numeric Char Count:</td><td>{{$passwordPolicy->numeric_char_count ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Maximum Attempts:</td><td>{{$passwordPolicy->max_attempts ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Expiry Period:</td><td>{{$passwordPolicy->expiry_period ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Approval Status:</td>
                                <td> @if ($passwordPolicy->isWaitingApproval == 2) 
                                      <span class="badge badge-pill  badge-primary">Rejected</span>
                                      @elseif($passwordPolicy->isWaitingApproval == 1)
                                      <span class="badge badge-pill  badge-primary">Pending</span>
                                      @elseif($passwordPolicy->isWaitingApproval == 0)
                                      <span class="badge badge-pill  badge-primary">Approved</span>
                                     @endif
                                </td>
                            </tr>
                            <tr><td  class="font-weight-bold">Date Updated:</td><td>{{$passwordPolicy->updated_at ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Date Created:</td><td>{{$passwordPolicy->created_at ?? 'N/A'}}</td></tr>

                        </tbody>
                    </table>
                    <hr/>
                    <a href="{{ route('ib.sPolicy.index') }}" class="btn btn-sm btn-link">Go back</a>
                </div>
            </div>
        </div>
    </div>

@endsection
