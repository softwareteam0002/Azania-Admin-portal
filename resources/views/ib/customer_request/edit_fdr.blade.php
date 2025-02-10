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
        <hr/>
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small" style="background-color:#f9f9f9; padding: .55rem 1rem;">
            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/ib/fdr/index') }}">Fixed Deposit Rates(FDR)</a></li>
            <li class="breadcrumb-item active">Update Fixed Deposit Rate</li>
        </ol>
    </nav>


    <h5>Fixed Deposit Rates(FDR)</h5>
    <hr/>

    <div class="card mb-4">
        <div class="card-body">
            <h6>Update a Fixed Deposit Rate</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ url('ib/fdr/update', [$fdr->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <p class="small text-primary">Amount Range.</p>

                <div class="row">
                 <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">From Amount:<span class="text-danger">*</span></label>
                            <input type="text" name="amount_interval" value="{{ $fdr->amount_interval }}" class="form-control form-control-sm number" placeholder="Enter Amount." required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">To Amount:<span class="text-danger">*</span></label>
                            <input type="text" name="amount_limit" value="{{ number_format($fdr->amount_limit, 2) }}" class="form-control form-control-sm number" placeholder="Enter Amount." required>
                        </div>
                    </div>
                    </div>

                <p class="small text-primary">Tenure and Rate.</p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="types">Tenure</label>
                            <select name="tenure" class="form-control form-control-sm">
                                @foreach($tenures as $tenure)
                                    <option value="{{ $tenure }}" @if($fdr->tenure_of_term_deposit==$tenure) selected @endif>{{ $tenure }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Rate:<span class="text-danger">*</span></label>
                            <input type="text" name="rate" value="{{ $fdr->existing_rates }}" class="form-control form-control-sm" placeholder="Enter Rate" required>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12">
                        <hr/>
                        <button type="submit" class="btn btn-success btn-sm">Save</button>
                        <button type="reset" class="btn btn-link btn-sm">Clear form</button>
                    </div>
                </div>
            </form>
        </div>
    </div>



@endsection
