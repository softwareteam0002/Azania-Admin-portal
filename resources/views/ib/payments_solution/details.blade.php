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
        <li class="breadcrumb-item"><a href="{{ url('ib/payments_solution') }}">Payment Solutions</a></li>
        <li class="breadcrumb-item active">Payment Solutions Details</li>
    </ol>
</nav>


<h5>Payment Solution Details</h5>
<hr />

<div class="card">
    <div class="card-body">
        <label>Institutions:</label>
        <div class="input-group">
            <select class="custom-select" id="institutionID" aria-label="Example select with button addon">
                <option selected>Select an institution</option>
                @foreach($institutions as $institution)
                @if($institution->id == $id)
                <option id="instituteOption" value="{{$institution->id}}" selected>{{$institution->institute_name}}</option>
                @else
                <option id="instituteOption" value="{{$institution->id}}">{{$institution->institute_name}}</option>
                @endif
                @endforeach
            </select>
            <div class="input-group-append">
                <button class="btn btn-primary" onclick="attrClick()">Submit</button>
            </div>
        </div>
    </div>
</div>


<div class="card">
    <div class="card-body">
        <h5>{{ $institution_name }}</h5>
        <hr />
        <ul class="nav nav-pills mb-3 " id="pills-tab" role="tablist">

            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="pills-payments-tab" data-toggle="pill" href="#pills-payments" role="tab" aria-controls="pills-payments" aria-selected="true">Payments</a>
            </li>

            <li class="nav-item" role="presentation">
                <a class="nav-link" id="pills-members-tab" data-toggle="pill" href="#pills-members" role="tab" aria-controls="pills-members" aria-selected="false">Members</a>
            </li>

            <li class="nav-item" role="presentation">
                <a class="nav-link" id="pills-payers-tab" data-toggle="pill" href="#pills-payers" role="tab" aria-controls="pills-payers" aria-selected="false">Payers</a>
            </li>

            <li class="nav-item" role="presentation">
                <a class="nav-link" id="pills-payment-services-tab" data-toggle="pill" href="#pills-payment-services" role="tab" aria-controls="pills-payment-services" aria-selected="false">Payment Services</a>
            </li>

            <li class="nav-item" role="presentation">
                <a class="nav-link" id="pills-levels-tab" data-toggle="pill" href="#pills-levels" role="tab" aria-controls="pills-levels" aria-selected="false">Levels</a>
            </li>

        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-payments" role="tabpanel" aria-labelledby="pills-payments-tab">
                <h6 class="mb-2">Payments</h6>
                <!--

                {
    "id": 1,
    "tbl_institution_payer_id": "13",
    "institute_id": null,
    "amount": "1200.00",
    "status": "1",
    "service_id": "1",
    "initiator_id": "0",
    "approver_id": "0",
    "isWaitingApproval": "0",
    "created_at": null,
    "updated_at": null,
    "responseCode": null,
    "responseMessage": "Success ",
    "transactionTimestamp": null,
    "transactionId": null,
    "paymentType": "FULL ",
    "serviceName": "ZAKA ",
    "institute_name": null,
    "description": "Consequatur aut necessitatibus nam voluptas omnis voluptatem."
}


                -->

                <table class="table table-striped table-sm table-hover datatable">
                    <thead>
                        <tr>
                            <th width="10"></th>
                            <th>Payer's Name</th>
                            <th>Payer's Mobile</th>
                            <th>Institution</th>
                            <th>Amount</th>
                            <th>Service</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr>
                            <td widtd="10"></td>
                            <td>@if($payment->payers) {{ $payment->payers->full_name }} @else {{ 'N/A' }} @endif</td>
                            <td>@if($payment->payers) {{ $payment->payers->phone }} @else {{ 'N/A' }} @endif</td>
                            <td>{{ $payment->institutions->institute_name }}</td>
                            <td>{{ $payment->amount }}</td>
                            <td>{{ $payment->services->name }}</td>
                            <td>@if($payment->status==0) <span class="badge badge-danger">Inactive</span> @else <span class="badge badge-success">Active</span> @endif</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>




            </div>
            <div class="tab-pane fade" id="pills-members" role="tabpanel" aria-labelledby="pills-members-tab">
                <h6 class="mb-2">Members</h6>
                <!--
                    {
    "id": 1,
    "full_name": "Paul Swebe",
    "phone": "255689836596",
    "service_level_id": "1",
    "institution_id": "47",
    "added_by": "14",
    "control_no": "699811",
    "status": "0",
    "isWaitingApproval": "0",
    "created_at": "2020-06-26 07:54:14.483",
    "updated_at": "2020-06-26 07:54:14.483"
}
                -->
                <table class="table table-striped table-sm table-hover datatable">
                    <thead>
                        <tr>
                            <th width="10"></th>
                            <th>Full Name</th>
                            <th>Mobile Number</th>
                            <th>Service Level</th>
                            <th>Institution</th>
                            <th>Added By</th>
                            <th>Control Number</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $member)
                        <tr>
                            <td widtd="10"></td>
                            <td>{{ $member->full_name }}</td>
                            <td>{{ $member->phone }}</td>
                            <td>{{ $member->services->name }}</td>
                            <td>{{ $member->institutions->institute_name }}</td>
                            <td>{{ $member->users->name }}</td>
                            <td>{{ $member->control_no }}</td>
                            <td>@if($member->status==0) <span class="badge badge-danger">Inactive</span> @else <span class="badge badge-success">Active</span> @endif</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
            <div class="tab-pane fade" id="pills-payers" role="tabpanel" aria-labelledby="pills-payers-tab">
                <h6 class="mb-2">Payers</h6>
                <!--
                     {
    "id": 2,
    "full_name": "Paul Swebe",
    "phone": "255689836596",
    "service_level_id": "1",
    "institution_id": "47",
    "service_id": "1",
    "amount": "100.00",
    "code": "0",
    "control_no": "2006279428",
    "status": "1",
    "isWaitingApproval": "0",
    "created_at": "2020-06-27 08:21:52.263",
    "updated_at": "2020-06-27 10:59:46.540"
}
                -->

                <table class="table table-striped table-sm table-hover datatable">
                    <thead>
                        <tr>
                            <th width="10"></th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Institution</th>
                            <th>Amount</th>
                            <th>Service</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payers as $payer)
                        <tr>
                            <td widtd="10"></td>
                            <td>{{ $payer->full_name }}</td>
                            <td>{{ $payer->phone }}</td>
                            <td>{{ $payer->institutions->institute_name }}</td>
                            <td>{{ $payer->amount }}</td>
                            <td>{{ $payer->services->name ?? '' }}</td>
                            <td>@if($payer->status==0) <span class="badge badge-danger">Inactive</span> @else <span class="badge badge-success">Active</span> @endif</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
            <div class="tab-pane fade" id="pills-payment-services" role="tabpanel" aria-labelledby="pills-payment-services-tab">
                <h6 class="mb-2">Payment Services</h6>
                <!--
                    {
    "id": 2,
    "name": "ADA",
    "service_type_id": "1",
    "minimum_amount": "1000.00",
    "code": "192712",
    "institution_id": "47",
    "status": "1",
    "created_at": "2020-06-27 11:02:11.813",
    "updated_at": "2020-06-27 11:13:12.037"
}
                -->

                <table class="table table-striped table-sm table-hover datatable">
                    <thead>
                        <tr>
                            <th width="10"></th>
                            <th>Name</th>
                            <th>Minimum Amount</th>
                            <th>Code</th>
                            <th>Institution</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paymentservices as $paymentservice)
                        <tr>
                            <td widtd="10"></td>
                            <td>{{ $paymentservice->name }}</td>
                            <td>{{ $paymentservice->minimum_amount ?? 'N/A' }}</td>
                            <td>{{ $paymentservice->code }}</td>
                            <td>{{ $paymentservice->institutions->institute_name  }}</td>
                            <td>@if($paymentservice->status==0) <span class="badge badge-danger">Inactive</span> @else <span class="badge badge-success">Active</span> @endif</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
            <div class="tab-pane fade" id="pills-levels" role="tabpanel" aria-labelledby="pills-levels-tab">
                <h6 class="mb-2">Levels</h6>
                <!--
                    {
    "id": 1,
    "name": "test level",
    "description": "This is just a test.",
    "institution_id": "47",
    "status": "1",
    "created_at": "2020-06-26 07:53:49.523",
    "updated_at": "2020-06-26 07:53:54.747"
}
                -->
                <table class="table table-striped table-sm table-hover datatable">
                    <thead>
                        <tr>
                            <th width="10"></th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Institution</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($levels as $level)
                        <tr>
                            <td widtd="10"></td>
                            <td>{{ $level->name }}</td>
                            <td>{{ $level->description }}</td>
                            <td>{{ $level->institutions->institute_name }}</td>
                            <td>@if($level->status==0) <span class="badge badge-danger">Inactive</span> @else <span class="badge badge-success">Active</span> @endif</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<script>
    function attrClick() {
        var v = $("#institutionID").val();
        window.location.replace("{{ url('ib/payments_solution/institution/') }}/" + v);;
    }
</script>




@section('scripts')
@parent
<script>
    $(document).ready(function() {
        $('.datatable').DataTable();
    });
</script>
@endsection

@endsection