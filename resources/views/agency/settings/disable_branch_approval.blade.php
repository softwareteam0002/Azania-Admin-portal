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
            <li class="breadcrumb-item active">Branchs</li>
        </ol>
    </nav>

    <h5>Branches</h5>
    <hr/>

    <div class="card mb-5">
        <div class="card-body">
            <h6>@if($branch->status == 'Disabled') Enable @endif
                @if($branch->status == 'Active') Disable @endif Branch Approval form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ route('agency.branch.disableActApproval', $branch->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">

                    <input type="hidden" name="id" value="{{ $branch->id }}">

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Branch name:</label>
                            <input type="text" name="branchName" class="form-control form-control-sm" value="{{ $branch->branchName ?? '' }}" placeholder="Enter bank name here." readonly>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">BIC/Swift Code:</label>
                            <input type="text" name="branch_code" class="form-control form-control-sm" value="{{ $branch->branch_code ?? '' }}" placeholder="Enter bank code here." readonly>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Description:</label>
                            <textarea name="description" class="form-control form-control-sm" id="" cols="30"  rows="4" placeholder="Enter descriton here." readonly>{{ $branch->description ?? '' }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Address:</label>
                            <textarea name="address" class="form-control form-control-sm" id="" cols="30" rows="4" placeholder="Enter address here." readonly>{{ $branch->address ?? '' }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <hr/>
                        <input type="submit" name="approve" value="approve" class="btn btn-success btn-sm" value="Approve" />
                        <input type="submit" name="reject" value="reject" class="btn btn-primary btn-sm" value="Reject" />
                        <a href="{{ url('agency/view_branch') }}" class="btn btn-link btn-sm"><i class="fas fa-arrow-left"></i> Go back</a>
                    </div>

                </div>



            </form>
        </div>
    </div>


    @endsection
