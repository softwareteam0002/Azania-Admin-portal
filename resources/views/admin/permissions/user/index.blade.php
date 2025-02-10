@extends('layouts.admin')
@section('content')

    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#rolePermission" role="tab" aria-controls="home">
                Role Permissions</a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#userPermission" role="tab" aria-controls="profile">
                User Permission</a></li>
        <!-- <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#permissionManagement" role="tab" aria-controls="profile">
                Permission Management</a></li> -->
    </ul>
    <div class="tab-content mt-3" style="background-color: #E4E5E7;">
        <div class="tab-pane active" id="rolePermission" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            Add New Role
                        </div>

                        <div class="card-body">
                            <!-- Success Alert -->
                            <div id="notification"></div>

                            <form >
                                @csrf
                                <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                                    <label for="title">{{ trans('global.role.fields.title') }}*</label>
                                    <input type="text" id="title" name="title" class="form-control" >
                                    @if($errors->has('title'))
                                        <em class="invalid-feedback">
                                            {{ $errors->first('title') }}
                                        </em>
                                    @endif
                                    <p class="helper-block">
                                        {{ trans('global.role.fields.title_helper') }}
                                    </p>
                                </div>
                                <div>
                                    <input class="btn btn-primary" type="submit" id="ajaxSubmit" value="{{ trans('global.save') }}">
                                </div>
                            </form>



                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            Grant Role Permissions
                        </div>

                        <div class="card-body">
                            <div class="form-group">
                                <div id="notification-update"></div>
                                {{--<label for="sel1">Select Role</label>--}}
                                <select class="form-control" id="dropDownRoles">
                                    <option selected>Select Role</option>
                                    @foreach($roles as $userRole)
                                        <option  value="{{Crypt::encrypt($userRole->id) }}">{{$userRole->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <form>
                                <table class="table table-responsive-sm table-bordered table-striped table-sm">
                                    <thead>
                                    {{--<tr>--}}
                                    {{--<th>name</th>--}}
                                    {{--<th>email</th>--}}
                                    {{--<th>Role</th>--}}
                                    {{--<th>Status</th>--}}
                                    {{--</tr>--}}
                                    <tr class="bottom-border-color-green primary" data-toggle="modal" data-target="#largeModalTesting">
                                        <th colspan="2">Permissions</th>
                                    </tr>
                                    </thead>
                                    <tbody id="permissionBody">
                                    @foreach($permissions as $id => $permissions)
                                        <tr>
                                            <td>{{ $permissions }}</td>
                                            <td>
                                                <label class="switch switch-pill switch-outline-success-alt float-right">
                                                    <input class="switch-input" type="checkbox" name="perm"  value="{{ $id }}" {{ (in_array($id, old('permissions', [])) || isset($role) && $role->permissions->contains($id)) ? 'checked' : '' }}>
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </td>
                                        </tr>

                                    @endforeach

                                    </tbody>
                                </table>

                                <input class="btn btn-primary" type="submit" id="ajaxSubmitPermission" value="{{ trans('global.save') }}">

                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="tab-pane" id="userPermission" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <!-- Success Alert -->
                        <div id="notification-group-user"></div>
                        <div class="card-header">
                            Grant User Group Permissions
                        </div>

                        <div class="card-body">
                            <div class="form-group">
                                <div id="notification-update"></div>
                                <select class="form-control" id="dropDownUsers">
                                    @foreach($users as $user)
                                        <option  value="{{Crypt::encrypt($user->id) }}" {{Auth::user() == $user ? 'selected' : ''}}>{{$user->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <form>
                                <table class="table table-responsive-sm table-bordered table-striped table-sm">
                                    <thead>
                                    <tr class="bottom-border-color-green primary" data-toggle="modal" data-target="#largeModalTesting">
                                        <th colspan="2">Groups</th>

                                    </tr>
                                    </thead>
                                    <tbody id="groupPermissionBody">
                                    @foreach($roles as $id => $rol)
                                        <tr>
                                            <td>{{ $rol->title }}</td>
                                            <td>
                                                <label class="switch switch-pill switch-outline-success-alt float-right">
                                                    <input class="switch-input" type="radio" name="radioRole"  value="{{ $rol->id }}" {{ $userRoles->id == $rol->id ? 'checked' : '' }}>
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </td>
                                        </tr>

                                    @endforeach

                                    </tbody>
                                </table>

                                <input class="btn btn-primary" type="submit" id="ajaxSubmitGroups" value="{{ trans('global.save') }}">

                            </form>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-md-6">
                    <h2>Column 2</h2>
                    <p>Some text..</p>
                </div> -->
            </div>

        </div>
        <!-- <div class="tab-pane" id="permissionManagement" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <h2>Column 1</h2>
                    <p>Some text..</p>
                </div>
                <div class="col-md-6">
                    <h2>Column 2</h2>
                    <p>Some text..</p>
                </div>
            </div>

        </div> -->
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

        jQuery(document).ready(function(){
            jQuery('#ajaxSubmitGroups').click(function(e){
                e.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                var checkboxes = document.getElementsByName("radioRole");
                var checkboxesChecked = [];
                // loop over them all
                for (var i=0; i<checkboxes.length; i++) {
                    // And stick the checked ones onto an array...
                    if (checkboxes[i].checked) {
                        console.log("Id of checked box is " + checkboxes[i].value);
                        checkboxesChecked.push(checkboxes[i].value);
                    }
                }
                console.log("User selected Id "+ document.getElementById("dropDownUsers").value);
                console.log("Total roles " + checkboxesChecked[0]);
                jQuery.ajax({
                    url: "{{  url("/admin/roles/asyc/role/user/update") }}",
                    method: 'post',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "role": checkboxesChecked,
                        "user": document.getElementById("dropDownUsers").value
                    },
                    success: function(result){
                        if (result['status'] === '00'){


                            document.getElementById('notification-group-user').innerHTML = "<div class=\"alert alert-success alert-dismissible fade show\">\n" +
                                "                                        <strong>Success!</strong> Permission was updated successfully.\n" +
                                "                                        <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>\n" +
                                "                                    </div>";
                        }else {

                            document.getElementById('notification-group-user').innerHTML = "<div class=\"alert alert-danger alert-dismissible fade show\">\n" +
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
                        document.getElementById('notification-group-user').innerHTML = "<div class=\"alert alert-warning alert-dismissible fade show\">\n" +
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

        $(document).ready(function(){
            $("#dropDownUsers").change(function() {
                var id = $(this).find(":selected").val();
                $.ajax({
                    url: "{{  url("/admin/roles/asyc/user") }}".concat("/"+id),
                    method: 'get',
                    dataType: "json",
                    cache: false,
                    success: function(result) {

                        if (result['status'] === '00') {
                            var allRoles = JSON.parse(result.allRoles);
                            var userRoles = JSON.parse(result.userRoles)

                            var innerHtml = "";
                            var checkValue = "";
                            for (var i = 0; i < allRoles.length; i++) {
                                if (allRoles[i].id === userRoles[0].id){
                                    checkValue = "checked";

                                }
                                innerHtml += "<tr>\n" +
                                    "         <td>" + allRoles[i].title + "</td>\n" +
                                    "         <td>\n" +
                                    "         <label class=\"switch switch-pill switch-outline-success-alt float-right\">\n" +
                                    "         <input class=\"switch-input\" type=\"radio\" name=\"radioRole\"  value=\"" + allRoles[i].id + "\" "+ checkValue +" >\n" +
                                    "         <span class=\"switch-slider\"></span>\n" +
                                    "         </label>\n" +
                                    "         </td>\n" +
                                    "         </tr>";

                                checkValue = "";
                            }

                            document.getElementById("groupPermissionBody").innerHTML = innerHtml;
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
            @can('user_delete')
            dtButtons.push(deleteButton)
            @endcan

            $('.datatable:not(.ajaxTable)').DataTable({ buttons: dtButtons })
        })

    </script>
@endsection
@endsection