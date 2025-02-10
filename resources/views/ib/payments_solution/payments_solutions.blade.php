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

    <h4>{{ $institute_name }}</h4>
    <hr/>

    <h5>Payment Solutions</h5>
    <hr/>
    
    <hr/>

    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link @if($title=='payments') active @endif" href="{{ url('ib/payments_solution/payments/'.$id) }}">PAYMENTS</a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if($title=='services') active @endif" href="{{ url('ib/payments_solution/services/'.$id) }}">SERVICES</a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if($title=='payers') active @endif" href="{{ url('ib/payments_solution/payers/'.$id) }}">PAYERS</a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if($title=='levels') active @endif" href="{{ url('ib/payments_solution/levels/'.$id) }}">LEVELS</a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if($title=='members') active @endif" href="{{ url('ib/payments_solution/members/'.$id) }}">MEMBERS</a>
        </li>
    </ul>

    <hr/>

    <div class="card">
        <div class="card-body">
            <h6 class="mb-3">ALL {{ strtoupper($title) }} </h6>
            <div class="table-responsive">
                @if($title=="members")
                    @include('ib.payments_solution.members')
                @endif
                @if($title=="levels")
                    @include('ib.payments_solution.levels')
                @endif
                @if($title=="payments")
                    @include('ib.payments_solution.payments')
                @endif
                @if($title=="services")
                    @include('ib.payments_solution.services')
                @endif
                @if($title=="payers")
                    @include('ib.payments_solution.payers')
                @endif
            </div>
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


            $('.datatable').DataTable( {
                "order": [[ 1, "desc" ]]
            } );
        })

    </script>
@endsection
@endsection
