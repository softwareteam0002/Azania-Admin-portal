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
        <li class="breadcrumb-item active">Service charges</li>
    </ol>
</nav>

<h5>Service charges</h5>
<hr />
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-10">
                <label>Select a service batch:</label>
                <div class="input-group">
                    <select class="custom-select" id="serviceBatchID" aria-label="Example select with button addon">
                        <option selected>Select a service batch:</option>
                        @foreach($batches as $batch)
                        @if($batch->batch_id == $activebatch->batch_id)
                        <option value="{{ $batch->batch_id }}" selected>Batch #: {{ $batch->batch_id }} From: {{ $batch->from_date }} - [{{ $batch->status->status }}]</option>
                        @else
                        <option value="{{ $batch->batch_id }}">Batch #: {{ $batch->batch_id }} From: {{ $batch->from_date }} - [{{ $batch->status->status }}]</option>
                        @endif

                        @endforeach
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-primary" onclick="attrClick()">Get Service Charges</button>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class=''>
                    <label>Add new batch:</label>
                    <form action="{{ url('agency/servicecharges/batch') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="batch_id" value="{{ $activebatch->batch_id }}">
                        <input type="hidden" name="op" value="1">
                        <button type="submit" class="btn btn-primary btn-block" title="Add / Create">
                            <i class="fas fa-plus"></i> Create new Batch
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function attrClick() {
            var v = $("#serviceBatchID").val();
            window.location.replace("{{ url('agency/servicecharges/') }}/" + v);;
        }
    </script>
</div>


<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <p>Valid from:</p>
                <h4 class="mt-n3">{{ $activebatch->from_date ?? "N/A"}}</h4>

                <p class="mt-3">Initiator:</p>
                <h4 class="mt-n3">{{ $activebatch->initiator->name ?? "N/A"}}</h4>


            </div>
            <div class="col-md-4 text-center">
                <div class="card">
                    <div class="card-body">
                        <h2>Selected Batch #: {{ $activebatch->batch_id }}</h2>
                        @if($activebatch->batch_status == 1)
                        <span class="badge badge-success">{{ $activebatch->status->status }}</span>
                        @else
                        <span class="badge badge-danger">{{ $activebatch->status->status }}</span>
                        @endif

                        <br />
                        <br />

                        <form action="{{ url('agency/servicecharges/batch') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="batch_id" value="{{ $activebatch->batch_id }}">
                            <input type="hidden" name="op" value="2">
                            <button type="submit" class="btn btn-warning btn-sm" title="Duplicate">
                                <i class="fas fa-clone"></i>
                            </button>
                        </form>

                        <form action="{{ url('agency/servicecharges/batch') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="batch_id" value="{{ $activebatch->batch_id }}">
                            <input type="hidden" name="op" value="3">
                            <button type="submit" class="btn btn-info btn-sm" title="Download">
                                <i class="fas fa-arrow-down"></i>
                            </button>
			    <button type="submit" class="btn btn-success btn-sm" title="Upload">
                                <i class="fas fa-arrow-up"></i>
                            </button>
                        </form>

                        @if($activebatch->batch_status == 2)
                        <form action="{{ url('agency/servicecharges/batch') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="batch_id" value="{{ $activebatch->batch_id }}">
                            <input type="hidden" name="op" value="4">
                            <button type="submit" class="btn btn-success btn-sm" title="Activate">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                        @endif

                        @if($activebatch->batch_status == 1)
                        <form action="{{ url('agency/servicecharges/batch') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="batch_id" value="{{ $activebatch->batch_id }}">
                            <input type="hidden" name="op" value="5">
                            <button type="submit" class="btn btn-danger btn-sm" title="Deactivate">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                        @endif

                    </div>
                </div>




            </div>
            <div class="col-md-4 text-right">
                <p>Valid to:</p>
                <h4 class="mt-n3">{{ $activebatch->to_date ?? "N/A"}}</h4>

                <p class="mt-3">Approver:</p>
                <h4 class="mt-n3">{{ $activebatch->approver->name ?? "N/A"}}</h4>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <h4>Charges</h4>
                <br />
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#fixedcharges" role="tab" aria-controls="pills-home" aria-selected="true">Fixed</a>
                    </li>


                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#percentagecharges" role="tab" aria-controls="pills-profile" aria-selected="false">Percentage</a>
                    </li>


                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#intervalcharges" role="tab" aria-controls="pills-profile" aria-selected="false">Interval</a>
                    </li>


                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#percentageintervalcharges" role="tab" aria-controls="pills-profile" aria-selected="false">Interval Percentage</a>
                    </li>
                </ul>
                <hr />

                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="fixedcharges" role="tabpanel" aria-labelledby="pills-home-tab">
                        <h5 class="mb-2">Fixed charges:</h5>
                        <hr />

                        @if( $activebatch->batch_status == 2)
                        <div class="card">
                            <div class="card-body">
                                <h6>Create service charge from Batch ID {{ $activebatch->batch_id }} Valid from {{ $activebatch->from_date }} to {{ $activebatch->to_date ?? "Today"  }}</h6>
                                <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
                                <form action="{{ url('agency/charges') }}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <div class="row">

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="types">Service Name:<span class="text-danger">*</span></label>
                                                <input type="hidden" name="charge_type_id" value="1">
                                                <select name="agent_service_id" class="form-control form-control-sm" id="service_name">

                                                    @foreach($agentservices as $service)
                                                    <option value="{{ $service->agent_serviceID ?? '' }}">{{ $service->agent_serviceName ?? '' }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="types">Payer:<span class="text-danger">*</span></label>
                                                <select name="payee" class="form-control form-control-sm" id="payee">
                                                    <option value="0">Customer</option>
                                                    <option value="1">Bank</option>
                                                    <option value="2">Third Party</option>
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="name">Charge Amount:</label>
                                                <input type="text" name="charge_amount" id="charge_amount" placeholder="Enter the charge amount" class="form-control form-control-sm">
                                                <input type="hidden" name="batch_id" value="{{ $activebatch->batch_id }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <hr />
                                        <button type="submit" class="btn btn-success btn-sm">{{ trans('global.save') }}</button>
                                        <button type="reset" class="btn btn-link btn-sm">Clear form</button>
                                    </div>

                                </form>
                            </div>
                        </div>
                        @endif

                        <table class="table table-striped table-sm table-hover datatable">
                            <thead>
                                <tr>
                                    <th>Service Name</th>
                                    <th>Charge Type</th>
                                    <th>Payer</th>
                                    <th class="text-right">Charge Amount</th>
                                    <th class="text-right"></th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fixedcharges as $charge)
                                <tr>
                                    <td>{{ $charge->services->agent_serviceName}}</td>
                                    <td>{{ $charge->chargetypes->charge_type}}</td>
                                    <td>{{ ucfirst($charge->payee) }}</td>
                                    <td class="text-right">{{ number_format($charge->amount , 2) ?? $charge->amount_percentage }}</td>
                                    <td class="text-right"> </td>
                                    <td>
                                        @if( $activebatch->batch_status == 2)
                                        <form action="{{ url('agency/servicecharges/delete') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="charge_id" value="{{ $charge->charge_id }}">
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                    <div class="tab-pane fade" id="percentagecharges" role="tabpanel" aria-labelledby="pills-profile-tab">
                        <h5 class="mb-2">Percentage charges</h5>
                        <hr />

                        @if( $activebatch->batch_status == 2)
                        <div class="card">
                            <div class="card-body">
                                <h6>Create service charge from Batch ID {{ $activebatch->batch_id }} Valid from {{ $activebatch->from_date }} to {{ $activebatch->to_date ?? "Today"  }}</h6>
                                <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
                                <form action="{{ url('agency/charges') }}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <div class="row">

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="types">Service Name:<span class="text-danger">*</span></label>
                                                <input type="hidden" name="charge_type_id" value="2">
                                                <select name="agent_service_id" class="form-control form-control-sm" id="service_name">
                                                    @foreach($agentservices as $service)
                                                    <option value="{{ $service->agent_serviceID ?? '' }}">{{ $service->agent_serviceName ?? '' }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="types">Payer:<span class="text-danger">*</span></label>
                                                <select name="payee" class="form-control form-control-sm" id="payee">
                                                    <option value="0">Customer</option>
                                                    <option value="1">Bank</option>
                                                    <option value="2">Third Party</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="name">Percentage:</label>
                                                <input type="text" name="charge_percent" id="charge_percent" placeholder="Enter the charge percentage" class="form-control form-control-sm">
                                                <input type="hidden" name="batch_id" value="{{ $activebatch->batch_id }}">
                                            </div>
                                        </div>

                                    </div>


                                    <div class="col-md-12">
                                        <hr />
                                        <button type="submit" class="btn btn-success btn-sm">{{ trans('global.save') }}</button>
                                        <button type="reset" class="btn btn-link btn-sm">Clear form</button>
                                    </div>

                                </form>
                            </div>
                        </div>
                        @endif

                        <table class="table table-striped table-sm table-hover datatable">
                            <thead>
                                <tr>
                                    <th>Service Name</th>
                                    <th>Charge Type</th>
                                    <th>Payer</th>
                                    <th class="text-right">Charge Percent</th>
                                    <th class="text-right"></th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($percentagecharges as $charge)
                                <tr>
                                    <td>{{ $charge->services->agent_serviceName}}</td>
                                    <td>{{ $charge->chargetypes->charge_type}}</td>
                                    <td>{{ ucfirst($charge->payee) }}</td>
                                    <td class="text-right">{{ $charge->amount_percentage }}</td>
                                    <td class="text-right"> </td>
                                    <td>
                                        @if( $activebatch->batch_status == 2)
                                        <form action="{{ url('agency/servicecharges/delete') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="charge_id" value="{{ $charge->charge_id }}">
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>

                    <div class="tab-pane fade" id="intervalcharges" role="tabpanel" aria-labelledby="pills-contact-tab">
                        <h5 class="mb-2">Interval charges</h5>
                        <hr />

                        @if( $activebatch->batch_status == 2)
                        <div class="card">
                            <div class="card-body">
                                <h6>Create service charge from Batch ID {{ $activebatch->batch_id }} Valid from {{ $activebatch->from_date }} to {{ $activebatch->to_date  ?? "Today" }}</h6>
                                <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
                                <form action="{{ url('agency/charges') }}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <div class="row">

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="types">Service Name:<span class="text-danger">*</span></label>
                                                <select name="agent_service_id" class="form-control form-control-sm" id="service_name">
                                                    @foreach($agentservices as $service)
                                                    <option value="{{ $service->agent_serviceID ?? '' }}">{{ $service->agent_serviceName ?? '' }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="types">Payer:<span class="text-danger">*</span></label>
                                                <select name="payee" class="form-control form-control-sm" id="payee">
                                                    <option value="0">Customer</option>
                                                    <option value="1">Bank</option>
                                                    <option value="2">Third Party</option>
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="name">Start Amount:</label>
                                                <input type="text" name="from_amount" id="from_amount" placeholder="Enter begining range amount" class="form-control form-control-sm">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="name">End Amount:</label>
                                                <input type="text" name="to_amount" id="to_amount" placeholder="Enter ending range amount" class="form-control form-control-sm">
                                            </div>
                                        </div>


                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="name">Charge Amount:</label>
                                                <input type="text" name="charge_amount" id="charge_amount" placeholder="Enter charge amount" class="form-control form-control-sm">
                                                <input type="hidden" name="batch_id" value="{{ $activebatch->batch_id }}">
                                                <input type="hidden" name="charge_type_id" value="3">
                                            </div>
                                        </div>



                                    </div>


                                    <div class="col-md-12">
                                        <hr />
                                        <button type="submit" class="btn btn-success btn-sm">{{ trans('global.save') }}</button>
                                        <button type="reset" class="btn btn-link btn-sm">Clear form</button>
                                    </div>

                                </form>
                            </div>
                        </div>
                        @endif

                        <table class="table table-striped table-sm table-hover datatable">
                            <thead>
                                <tr>
                                    <th>Service Name</th>
                                    <th>Charge Type</th>
                                    <th>Payer</th>
                                    <th class="text-right">From Amount</th>
                                    <th class="text-right">To Amount</th>
                                    <th class="text-right">Charge Amount</th>
                                    <th class="text-right"></th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($intervalcharges as $charge)
                                <tr>
                                    <td>{{ $charge->services->agent_serviceName}}</td>
                                    <td>{{ $charge->chargetypes->charge_type}}</td>
                                    <td>{{ ucfirst($charge->payee) }}</td>
                                    <td class="text-right">{{ number_format($charge->from_amount, 2) ?? "-"}}</td>
                                    <td class="text-right">{{ number_format($charge->to_amount, 2) ?? "-"}}</td>
                                    <td class="text-right">{{ number_format($charge->amount , 2) }}</td>
                                    <td class="text-right"> </td>
                                    <td>
                                        @if( $activebatch->batch_status == 2)
                                        <form action="{{ url('agency/servicecharges/delete') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="charge_id" value="{{ $charge->charge_id }}">
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>


                    <div class="tab-pane fade" id="percentageintervalcharges" role="tabpanel" aria-labelledby="pills-contact-tab">
                        <h5 class="mb-2">Interval Percentage charges</h5>
                        <hr />

                        @if( $activebatch->batch_status == 2)
                        <div class="card">
                            <div class="card-body">
                                <h6>Create service charge from Batch ID {{ $activebatch->batch_id }} Valid from {{ $activebatch->from_date }} to {{ $activebatch->to_date ?? "Today"  }}</h6>
                                <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
                                <form action="{{ url('agency/charges') }}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <div class="row">

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="types">Service Name:<span class="text-danger">*</span></label>
                                                <select name="agent_service_id" class="form-control form-control-sm" id="service_name">
                                                    @foreach($agentservices as $service)
                                                    <option value="{{ $service->agent_serviceID ?? '' }}">{{ $service->agent_serviceName ?? '' }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="types">Payer:<span class="text-danger">*</span></label>
                                                <select name="payee" class="form-control form-control-sm" id="payee">
                                                    <option value="0">Customer</option>
                                                    <option value="1">Bank</option>
                                                    <option value="2">Third Party</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="name">Start Amount:</label>
                                                <input type="text" name="from_amount" id="from_amount" placeholder="Enter begining range amount" class="form-control form-control-sm">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="name">End Amount:</label>
                                                <input type="text" name="to_amount" id="to_amount" placeholder="Enter ending range amount" class="form-control form-control-sm">
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="name">Percentage:</label>
                                                <input type="text" name="charge_percent" id="charge_percent" placeholder="Enter the charge percentage" class="form-control form-control-sm">
                                                <input type="hidden" name="batch_id" value="{{ $activebatch->batch_id }}">
                                                <input type="hidden" name="charge_type_id" value="4">
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-md-12">
                                        <hr />
                                        <button type="submit" class="btn btn-success btn-sm">{{ trans('global.save') }}</button>
                                        <button type="reset" class="btn btn-link btn-sm">Clear form</button>
                                    </div>

                                </form>
                            </div>
                        </div>
                        @endif

                        <table class="table table-striped table-sm table-hover datatable">
                            <thead>
                                <tr>
                                    <th>Service Name</th>
                                    <th>Charge Type</th>
                                    <th>Payer</th>
                                    <th class="text-right">From Amount</th>
                                    <th class="text-right">To Amount</th>
                                    <th class="text-right">Charge Amount/Percent</th>
                                    <th class="text-right"></th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($intervalpercentagecharges as $charge)
                                <tr>
                                    <td>{{ $charge->services->agent_serviceName}}</td>
                                    <td>{{ $charge->chargetypes->charge_type}}</td>
                                    <td>{{ ucfirst($charge->payee) }}</td>
                                    <td class="text-right">{{ number_format($charge->from_amount, 2) ?? "-"}}</td>
                                    <td class="text-right">{{ number_format($charge->to_amount, 2) ?? "-"}}</td>
                                    <td class="text-right">{{ number_format($charge->amount , 2) ?? $charge->amount_percentage }}</td>
                                    <td class="text-right"> </td>
                                    <td>
                                        @if( $activebatch->batch_status == 2)
                                        <form action="{{ url('agency/servicecharges/delete') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="charge_id" value="{{ $charge->charge_id }}">
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif

                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>




                </div>


            </div>
        </div>

    </div>
</div>





@section('scripts')
@parent
<script>
    //dynamicaly hide and display the form elements based on the value selected
    $("#charge_type").change(function() {
        console.log("Charge Type Changed!");
        //disable all inputs
        $("#charge_amount").attr("disabled", true);
        $("#charge_percent").attr("disabled", true);
        $("#from_amount").attr("disabled", true);
        $("#to_amount").attr("disabled", true);
        //get the selected value
        var s = parseInt($(this).val());
        switch (s) {
            //fixed charges, show charge_amount
            case 1:
                $("#charge_amount").removeAttr("disabled");
                break;
                //percentage charges, show charge_percent
            case 2:
                //$("#charge_amount").removeAttr("disabled");
                $("#charge_percent").removeAttr("disabled");
                break;
                //interval charges, show from_amount , to_amount
            case 3:
                $("#charge_amount").removeAttr("disabled");
                $("#from_amount").removeAttr("disabled");
                $("#to_amount").removeAttr("disabled");
                break;
                //interval percentage charges, show interval and range
            case 5:

                $("#charge_percent").removeAttr("disabled");
                $("#from_amount").removeAttr("disabled");
                $("#to_amount").removeAttr("disabled");
                break;
        }
    });




    function openCharge(evt, cityName) {
        // Declare all variables
        var i, tabcontent, tablinks;

        // Get all elements with class="tabcontent" and hide them
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        // Get all elements with class="tablinks" and remove the class "active"
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }

        // Show the current tab, and add an "active" class to the button that opened the tab
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    // Get the element with id="defaultOpen" and click on it
    document.getElementById("defaultOpen").click();
</script>

@endsection
@endsection