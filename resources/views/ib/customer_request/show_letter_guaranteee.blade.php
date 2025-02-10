@extends('layouts.admin')
@section('content')

    <div class="row mb-3">
        <div class="col-md-8">
            <!-- Notifications-->
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

            @if(session('notification') && session('color'))
                <div class="alert alert-{{ session('color') }} alert-dismissible fade show" role="alert">
                    {{ session('notification') }}
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
            <li class="breadcrumb-item"><a href="{{ url('ib/letter/guarantee') }}">Letter of guarantee</a></li>
            <li class="breadcrumb-item active">Letter of guarantee details</li>
        </ol>
    </nav>


    <h5>Letter of guarantee details</h5>
    <hr/>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-4">
                    <button type="button" class="btn btn-sm btn-outline-dark" onclick="printDiv('letterOfGuarantee')">Print</button>
                </div>
                <hr/>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <h6 class="md-1">Request details:</h6>
                    <table class="table table-sm">
                        <tbody>
                        <tr><td class="font-weight-bold">Name:</td><td>{{$request->name}}</td></tr>
                        <tr><td class="font-weight-bold">Business Type:</td><td>{{$request->business_type}}</td></tr>
                        <tr><td class="font-weight-bold">Location:</td><td>{{$request->location}}</td></tr>
                        <tr><td class="font-weight-bold">Contract Amount:</td><td>{{$request->contract_amount}}</td></tr>
                        <tr><td class="font-weight-bold">Guarantee Amount:</td><td>{{$request->guarantee_amount}}</td></tr>
                        <tr><td class="font-weight-bold">Collateral Cover:</td><td>{{$request->collateral_cover}}</td></tr>
                        <tr><td class="font-weight-bold">Instution Name:</td><td>{{$request->institutions->institute_name ?? 'N/A' }}</td></tr>
                        <tr><td class="font-weight-bold">Account Number:</td><td>{{$request->accounts->accountID ?? 'N/A' }}</td></tr>
                        <tr><td class="font-weight-bold">Client Name:</td><td>{{$request->accounts->clientName ?? 'N/A' }}</td></tr>
                        <tr><td class="font-weight-bold">Address</td><td>{{$request->institutions->address ?? 'N/A' }}</td></tr>
                        <tr><td class="font-weight-bold">Start Date</td><td>{{$request->start_date }}</td></tr>
                        <tr><td class="font-weight-bold">End Date</td><td>{{$request->end_date}}</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12">
                    <hr/>
                    <a class="btn btn-sm btn-link" href="{{ url('ib/letter/guarantee') }}">Go back</a>
                </div>
            </div>

            <div class="row d-none" id="letterOfGuarantee">
                <div class="col-md-12">
                    <h1 class="text-center">Letter of guarantee request</h1>
                </div>

                <div class="col-md-12">
                    <h6 class="mb-2">Request details:</h6>
                    <table class="table table-sm">
                        <tbody>
                        <tr><td class="font-weight-bold">Name:</td><td>{{$request->name}}</td></tr>
                        <tr><td class="font-weight-bold">Business Type:</td><td>{{$request->business_type}}</td></tr>
                        <tr><td class="font-weight-bold">Location:</td><td>{{$request->location}}</td></tr>
                        <tr><td class="font-weight-bold">Contract Amount:</td><td>{{$request->contract_amount}}</td></tr>
                        <tr><td class="font-weight-bold">Guarantee Amount:</td><td>{{$request->guarantee_amount}}</td></tr>
                        <tr><td class="font-weight-bold">Collateral Cover:</td><td>{{$request->collateral_cover}}</td></tr>
                        <tr><td class="font-weight-bold">Instution Name:</td><td>{{$request->institutions->institute_name ?? 'N/A' }}</td></tr>
                        <tr><td class="font-weight-bold">Account Number:</td><td>{{$request->accounts->accountID ?? 'N/A' }}</td></tr>
                        <tr><td class="font-weight-bold">Client Name:</td><td>{{$request->accounts->clientName ?? 'N/A' }}</td></tr>
                        <tr><td class="font-weight-bold">Address</td><td>{{$request->institutions->address ?? 'N/A' }}</td></tr>
                        <tr><td class="font-weight-bold">Start Date</td><td>{{$request->start_date }}</td></tr>
                        <tr><td class="font-weight-bold">End Date</td><td>{{$request->end_date}}</td></tr>
                        </tbody>
                    </table>
                </div>


            </div>
        </div>
    </div>
@section('scripts')
    @parent
    <script>
        //add an event listener on the print btn
        function printDiv(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>
@endsection

@endsection
