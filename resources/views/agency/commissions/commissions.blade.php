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
        <li class="breadcrumb-item active">Commisions</li>
    </ol>
</nav>


<div class="card">
    <div class="card-body">
        <h6>Create commision form</h6>
        <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
        <form action="{{ url('agency/commission/store') }}" method="POST">
            @csrf
            @method('POST')
            <div class="row">
                <div class="col-md-3 form-group">
                    <label for="types">Select service<span class="text-danger">*</span></label>
                    <select name="service" class="form-control">
                        <option value="0">Please select service</option>
                        @foreach($services as $service)
                        <option value="{{ $service->agent_serviceID }}">{{ $service->agent_serviceName }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 form-group">
                    <label for="name">Bank Rate</label>
                    <input type="number" min="0" id="bank_rate" max="100" name="bank_rate" class="form-control" placeholder="Enter rate value from 1 to 100">
                </div>

                <div class="col-md-3 form-group">
                    <label for="name">Agent Rate</label>
                    <input type="number" min="0" id="agent_rate" max="100" name="agent_rate" class="form-control" placeholder="Enter rate value from 1 to 100">
                </div>

                <div class="col-md-3 form-group">
                    <label for="name">Third Party Rate</label>
                    <input type="number" min="0" id="third_party_rate" max="100" name="third_party_rate" class="form-control" placeholder="Enter rate value from 1 to 100">
                </div>

                <div class="col-12">
                    <hr />
                    <button type="submit" class="btn btn-sm btn-success">Save</button>
                    <button type="reset" class="btn btn-sm btn-link">Reset</button>
                </div>
            </div>
        </form>
    </div>
</div>




<h5>All Commissions</h5>
<hr />
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>Service</th>
                        <th>Bank Rate(%)</th>
                        <th>Agent Rate(%)</th>
                        <th>Third Party Rate(%)</th>
                        <th>Initiator</th>
                        <th>Approver</th>
                        <th>Approval Status</th>
                        <th>Actions</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($commissions as $commission)
                    <tr data-entry-id="1" data-toggle="modal" data-target="#largeModal{{$commission->commision_id}}" id="loadingModel" data="{{$commission->commision_id}}">
                        <td>
                        </td>
                        <td>{{$commission->service->agent_serviceName}}</td>
                        <td>{{$commission->bank_rate_value}}</td>
                        <td>{{$commission->agent_rate_value}}</td>
                        <td>{{$commission->third_parties}}</td>
                        <td>{{ $commission->initiator->name ?? '--' }}</td>
                        <td>{{ $commission->approver->name ?? '--'}}</td>
                        <td>
                                @if($commission->isWaitingApproval == 0)
                                    <span class="badge badge-success">Approved</span>
                                @elseif($commission->isWaitingApproval == 2)
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-danger">Pending Approval</span>
                                @endif
                            </td>
                        <td>
                             @if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)
                            <a href="{{ url('agency/commission/edit', [$commission->commision_id]) }}" class="btn btn-sm btn-primary" title="Edit commission">
                                <span><i class="fas fa-edit text-dark-pastel-green"></i></span>
                            </a>
                            @endif
                            @if($commission->initiator_id != Auth::user()->id && $commission->isWaitingApproval==1 && (Auth::user()->action_id == 2 || Auth::user()->action_id == 3))
                            <a  href="{{ route('agency.commission.approve', $commission->commision_id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-success btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-green"></i></span>
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
{{-- Jquery datatables enabled --}}
@section('scripts')
@parent
<script>
    jQuery(document).ready(function() {
        jQuery('#ajaxSubmit').click(function(e) {
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
                {
                    {
                        --url: "{{ url('/ib/subscriber/'.Crypt::encrypt(1)) }}", --
                    }
                }
                url: "{{ url('/ib/subscriber/'.Crypt::encrypt(1)) }}",
                method: 'get',
                data: {},
                success: function(result) {
                    var cleanData = JSON.parse(result.data);
                    console.log("Here is the data received ", cleanData);
                    var tempData = "<tr>\n" +
                        "                                        <td>Name</td>\n" +
                        "                                        <td>" + cleanData[0]['name'] + "</td>\n" +
                        "                                    </tr>\n" +
                        "                                    <tr>\n" +
                        "                                        <td>Email</td>\n" +
                        "                                        <td>" + cleanData[0]['email'] + "</td>\n" +
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

                }
            });
        });
    });

    jQuery(document).ready(function() {
        jQuery('#loadingModel').click(function(e) {
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
                success: function(result) {
                    var cleanData = JSON.parse(result.data);
                    var tempData = "<tr>\n" +
                        "                                        <td>Name</td>\n" +
                        "                                        <td>" + cleanData[0]['name'] + "</td>\n" +
                        "                                    </tr>\n" +
                        "                                    <tr>\n" +
                        "                                        <td>Email</td>\n" +
                        "                                        <td>" + cleanData[0]['email'] + "</td>\n" +
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

                }
            });
        });
    });





    $(function() {
        let deleteButtonTrans = '{{ trans('
        global.datatables.delete ') }}'
        let deleteButton = {
            text: deleteButtonTrans,
            url: "{{ route('admin.users.massDestroy') }}",
            className: 'btn-danger',
            action: function(e, dt, node, config) {
                var ids = $.map(dt.rows({
                    selected: true
                }).nodes(), function(entry) {
                    return $(entry).data('entry-id')
                });

                if (ids.length === 0) {
                    alert('{{ trans('
                        global.datatables.zero_selected ') }}')

                    return
                }

                if (confirm('{{ trans('
                        global.areYouSure ') }}')) {
                    $.ajax({
                            headers: {
                                'x-csrf-token': _token
                            },
                            method: 'POST',
                            url: config.url,
                            data: {
                                ids: ids,
                                _method: 'DELETE'
                            }
                        })
                        .done(function() {
                            location.reload()
                        })
                }
            }
        }
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
        @can('user_delete')
        dtButtons.push(deleteButton)
        @endcan

        $('.datatable:not(.ajaxTable)').DataTable({
            buttons: dtButtons
        })
    })
</script>
<script>
    $(function() {
        $('.datatable').DataTable()
    });
</script>
@endsection
@endsection