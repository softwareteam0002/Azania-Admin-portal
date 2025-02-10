@extends('layouts.admin')
@section('content')
    @can('user_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success btn-sm" href="{{ route("admin.users.create") }}">
                    Add Agent
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            All users
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            Agent Name
                        </th>
                        <th>
                            MSISDN
                        </th>
                        <th>
                            Language
                        </th>
                        <th>
                            Agent Number
                        </th>
                        <th>
                            Terminal Id
                        </th>
                        <th>
                            Status
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr data-entry-id="1" data-toggle="modal" data-target="#largeModal1">
                        <td>

                        </td>
                        <td>
                            Fadhili Chambo
                        </td>
                        <td>
                            255654896656
                        </td>
                        <td>
                            Kiswahili
                        </td>
                        <td>
                            12358
                        </td>
                        <td>
                            ABC1234
                        </td>
                        <td>
                            <span class="badge badge-success">Active</span>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="container-fluid">
        {{--@foreach($users as $user)--}}
        <div class="modal fade" id="largeModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="
                        background: rgb(255,255,1);
                        background: linear-gradient(to left, rgba(255,255,1,1) 0%, rgba(2,144,252,1) 90%);">
                        <h4 class="modal-title "> <i class="fas fa-home"></i> <strong>Agent Profile => Fadhili Chambo</strong></h4>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#all1" role="tab" aria-controls="home">
                                    Agent</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#pending1" role="tab" aria-controls="profile">
                                    Transaction</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#pending2" role="tab" aria-controls="profile">
                                    Account</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#pending3" role="tab" aria-controls="profile"  id="ajaxSubmit">
                                    Incident</a></li>
                        </ul>
                        @if(1===1)
                            <div class="text-center mb-4" id="oval" style="background-color: #0fc51f;">
                                <strong class="p-3" style="color: white;">Agent is Active</strong>
                            </div>
                        @elseif(1>1)
                            <div class="text-center mb-4" id="oval" style="background-color: #c50900;">
                                <strong class="p-3" style="color: white;">Agent is Active</strong>
                            </div>
                        @endif
                        <div class="text-center mt-3 mb-3">
                            <div>
                                <button type="button" class="btn btn-sm btn-primary">Add Account</button>
                                <button type="button" class="btn btn-sm btn-info">Edit Agent</button>
                                <button type="button" class="btn btn-sm btn-danger">Suspend Agent</button>
                                <button type="button" class="btn btn-sm btn-primary" id="btnBlock">Block Agent</button>
                                <button type="button" class="btn btn-sm btn-warning">Reset PIN</button>
                                <button type="button" class="btn btn-sm btn-dark">Register Incident</button>
                                <button type="button" class="btn btn-sm btn-secondary">Manage Class</button>
                            </div>
                        </div>
                        <div class="tab-content mt-5">
                            <div class="tab-pane active" id="all1" role="tabpanel">
                                {{--<div class="card">--}}
                                {{--<div class="card-header"><i class="fa fa-align-justify"></i>User information</div>--}}
                                {{--<div class="card-body">--}}
                                <table class="table table-responsive-sm table-bordered table-striped table-sm">
                                    <thead>
                                    {{--<tr>--}}
                                    {{--<th>name</th>--}}
                                    {{--<th>email</th>--}}
                                    {{--<th>Role</th>--}}
                                    {{--<th>Status</th>--}}
                                    {{--</tr>--}}
                                    <tr class="bottom-border-color-green primary" data-toggle="modal" data-target="#largeModalTesting">
                                        <th colspan="2">AGENT DETAILS</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Name</td>
                                        <td>Fadhili</td>
                                    </tr>
                                    <tr>
                                        <td>MSISDN</td>
                                        <td>255654896656</td>
                                    </tr>
                                    <tr>
                                        <td>Language</td>
                                        <td>
                                            Kiswahili
                                        </td>
                                    </tr>
                                    <tr>


                                        <td>Status</td>
                                        <td><span class="badge badge-success">Active</span></td>
                                        {{--@if($user->status == 0)--}}
                                        {{--<td><span class="badge badge-danger">Inactive</span></td>--}}
                                        {{--@elseif($user->status == 1)--}}
                                        {{--<td><span class="badge badge-success">Active</span></td>--}}
                                        {{--@elseif($user->status == 2)--}}
                                        {{--<td><span class="badge badge-warning">Pending</span></td>--}}
                                        {{--@else--}}
                                        {{--<td><span class="badge badge-info">Unknown</span></td>--}}
                                        {{--@endif--}}
                                    </tr>
                                    </tbody>
                                </table>
                                {{--</div>--}}
                                {{--</div>--}}

                            </div>
                            <div class="tab-pane" id="pending1" role="tabpanel">
                                <p>Fadhili Chambo</p>

                            </div>
                            <div class="tab-pane" id="pending2" role="tabpanel">
                                <p>Pending 2</p>

                            </div>
                            <div class="tab-pane" id="pending3" role="tabpanel">
                                <table class="table table-responsive-sm table-bordered table-striped table-sm">
                                    <thead>
                                    {{--<tr>--}}
                                    {{--<th>name</th>--}}
                                    {{--<th>email</th>--}}
                                    {{--<th>Role</th>--}}
                                    {{--<th>Status</th>--}}
                                    {{--</tr>--}}
                                    <tr class="bottom-border-color-green primary" data-toggle="modal" data-target="#largeModalTesting">
                                        <th colspan="2">AGENT DETAILS</th>
                                    </tr>
                                    </thead>
                                    <tbody id="ajaxId">

                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-sm btn-secondary" type="button" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content-->
            </div>
            <!-- /.modal-dialog-->
        </div>
        {{--@endforeach--}}

    </div>

@section('scripts')
    @parent
    <script>



        jQuery(document).ready(function(){
            jQuery('#ajaxSubmit').click(function(e){
                e.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url: "{{ url('/agency/agent/'.Crypt::encrypt(1)) }}",
                    method: 'get',
                    data: {},
                    success: function(result){
                        var cleanData = JSON.parse(result.data);
                        var tempData = "<tr>\n" +
                            "                                        <td>Name</td>\n" +
                            "                                        <td>"+cleanData['name']+"</td>\n" +
                            "                                    </tr>\n" +
                            "                                    <tr>\n" +
                            "                                        <td>Email</td>\n" +
                            "                                        <td>"+cleanData['email']+"</td>\n" +
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
                        document.getElementById('ajaxId').innerHTML = tempData;

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
