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
            <li class="breadcrumb-item"><a href="{{ url('/agency/users') }}">Agents</a></li>
            <li class="breadcrumb-item active">Agent Devices</li>
        </ol>
    </nav>

    <h5>{{ $agent->agent_full_name }} Devices</h5>
    <hr/>
    <div class="card">
        <div class="card-body">
            <h6>Assign a device to {{ $agent->agent_full_name }}</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>

            <form action="{{ url('agency/device/store', [$id]) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="types">Choose device:<span class="text-danger">*</span></label>
                            <select name="device" class="form-control form-control-sm">
                                @foreach($unassigneddevices as $device)
                                    <option value="{{ $device->device_id }}" >{{ $device->terminal_ID }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="types">Trading Account:<span class="text-danger">*</span></label>
                            <select name="trading_ac" class="form-control form-control-sm">
                                    <option value="0">Select trading account</option>
                                @foreach($tradingacs as $tradingac)
                                    <option value="{{ $tradingac->account_id }}" >{{ $tradingac->bank_account }} -  {{ $tradingac->accountStatus->status  }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="types">Commision Account:<span class="text-danger">*</span></label>
                            <select name="commision_ac" class="form-control form-control-sm">
                                    <option value="0">Select commision account</option>
                                @foreach($commisionacs as $commisionac)
                                    <option value="{{ $commisionac->account_id }}" >{{ $commisionac->bank_account  }} -  {{ $commisionac->accountStatus->status  }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <hr/>
                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                        <button type="reset" class="btn btn-sm btn-link">Reset</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!--

        {{--    @can('user_create')--}}
                <div style="margin-bottom: 10px;" class="row">
                    <div class="col-lg-12">
                        <a class="btn btn-success btn-sm" href="{{ url('agency/device/create', [$id]) }}">
                            Add Device
                        </a>
                    </div>
                </div>
        {{--    @endcan--}}



-->

    <h5>All devices assigned to {{ $agent->agent_full_name }}</h5>
    <hr/>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>Terminal ID</th>
                        <th>IMEI 1</th>
                        <th>IMEI 2</th>
                        <th>Registered by</th>
                        <th>Trading Account</th>
                        <th>Commision Account</th>
                        <th>Initiator</th>
                        <th>Approver</th>
                        <th>Status</th>
                        <th>Actions</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($devices as $device)
                        <tr data-entry-id="1" data-toggle="modal" data-target="#largeModal{{$device->id}}" id="loadingModel" data="{{$device->id}}">
                            <td>
                            </td>
                            <td>{{$device->device->terminal_ID ?? 'N/A'}}</td>
                            <td>{{$device->device->device_imei1}}</td>
                            <td>{{$device->device->device_imei2 ?? 'Nil'}}</td>
                            <td>{{$device->device->users->name ?? 'N/A'}}</td>
                            <td>{{$device->device->tradingac->bank_account ?? 'Not assigned'}}</td>
                            <td>{{$device->device->commisionac->bank_account  ?? 'Not Assigned'}}</td>
                            <td>{{$device->initiators->name ?? '--' }}</td>
                            <td>{{$device->approvers->name ?? '--'}}</td>
                            <td>
                                 @if($device->isWaitingApproval == 0)
                                    <span class="badge badge-success">Approved</span>
                                @elseif($device->isWaitingApproval == 2)
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-danger">Pending Approval</span>
                                @endif
                                @if($device->status == 1)
                                    <span class="badge badge-success">Active</span>
                                @elseif($device->status == 2)
                                    <span class="badge badge-warning">InActive</span>
                                @elseif($device->status == 3)
                                    <span class="badge badge-danger">Blocked</span>
                                @elseif($device->status == 4)
                                    <span class="badge badge-danger">Suspended</span>
                                @endif
                            </td>
                            <td>
                                @if($device->status == 1)
                                <!-- 
                                <form class="d-inline" action="{{ url('agency/device/status' )}}" method="POST">
                                    @csrf
                                    <input type="hidden" name="agent_device_id" value="{{ $device->id }}">
                                    <input type="hidden" name="status" value="2">
                                    <button href="" title="De Acivate device" class="btn btn-sm btn-danger">
                                        <i class="fas fa-times"></i>
                                    </button>      
                                </form>

                                <form class="d-inline" action="{{ url('agency/device/status' )}}"  method="POST">
                                    @csrf
                                    <input type="hidden" name="agent_device_id" value="{{ $device->id }}">
                                    <input type="hidden" name="status" value="3">
                                    <button href="" title="Block device" class="btn btn-sm btn-danger">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </form>
                                -->   

                                <form class="d-inline" action="{{ url('agency/device/status' )}}"  method="POST">
                                    @csrf
                                    <input type="hidden" name="agent_device_id" value="{{ $device->id }}">
                                    <input type="hidden" name="status" value="4">
                                    <button href="" title="Suspend  device" class="btn btn-sm btn-danger">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </button>
                                </form>
                                @elseif($device->status == 2)
                                <form class="d-inline" action="{{ url('agency/device/status' )}}"  method="POST">
                                    @csrf
                                    <input type="hidden" name="agent_device_id" value="{{ $device->id }}">
                                    <input type="hidden" name="status" value="1">
                                    <button href="" title="Activate device" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <!--  
                                <form class="d-inline" action="{{ url('agency/device/status' )}}"  method="POST">
                                    @csrf
                                    <input type="hidden" name="agent_device_id" value="{{ $device->id }}">
                                    <input type="hidden" name="status" value="3">
                                    <button href="" title="Block device" class="btn btn-sm btn-danger">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </form>-->
                                

                                <form class="d-inline" action="{{ url('agency/device/status' )}}"  method="POST">
                                    @csrf
                                    <input type="hidden" name="agent_device_id" value="{{ $device->id }}">
                                    <input type="hidden" name="status" value="4">
                                    <button href="" title="Suspend  device" class="btn btn-sm btn-danger">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </button>
                                </form>
                                @elseif($device->status == 3)
                                <form class="d-inline" action="{{ url('agency/device/status' )}}"  method="POST">
                                    @csrf
                                    <input type="hidden" name="agent_device_id" value="{{ $device->id }}">
                                    <input type="hidden" name="status" value="1">
                                    <button href="" title="Activate device" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>

                                <form class="d-inline" action="{{ url('agency/device/status' )}}"  method="POST">
                                    @csrf
                                    <input type="hidden" name="agent_device_id" value="{{ $device->id }}">
                                    <input type="hidden" name="status" value="4">
                                    <button href="" title="Suspend  device" class="btn btn-sm btn-danger">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </button>
                                </form>
                                @elseif($device->status == 4)
                                <form class="d-inline" action="{{ url('agency/device/status' )}}"  method="POST">
                                    @csrf
                                    <input type="hidden" name="agent_device_id" value="{{ $device->id }}">
                                    <input type="hidden" name="status" value="1">
                                    <button href="" title="Activate device" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <!--
                                <form class="d-inline" action="{{ url('agency/device/status' )}}"  method="POST">
                                    @csrf
                                    <input type="hidden" name="agent_device_id" value="{{ $device->id }}">
                                    <input type="hidden" name="status" value="3">
                                    <button href="" title="Block device" class="btn btn-sm btn-danger">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </form>-->
                                @endif

                                <form class="d-inline" action="{{ url('agency/device/status' )}}"  method="POST">
                                    @csrf
                                    <input type="hidden" name="agent_device_id" value="{{ $device->id }}">
                                    <input type="hidden" name="device_id" value="{{ $device->device_id }}">
                                    <input type="hidden" name="status" value="0">
                                    <button href="" title="Unassign device" class="btn btn-sm btn-primary">
                                        <i class="fas fa-unlink"></i>
                                    </button>
                                </form>

                                
                                <form action="{{ url('agency/requests/actions') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="id" value="{{ $device->id }}">
                                        <input type="hidden" name="approver_id" value="{{ Auth::user()->id }}">
                                        <input type="hidden" name="request_type" value="agency_devices">
                                        @if(Auth::user()->id != $device->initiator_id && $device->isWaitingApproval==1)
                                            <a  href="{{ route('agency.device.approve', $device->id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-success btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-green"></i></span>
                                        @endif
                                    </form>

                            </td>
                        </tr>

                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


@section('scripts')
    @parent
    <script>



        jQuery(document).ready(function(){
            jQuery('#ajaxSubmit').click(function(e){
                var dialogy = " <div class=\"d-flex justify-content-center\">\n" +
                    "                                            <div class=\"spinner-border\" role=\"status\">\n" +
                    "                                              <span class=\"sr-only\">Loading...</span>\n" +
                    "                                            </div>\n" +
                    "                                        </div>";
                document.getElementById('subscriberDetails').innerHTML = dialogy;
                e.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    {{--url: "{{ url('/ib/subscriber/'.Crypt::encrypt(1)) }}",--}}
                    url: "{{ url('/ib/subscriber/'.Crypt::encrypt(1)) }}",
                    method: 'get',
                    data: {},
                    success: function(result){
                        var cleanData = JSON.parse(result.data);
                        console.log("Here is the data received ", cleanData);
                        var tempData = "<tr>\n" +
                            "                                        <td>Name</td>\n" +
                            "                                        <td>"+cleanData[0]['name']+"</td>\n" +
                            "                                    </tr>\n" +
                            "                                    <tr>\n" +
                            "                                        <td>Email</td>\n" +
                            "                                        <td>"+cleanData[0]['email']+"</td>\n" +
                            "                                    </tr>\n" +
                            "                                    <tr>\n" +
                            "                                        <td>Language</td>\n" +
                            "                                        <td>\n" +
                            "                                            en\n" +
                            "                                        </td>\n" +
                            "                                    </tr>\n" +
                            "                                    <tr>\n" +
                            "\n" +
                            "\n" +
                            "                                        <td>Status</td>\n" +
                            "                                        <td><span class=\"badge badge-danger\">Suspende</span></td>\n" +
                            "                                    </tr>";
                        document.getElementById('subscriberDetails').innerHTML = "";
                        var table = "<table class=\"table table-responsive-sm table-bordered table-striped table-sm\">\n" +
                            "                                    <thead>\n" +
                            "                                    <tr class=\"bottom-border-color-green primary\" data-toggle=\"modal\" data-target=\"#largeModalTesting\">\n" +
                            "                                        <th colspan=\"2\">SUBSCRIBER DETAILS</th>\n" +
                            "                                    </tr>\n" +
                            "                                    </thead>\n" +
                            "                                    <tbody \n" +
                            tempData +
                            "                                    </tbody>\n" +
                            "                                </table>";
                        document.getElementById('subscriberDetails').innerHTML = table;

                    }});
            });
        });

        jQuery(document).ready(function(){
            jQuery('#loadingModel').click(function(e){
                var dialogy = " <div class=\"d-flex justify-content-center\">\n" +
                    "                                            <div class=\"spinner-border\" role=\"status\">\n" +
                    "                                              <span class=\"sr-only\">Loading...</span>\n" +
                    "                                            </div>\n" +
                    "                                        </div>";
                document.getElementById('subscriberDetails').innerHTML = dialogy
                console.log(document.getElementById("loadingModel").getAttribute("data"));
                e.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url: "{{ url('/ib/subscriber/'.Crypt::encrypt(1)) }}",
                    method: 'get',
                    data: {},
                    success: function(result){
                        var cleanData = JSON.parse(result.data);
                        var tempData = "<tr>\n" +
                            "                                        <td>Name</td>\n" +
                            "                                        <td>"+cleanData[0]['name']+"</td>\n" +
                            "                                    </tr>\n" +
                            "                                    <tr>\n" +
                            "                                        <td>Email</td>\n" +
                            "                                        <td>"+cleanData[0]['email']+"</td>\n" +
                            "                                    </tr>\n" +
                            "                                    <tr>\n" +
                            "                                        <td>Language</td>\n" +
                            "                                        <td>\n" +
                            "                                            en\n" +
                            "                                        </td>\n" +
                            "                                    </tr>\n" +
                            "                                    <tr>\n" +
                            "\n" +
                            "\n" +
                            "                                        <td>Status</td>\n" +
                            "                                        <td><span class=\"badge badge-danger\">Suspende</span></td>\n" +
                            "                                    </tr>";
                        document.getElementById('subscriberDetails').innerHTML = "";
                        var table = "<table class=\"table table-responsive-sm table-bordered table-striped table-sm\">\n" +
                            "                                    <thead>\n" +
                            "                                    <tr class=\"bottom-border-color-green primary\" data-toggle=\"modal\" data-target=\"#largeModalTesting\">\n" +
                            "                                        <th colspan=\"2\">SUBSCRIBER DETAILS</th>\n" +
                            "                                    </tr>\n" +
                            "                                    </thead>\n" +
                            "                                    <tbody \n" +
                            tempData +
                            "                                    </tbody>\n" +
                            "                                </table>";
                        document.getElementById('subscriberDetails').innerHTML = table;

                    }});
            });
        });





        $(function () {
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.users.massDestroy') }}",
                className: 'btn-danger',
                action: function (e, dt, node, config) {
                    var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                        return $(entry).data('entry-id')
                    });

                    if (ids.length === 0) {
                        alert('{{ trans('global.datatables.zero_selected') }}')

                        return
                    }

                    if (confirm('{{ trans('global.areYouSure') }}')) {
                        $.ajax({
                            headers: {'x-csrf-token': _token},
                            method: 'POST',
                            url: config.url,
                            data: { ids: ids, _method: 'DELETE' }})
                            .done(function () { location.reload() })
                    }
                }
            }
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('user_delete')
            dtButtons.push(deleteButton)
            @endcan

            $('.datatable:not(.ajaxTable)').DataTable({ buttons: dtButtons })
        })

    </script>
@endsection
@endsection
