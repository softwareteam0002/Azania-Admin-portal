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
            <li class="breadcrumb-item active">Operators</li>
        </ol>
    </nav>

    <h5>Agent {{ $agent_name }} Operators</h5>
    <hr/>
    <!--

    @can('user_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success btn-sm" href="{{ url('agency/operator/create', [$id]) }}">
                    Add Operator
                </a>
            </div>
        </div>
    @endcan

    -->


    <div class="card">
        <div class="card-body">
            <h6>Create operator form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>

        <form action="{{ url('agency/operator/store', [$id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                        <label for="name">Full Name:<span class="text-danger">*</span></label>
                        <input type="text" id="name" name="full_name" class="form-control form-control-sm" placeholder="Enter operator full name." required>
                        @if($errors->has('name'))
                            <em class="invalid-feedback">
                                {{ $errors->first('name') }}
                            </em>
                        @endif
                        <p class="helper-block">
                            {{ trans('global.user.fields.name_helper') }}
                        </p>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Mobile Number:<span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control form-control-sm" placeholder="Enter operator mobile number" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Location</label>
                        <input type="text" name="location" class="form-control form-control-sm" placeholder="Enter operator location" required>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="types">Device:<span class="text-danger">*</span></label>
                        <select name="device" class="form-control form-control-sm">
                            <option value="0" >Select a device</option>
                            @foreach($devices as $device)
                                <option value="{{ $device->device->device_id }}">{{ $device->device->terminal_ID }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="types">Service Menu:<span class="text-danger">*</span></label>
                        <br />
                        @if(isset($agentservices))
                        @foreach($agentservices as $agentservice)
                            @if($agentservice->short_name == "BI")
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="agent_service[]" value="{{ $agentservice->short_name }}" checked>
                                <label class="form-check-label">
                                    {{ $agentservice->agent_serviceName }}
                                </label>
                            </div>
                            @elseif($agentservice->short_name == "DC")
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="agent_service[]" value="{{ $agentservice->short_name }}" checked>
                                <label class="form-check-label">
                                    {{ $agentservice->agent_serviceName }}
                                </label>
                            </div>
                            @elseif($agentservice->short_name == "WC")
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="agent_service[]" value="{{ $agentservice->short_name }}" checked>
                                <label class="form-check-label">
                                    {{ $agentservice->agent_serviceName }}
                                </label>
                            </div>
                            @elseif($agentservice->short_name == "FT")
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="agent_service[]" value="{{ $agentservice->short_name }}" checked>
                                <label class="form-check-label">
                                    {{ $agentservice->agent_serviceName }}
                                </label>
                            </div>
                            @elseif($agentservice->short_name == "AS")
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="agent_service[]" value="{{ $agentservice->short_name }}" checked>
                                <label class="form-check-label">
                                    {{ $agentservice->agent_serviceName }}
                                </label>
                            </div>
                            @elseif($agentservice->short_name == "MS")
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="agent_service[]" value="{{ $agentservice->short_name }}" checked>
                                <label class="form-check-label">
                                    {{ $agentservice->agent_serviceName }}
                                </label>
                            </div>
                            @elseif($agentservice->short_name == "UP")
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="agent_service[]" value="{{ $agentservice->short_name }}" checked>
                                <label class="form-check-label">
                                    {{ $agentservice->agent_serviceName }}
                                </label>
                            </div>
                            @elseif($agentservice->short_name == "BW")
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="agent_service[]" value="{{ $agentservice->short_name }}" checked>
                                <label class="form-check-label">
                                    {{ $agentservice->agent_serviceName }}
                                </label>
                            </div>
                            @elseif($agentservice->short_name == "SC")
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="agent_service[]" value="{{ $agentservice->short_name }}" checked>
                                <label class="form-check-label">
                                    {{ $agentservice->agent_serviceName }}
                                </label>
                            </div>
                            @elseif($agentservice->short_name == "IP")
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="agent_service[]" value="{{ $agentservice->short_name }}" checked>
                                <label class="form-check-label">
                                    {{ $agentservice->agent_serviceName }}
                                </label>
                            </div>
                            @else
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="agent_service[]" value="{{ $agentservice->short_name }}">
                                <label class="form-check-label">
                                    {{ $agentservice->agent_serviceName }}
                                </label>
                            </div>
                            @endif
                        @endforeach
                        @endif


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


    <h5> All Operators for {{ $agent_name }}</h5>
    <hr/>
    
    <hr/>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>Operator Name</th>
                        <th>Operator Number</th>
                        <th>Operator Mobile Number</th>
                        <th>Location</th>
                        <th>Terminal</th>
                        <th>Initiator</th>
                        <th>Approver</th>
                        <th>Status</th>
                        <th>Actions</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($operators as $operator)
                        <tr>
                            <td>
                            </td>
                            <td>{{$operator->operator_fullname}}</td>
                            <td>{{$operator->operator_id ?? 'N/A'}}</td>
                            <td>{{$operator->operator_msisdn ?? 'N/A'}}</td>
                            <td>{{$operator->location ?? 'N/A'}}</td>
                            <td>{{$operator->device->terminal_ID ?? '' }}</td>
                            <td>{{$operator->initiators->name ?? '--' }}</td>
                            <td>{{$operator->approvers->name ?? '--'}}</td>
                            <td>
                                @if($operator->isWaitingApproval == 0)
                                    <span class="badge badge-success">Approved</span>
                                @elseif($operator->isWaitingApproval == 2)
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-danger">Pending Approval</span>
                                @endif
                                @if($operator->operator_status == 1)
                                    <span class="badge badge-success">Active</span>
                                    @elseif($operator->operator_status == 2)
                                    <span class="badge badge-danger">In active</span>
                                    @elseif($operator->operator_status == 4)
                                    <span class="badge badge-danger">Suspended</span>
                                @endif
                            </td>

                            <td>
                                  @if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)
                                <a href="{{ url('agency/operator/edit/'.$operator->operator_id) }}" class="btn btn-sm btn-primary" title="Edit operator">
                                <i class="fas fa-edit"></i>
                                </a>
                                @endif
                                <form action="{{ url('agency/operator/resetpin') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="operator_id" value="{{ $operator->operator_id }}">
                                    <button type="submit" class="btn btn-warning btn-sm" title="Reset PIN">
                                    <i class="fas fa-user-lock"></i>
                                    </button>
                                </form> 
                                @if($operator->operator_status == 1)
                                    <form action="{{ url('agency/operator/status') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="operator_id" value="{{ $operator->operator_id }}">
                                        <input type="hidden" name="status" value="2">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Deactivate operator">
                                        <i class="fas fa-times"></i>
                                        </button>
                                    </form> 
                                @elseif($operator->operator_status == 2)
                                    <form action="{{ url('agency/operator/status') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="operator_id" value="{{ $operator->operator_id }}">
                                        <input type="hidden" name="status" value="1">
                                        <button type="submit" class="btn btn-success btn-sm" title="Actvate operator">
                                        <i class="fas fa-check"></i>
                                        </button>
                                    </form> 
                                @endif
                                
                                  @if(Auth::user()->id != $operator->initiator_id && $operator->isWaitingApproval==1 && $operator->isDeleted == 0)
                                     <a  href="{{ route('agency.operator.approve', $operator->operator_id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-success btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-green"></i></span>
                                </a>
                                     @endif
                                @if( $operator->isDeleted == 0)
                             <form action="{{ route('agency.operator.deleteOperator', $operator->operator_id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" class="d-inline">
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="operator_id" value="{{ $operator->operator_id }}">
                                <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            @endif
                                
                               @if((auth()->user()->id != $operator->deletedBy_id) && $operator->isDeleted == 1 && $operator->isWaitingApproval == 1)
                                  <a title="Delete"  href="{{ route('agency.operator.deleteApproval', $operator->operator_id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-danger btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-red"></i></span>
                                  </a>    
                                    @endif  
                                
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
