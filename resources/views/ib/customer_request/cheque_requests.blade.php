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
            <li class="breadcrumb-item active">Cheque requests</li>
        </ol>
    </nav>


    <h5>Cheque requests details</h5>
    <hr/>


    <div class="card">
        <div class="card-body">
            <h6 class="mb-3">All cheque requests</h6>
            <div class="table-responsive">
                <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            No. Books
                        </th>
                        <th>
                            No. Leaves
                        </th>
                        <th>
                            Account ID
                        </th>
                        <th>
                            Transaction Date
                        </th>
                        <th>
                            Requester
                        </th>
                        <th>
                            Initiator
                        </th>
                        <th>
                            Approver
                        </th>
                        <th>
                            Status
                        </th>
                        <th>
                            Action
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($requests as $request)
                        <tr data-entry-id="{{$request->id}}" data-toggle="modal" data-target="#largeModal{{ $request->id }}">
                            <td>

                            </td>
                            <td>
                                {{$request->number_of_books}}
                            </td>
                            <td>
                                {{$request->number_of_leaves}}
                            </td>
                            <td>
                                {{$request->account_number ?? 'Not Provided'}}
                            </td>
                            <td>
                                {{$request->created_at}}
                            </td>
                            <td>
                                {{$request->users->name ?? ''}}
                            </td>
                            <td>
                                {{ $request->initiators->name ?? '--' }}
                            </td>
                            <td>
                                {{ $request->approvers->name ?? '--' }}
                            </td>
                            <td>

                                @if($request->status_id == 1)

                                    <h5><span class="badge badge-danger">Submitted</span></h5>

                                @endif
                                @if($request->status_id == 2)

                                    <h5><span class="badge badge-warning">On Progress</span></h5>

                                @endif
                                @if($request->status_id == 4)

                                    <h5><span class="badge badge-success">Success</span></h5>

                                @endif
                                 @if($request->status_id == 6)

                                    <h5><span class="badge badge-danger">Failed</span></h5>

                                @endif
                            </td>
                            <td>
                                <a class="btn btn-primary btn-sm" title="Show" href="{{ url('ib/cheque/request/show',[$request->id]) }}"><b><i class="fas fa-eye "></i></b></a>
                                <form action="{{ url('ib/requests/actions') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id" value="{{ $request->id }}">
                                    <input type="hidden" name="request_type" value="cheque_book_request">
                                    

                                    @if(Auth::user()->action_id==1 && $request->status_id == 1)
                                        <button type="submit" name="status" value="2" title="Initiate" class="btn btn-secondary btn-sm"><span><i class="fas fa-check text-dark-pastel-green"></i></span></button>
                                    @endif

                                    @if(Auth::user()->action_id==3 && $request->status_id == 1)
                                        <button type="submit" name="status" value="2" title="Initiate" class="btn btn-info btn-sm"><span><i class="fas fa-check text-dark-pastel-blue"></i></span></button>
                                    @endif

                                    @if(Auth::user()->action_id == 3 && $request->status_id == 2)
                                        <button title="Success" type="submit" name="status" value="4" class="btn btn-success btn-sm"><span><i class="fas fa-check text-dark-pastel-green"></i></span></button>
                                        <button type="submit" name="status" value="6" title="Fail"  class="btn btn-danger btn-sm"><span><i class="fas fa-remove text-dark-pastel-green"></i></span></button>
                                    @endif

                                    @if(Auth::user()->action_id == 2 && $request->status_id == 2)
                                      <button title="Success" type="submit" name="status" value="4" class="btn btn-success btn-sm"><span><i class="fas fa-check text-dark-pastel-green"></i></span></button>
                                        <button type="submit" name="status" value="6" title="Fail"  class="btn btn-danger btn-sm"><span><i class="fas fa-remove text-dark-pastel-green"></i></span></button>
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
            jQuery('#ajaxSubmitPermission').click(function(e){
                e.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                var checkboxes = document.getElementsByName("perm");
                var checkboxesChecked = [];
                // loop over them all
                for (var i=0; i<checkboxes.length; i++) {
                    // And stick the checked ones onto an array...
                    if (checkboxes[i].checked) {
                        checkboxesChecked.push(checkboxes[i].value);
                    }
                }
                console.log("Select option from dropdown list is "+ document.getElementById("dropDownRoles").value);
                jQuery.ajax({
                    url: "{{  url("/admin/roles/asyc/role/permission/update") }}",
                    method: 'post',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "permission": checkboxesChecked,
                        "role": document.getElementById("dropDownRoles").value
                    },
                    success: function(result){
                        if (result['status'] === '00'){


                            document.getElementById('notification-update').innerHTML = "<div class=\"alert alert-success alert-dismissible fade show\">\n" +
                                "                                        <strong>Success!</strong> Permission was updated successfully.\n" +
                                "                                        <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>\n" +
                                "                                    </div>";
                        }else {

                            document.getElementById('notification-update').innerHTML = "<div class=\"alert alert-danger alert-dismissible fade show\">\n" +
                                "    <strong>Error!</strong> A problem has been occurred while submitting your data.\n" +
                                "    <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>\n" +
                                "</div>";
                        }

                    },
                    error: function (jqXHR, exception) {
                        var msg = '';
                        if (jqXHR.status === 0) {
                            msg = 'Not connect.\n Verify Network.';
                        } else if (jqXHR.status == 404) {
                            msg = 'Requested page not found. [404]';
                        } else if (jqXHR.status == 500) {
                            msg = 'Internal Server Error [500].';
                        } else if (exception === 'parsererror') {
                            msg = 'Requested JSON parse failed.';
                        } else if (exception === 'timeout') {
                            msg = 'Time out error.';
                        } else if (exception === 'abort') {
                            msg = 'Ajax request aborted.';
                        } else {
                            msg = 'Uncaught Error.\n' + jqXHR.responseText;
                        }
                        document.getElementById('notification-update').innerHTML = "<div class=\"alert alert-warning alert-dismissible fade show\">\n" +
                            "    <strong>Warning!</strong> " + msg + "\n" +
                            "    <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>\n" +
                            "</div>"
                    }
                });

            });
        });

        $(document).ready(function(){
            $("#dropDownRoles").change(function() {
                var id = $(this).find(":selected").val();
                $.ajax({
                    url: "{{  url("/admin/roles/asyc/role") }}".concat("/"+id),
                    method: 'get',
                    dataType: "json",
                    cache: false,
                    success: function(result) {

                        if (result['status'] === '00') {
                            var role = JSON.parse(result.role);
                            var permission = JSON.parse(result.permission);
                            var rolePermission = JSON.parse(result.rolePermission);

                            var innerHtml = "";
                            var checkValue = "";
                            for (var i = 0; i < permission.length; i++) {
                                for (var j = 0; j < rolePermission.length; j++){
                                    if (rolePermission[j].title === permission[i].title){
                                        checkValue = "checked";
                                        break;
                                    }
                                }
                                innerHtml += "<tr>\n" +
                                    "         <td>" + permission[i].name + "</td>\n" +
                                    "         <td>\n" +
                                    "         <label class=\"switch switch-pill switch-outline-success-alt float-right\">\n" +
                                    "         <input class=\"switch-input\" type=\"checkbox\" name=\"perm\"  value=\"" + permission[i].id + "\" "+ checkValue +" >\n" +
                                    "         <span class=\"switch-slider\"></span>\n" +
                                    "         </label>\n" +
                                    "         </td>\n" +
                                    "         </tr>";

                                checkValue = "";
                            }

                            document.getElementById("permissionBody").innerHTML = innerHtml;
                        }
                    }
                });
            })
        });

        jQuery(document).ready(function(){
            jQuery('#ajaxSubmit').click(function(e){
                e.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url: "{{  url("/admin/roles/asyc/add") }}",
                    method: 'post',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "title": document.getElementById('title').value
                    },
                    success: function(result){
                        if (result['status'] === '00'){
                            document.getElementById('title').value = "";
                            document.getElementById('notification').innerHTML = "<div class=\"alert alert-success alert-dismissible fade show\">\n" +
                                "                                        <strong>Success!</strong> Role was created successfully.\n" +
                                "                                        <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>\n" +
                                "                                    </div>";

                            jQuery.ajax({
                                url: "{{  url("/admin/roles/asyc/list") }}",
                                method: 'get',
                                success: function(result){
                                    console.log(result);
                                    if (result['status'] === '00'){
                                        var data = result.data;
                                        var newOpt = "<option selected>Select Role</option>";
                                        for (rs in data){
                                            newOpt += "<option id=" + rs + " value=" + rs + ">" + data[rs] +"</option>";
                                        }
                                        document.getElementById('dropDownRoles').innerHTML = newOpt;
                                    }else {

                                        console.log(result);
                                    }

                                },
                                error: function (jqXHR, exception) {
                                    var msg = '';
                                    if (jqXHR.status === 0) {
                                        msg = 'Not connect.\n Verify Network.';
                                    } else if (jqXHR.status == 404) {
                                        msg = 'Requested page not found. [404]';
                                    } else if (jqXHR.status == 500) {
                                        msg = 'Internal Server Error [500].';
                                    } else if (exception === 'parsererror') {
                                        msg = 'Requested JSON parse failed.';
                                    } else if (exception === 'timeout') {
                                        msg = 'Time out error.';
                                    } else if (exception === 'abort') {
                                        msg = 'Ajax request aborted.';
                                    } else {
                                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                                    }
                                    console.log(msg);
                                }
                            });


                        }else {

                            document.getElementById('notification').innerHTML = "<div class=\"alert alert-danger alert-dismissible fade show\">\n" +
                                "    <strong>Error!</strong> A problem has been occurred while submitting your data.\n" +
                                "    <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>\n" +
                                "</div>";
                        }

                    },
                    error: function (jqXHR, exception) {
                        var msg = '';
                        if (jqXHR.status === 0) {
                            msg = 'Not connect.\n Verify Network.';
                        } else if (jqXHR.status == 404) {
                            msg = 'Requested page not found. [404]';
                        } else if (jqXHR.status == 500) {
                            msg = 'Internal Server Error [500].';
                        } else if (exception === 'parsererror') {
                            msg = 'Requested JSON parse failed.';
                        } else if (exception === 'timeout') {
                            msg = 'Time out error.';
                        } else if (exception === 'abort') {
                            msg = 'Ajax request aborted.';
                        } else {
                            msg = 'Uncaught Error.\n' + jqXHR.responseText;
                        }
                        document.getElementById('notification').innerHTML = "<div class=\"alert alert-warning alert-dismissible fade show\">\n" +
                            "    <strong>Warning!</strong> " + msg + "\n" +
                            "    <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>\n" +
                            "</div>"
                    }
                });

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
            {{--@can('user_delete')--}}
            {{--dtButtons.push(deleteButton)--}}
            {{--@endcan--}}

            $('.datatable:not(.ajaxTable)').DataTable({ buttons: dtButtons })
        })

    </script>
@endsection

@endsection
