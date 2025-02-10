@extends('layouts.admin')
@section('content')
<div class="row mb-3">
    <div class="col-md-8">
            @include('error_handler')
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
                <h6>Password Policies</h6>
                <hr />
                <div class="collapse" id="editPinPolicy">
                    <h6>Edit Policy</h6>
                    <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
                    <form action="{{ url('ib/securitypolicies/password') }}" method="POST">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="name">Min Length:<span class="text-danger">*</span></label>
                                    <input type="text" name="min_length" class="form-control form-control-sm" value="{{ $passwordpolicy[0]->min_length }}">
                                    <input type="hidden" name="id" class="form-control form-control-sm" value="{{ $passwordpolicy[0]->id }}">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="name">Max Length:<span class="text-danger">*</span></label>
                                    <input type="text" name="max_length" class="form-control form-control-sm" value="{{ $passwordpolicy[0]->max_length }}">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="name">Upercase Count:<span class="text-danger">*</span></label>
                                    <input type="text" name="uppercase_count" class="form-control form-control-sm" value="{{ $passwordpolicy[0]->uppercase_char_count }}">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="name">Numeric Count:<span class="text-danger">*</span></label>
                                    <input type="text" name="numeric_count" class="form-control form-control-sm" value="{{ $passwordpolicy[0]->numeric_char_count }}">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="name">Max Attempts:<span class="text-danger">*</span></label>
                                    <input type="text" name="max_attempts" class="form-control form-control-sm" value="{{ $passwordpolicy[0]->max_attempts }}">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="name">Expiry:<span class="text-danger">*</span></label>
                                    <input type="text" name="expiry_period" class="form-control form-control-sm" value="{{ $passwordpolicy[0]->expiry_period }}">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <hr />
                                <button type="submit" class="btn btn-success btn-sm">Save</button>
                                <a href="#editPinPolicy" data-toggle="collapse" class="btn btn-sm btn-link">Cancel</a>
                            </div>
                            <br/>
                            <br/>
                            <br/>
                            <br/>

                        </div>
                    </form>

                </div>

                <table class="table table-striped table-sm table-hover datatable">
                    <thead>
                        <tr>
                            <th>Min Length</th>
                            <th>Max Length</th>
                            <th>Uppercase Count</th>
                            <th>Numeric Count</th>
                            <th>Max Attempts</th>
                            <th>Expiry (Days)</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $passwordpolicy[0]->min_length }}</td>
                            <td>{{ $passwordpolicy[0]->max_length }}</td>
                            <td>{{ $passwordpolicy[0]->uppercase_char_count }}</td>
                            <td>{{ $passwordpolicy[0]->numeric_char_count }}</td>
                            <td>{{ $passwordpolicy[0]->max_attempts }}</td>
                            <td>{{ $passwordpolicy[0]->expiry_period }}</td>
                            <td>
                                @if($passwordpolicy[0]->isWaitingApproval  == 1) 
                                <span class="badge badge-info"> {{ __('Pending')}} </span>
                                 @elseif($passwordpolicy[0]->isWaitingApproval  == 2) 
                                  <span class="badge badge-danger"> {{ __('rejected') }} </span> 
                                  @elseif($passwordpolicy[0]->isWaitingApproval  == 0) 
                                  <span class="badge badge-success"> {{ __('approved')}} </span> 
                                @endif
                            </td>
                            <td>
                             @if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)      
                            <a title="Edit" href="#editPinPolicy" data-toggle="collapse" class="btn btn-sm btn-primary">
                                <span><i class="fas fa-edit"></i></span>
                            </a>
                            @endif

                            @if(((auth()->user()->action_id == 2) || (auth()->user()->action_id == 3)) && $passwordpolicy[0]->isWaitingApproval == 1)   
                                  <a  href="{{ route('ib.sPolicy.approve', $passwordpolicy[0]->id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-info btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-green"></i></span>
                                  </a>    
                             @endif 
                             <a title="View" href="{{ route('ib.sPolicy.view', $passwordpolicy[0]->id) }}"  class="btn btn-sm btn-primary">
                                <span><i class="fas fa-eye"></i></span>
                            </a>
                           
                        
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h6>OTP Policies</h6>
                <hr />

                <div class="collapse" id="editOTPPolicy">
                    <h6>Edit Policy</h6>
                    <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
                    <form action="{{ url('ib/securitypolicies/otp') }}" method="POST">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">Min Length:<span class="text-danger">*</span></label>
                                    <input type="text" name="min_length" class="form-control form-control-sm" value="{{ $otppolicy[0]->min_length }}">
                                    <input type="hidden" name="id" class="form-control form-control-sm" value="{{ $otppolicy[0]->id }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">Max Length:<span class="text-danger">*</span></label>
                                    <input type="text" name="max_length" class="form-control form-control-sm" value="{{ $otppolicy[0]->min_length }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">Max Attempts:<span class="text-danger">*</span></label>
                                    <input type="text" name="max_attempts" class="form-control form-control-sm" value="{{ $otppolicy[0]->min_length }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr />
                                <button type="submit" class="btn btn-success btn-sm">Save</button>
                                <a href="#editOTPPolicy" data-toggle="collapse" class="btn btn-sm btn-link">Cancel</a>
                            </div>
                            <br/>
                            <br/>
                            <br/>
                            <br/>

                        </div>
                    </form>

                </div>
                <table class="table table-striped table-sm table-hover datatable">
                    <thead>
                        <tr>
                            <th>Min Length</th>
                            <th>Max Length</th>
                            <th>Max Attempts</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $otppolicy[0]->min_length }}</td>
                            <td>{{ $otppolicy[0]->max_length }}</td>
                            <td>{{ $otppolicy[0]->max_attempts }}</td>
                              <td>
                                @if($otppolicy[0]->isWaitingApproval  == 1) 
                                <span class="badge badge-info"> {{ __('Pending')}} </span>
                                 @elseif($otppolicy[0]->isWaitingApproval  == 2) 
                                  <span class="badge badge-danger"> {{ __('rejected') }} </span> 
                                  @elseif($otppolicy[0]->isWaitingApproval  == 0)
                                   <span class="badge badge-success"> {{ __('approved')}} </span> 
                                @endif
                            </td>
                            <td>
                 
                        @if(auth()->user()->action_id == 1  or auth()->user()->action_id == 3 or (auth()->user()->id == $otppolicy[0]->initiator_id))          
                            <a title="Edit" href="#editOTPPolicy" data-toggle="collapse" class="btn btn-sm btn-primary">
                                    <span><i class="fas fa-edit"></i></span>
                            </a>
                            @endif
                                @if(((auth()->user()->action_id == 2) || (auth()->user()->action_id == 3)) && $otppolicy[0]->isWaitingApproval == 1) 
                                  <a  href="{{ route('ib.oPolicy.approve', $otppolicy[0]->id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-info btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-green"></i></span>
                                  </a>    
                             @endif 
                             <a title="View" href="{{ route('ib.oPolicy.view', $otppolicy[0]->id) }}"  class="btn btn-sm btn-primary">
                                <span><i class="fas fa-eye"></i></span>
                            </a>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>


@section('scripts')
@parent
<script>
    $(document).ready(function() {


       $(function() {
           $('.datatables').DataTable()
       });




    });
    </script>
@endsection
@endsection