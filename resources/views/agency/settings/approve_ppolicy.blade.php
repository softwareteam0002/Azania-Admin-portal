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
        <li class="breadcrumb-item active">Security Policies</li>
    </ol>
</nav>

<h5>Security Policies</h5>
<hr />



<div class="row">

    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <h6>PIN Policies</h6>
                <hr />
                    <h6>Approve PIN Policy</h6>
                    <form action="{{ route('agency.pPolicy.approveAct', $pinpolicy->id) }}" method="POST">
                        @csrf
                        {!! method_field('PUT') !!}
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name">Min Length:<span class="text-danger">*</span></label>
                                    <input type="text" name="min_length" class="form-control form-control-sm" value="{{ $pinpolicy->min_length }}" readonly>
                                    <input type="hidden" name="id" class="form-control form-control-sm" value="{{ $pinpolicy->id }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name">Max Length:<span class="text-danger">*</span></label>
                                    <input type="text" name="max_length" class="form-control form-control-sm" value="{{ $pinpolicy->max_length }}" readonly>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name">Max Attempts:<span class="text-danger">*</span></label>
                                    <input type="text" name="max_attempts" class="form-control form-control-sm" value="{{ $pinpolicy->max_attempts }}" readonly>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name">Expiry:<span class="text-danger">*</span></label>
                                    <input type="text" name="expiry_period" class="form-control form-control-sm" value="{{ $pinpolicy->expiry_period }}" readonly>
                                </div>
                            </div>

                            <div class="col-md-12">
                            <hr/>
                                <input type="submit" name="approve" value="approve" class="btn btn-success btn-sm" value="Approve" />
                                <input type="submit" name="reject" value="reject" class="btn btn-primary btn-sm" value="Reject" />
                                <a href="{{ route('agency.sPolicy.index') }}" class="btn btn-link btn-sm"><i class="fas fa-arrow-left"></i> Go back</a>
                            </div>
                            <br/>
                            <br/>
                            <br/>
                            <br/>

                        </div>
                    </form>

            

                   

                </div>
               </div>
           </div>


           
     </div>
  



    @endsection
