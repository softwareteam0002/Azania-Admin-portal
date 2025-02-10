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
            <li class="breadcrumb-item"><a href="{{ url('ib/letter/credit') }}">Letter of credit requests</a></li>
            <li class="breadcrumb-item active">Letter of credit requests details</li>
        </ol>
    </nav>


    <h5>Letter of credit requests details</h5>
    <hr/>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-4">
                    <button type="button" class="btn btn-sm btn-outline-dark" onclick="printDiv('letterOfRequestDoc')">Print</button>
                </div>
                <hr/>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h6 class="md-1">Request details:</h6>
                    <table class="table table-sm">
                        <tbody>
                            <tr><td width="200px" class="font-weight-bold">Name:</td><td>{{$request->name ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Account number:</td><td>{{$request->accounts->accountID ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Business type:</td><td>{{$request->business_type ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Goods type:</td><td>{{$request->goods_type ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Goods value:</td><td>{{$request->goods_value ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Collateral cover:</td><td> {{$request->collateral_cover ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Status:</td><td> {{$request->status->name ?? 'N/A'}}</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="mb-2">Account details:</h6>
                    <table class="table table-sm">
                        <tbody>
                            <tr><td width="200px" class="font-weight-bold">Account name:</td><td>{{$request->accounts->accountName ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Account number:</td><td>{{$request->accounts->accountID ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Account status:</td><td class="font-weight-bold">{{$request->accounts->aCStatus ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Email:</td><td>{{$request->accounts->emailID ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Mobile:</td><td>{{$request->accounts->mobile ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Address:</td><td>{{$request->accounts->address ?? 'N/A'}} <br/> {{$request->accounts->countryName ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Branch:</td><td>({{ $request->accounts->branchId ?? 'N/A'}}) {{$request->accounts->branchName ?? 'N/A'}}</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12">
                    <hr/>
                    <a class="btn btn-sm btn-link" href="{{ url('ib/letter/credit') }}">Go back</a>
                </div>
            </div>

            <div class="row d-none" id="letterOfRequestDoc">
                <div class="col-md-12">
                    <h1 class="text-center">Letter of credit request</h1>
                </div>

                <div class="col-md-6">
                    <h6 class="mb-2">Request details:</h6>
                    <table class="table table-sm">
                        <tbody>
                            <tr><td width="200px" class="font-weight-bold">Name:</td><td>{{$request->name ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Account number:</td><td>{{$request->accounts->accountID ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Business type:</td><td>{{$request->business_type ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Goods type:</td><td>{{$request->goods_type ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Goods value:</td><td>{{$request->goods_value ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Collateral cover:</td><td> {{$request->collateral_cover ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Status:</td><td> {{$request->status->name ?? 'N/A'}}</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="col-md-6">
                    <h6 class="mb-5">Account details:</h6>
                    <br/>
                    <table class="table table-sm">
                        <tbody>
                            <tr><td width="200px" class="font-weight-bold">Account name:</td><td>{{$request->accounts->accountName ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Account number:</td><td>{{$request->accounts->accountID ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Account status:</td><td class="font-weight-bold">{{$request->accounts->aCStatus ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Email:</td><td>{{$request->accounts->emailID ?? 'N/A'}}</td></tr>
                            <tr><td  class="font-weight-bold">Mobile:</td><td>{{$request->accounts->mobile ?? 'N/A'}}</td></tr>
{{--                            <tr><td  class="font-weight-bold">Address:</td><td>{{$request->accounts->address ?? 'N/A'}} <br/> {{$request->accounts->countryName ?? 'N/A'}}</td></tr>--}}
{{--                            <tr><td  class="font-weight-bold">Branch:</td><td>({{ $request->accounts->branchId ?? 'N/A'}}) {{$request->accounts->branchName ?? 'N/A'}}</td></tr>--}}
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
