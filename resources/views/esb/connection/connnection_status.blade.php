@extends('layouts.admin')
@section('content')
    @can('user_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success btn-sm" href="{{ route("admin.users.create") }}">
                    {{ trans('global.add') }} {{ trans('global.user.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            All Connections
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            Name
                        </th>
                        <th>
                            Entity
                        </th>
                        <th>
                            Type
                        </th>
                        <th>
                            Port
                        </th>
                        <th>
                            Connection Time
                        </th>
                        <th>
                            Disconnection Time
                        </th>
                        <th>
                            State
                        </th>
                        <th>
                            IP Address
                        </th>
                        <th>
                           Status
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($trnxs  as $trnx)
                        <tr>
                            <td>

                            </td>
                            <td>
                                {{ $trnx->name ?? '' }}
                            </td>
                            <td>
                                {{ $trnx->entity ?? '' }}
                            </td>
                            <td>
                                {{ $trnx->type ?? '' }}
                            </td>
                            <td>
                                {{ $trnx->port ?? '' }}
                            </td>
                            <td>
                                {{ $trnx->timeConnected ?? '' }}
                            </td>
                            <td>
                                {{ $trnx->timeDisconnected ?? '' }}
                            </td>
                            <td>
                                {{ $trnx->state ?? '' }}
                            </td>
                            <td>
                                {{ $trnx->IP_Address ?? '' }}
                            </td>
                            <td>
                                {{ $trnx->status ?? '' }}
                            </td>
                            <td><span class="badge badge-success">Active</span></td>
                        </tr>
                    @endforeach


                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="container-fluid">
        @foreach($users as $user)
            <div class="modal fade" id="largeModal{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="
                        background: rgb(255,255,1);
                        background: linear-gradient(to left, rgba(255,255,1,1) 0%, rgba(2,144,252,1) 90%);">
                            <h4 class="modal-title "> <i class="fas fa-home"></i> <strong>User Profile => {{ $user->name }}</strong></h4>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        </div>
                        <div class="modal-body">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#all{{ $user->id }}" role="tab" aria-controls="home">
                                        User</a></li>
                                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#pending{{ $user->id }}" role="tab" aria-controls="profile">
                                        User Group</a></li>
                                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#reversal{{ $user->id }}" role="tab" aria-controls="messages">
                                        Group Permission</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="all{{ $user->id }}" role="tabpanel">
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
                                            <th colspan="2">USER DETAILS</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>Name</td>
                                            <td>{{ $user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td>Email</td>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                        <tr>
                                            <td>Roles</td>
                                            <td>
                                                @foreach($user->roles as $key => $item)
                                                    <span class="badge badge-info">{{ $item->title }}</span>
                                                @endforeach
                                            </td>
                                        </tr>
                                        <tr>


                                            <td>Status</td>
                                            @if($user->status == 0)
                                                <td><span class="badge badge-danger">Inactive</span></td>
                                            @elseif($user->status == 1)
                                                <td><span class="badge badge-success">Active</span></td>
                                            @elseif($user->status == 2)
                                                <td><span class="badge badge-warning">Pending</span></td>
                                            @else
                                                <td><span class="badge badge-info">Unknown</span></td>
                                            @endif
                                        </tr>
                                        </tbody>
                                    </table>
                                    {{--</div>--}}
                                    {{--</div>--}}

                                </div>
                                <div class="tab-pane" id="pending{{ $user->id }}" role="tabpanel">
                                    <p>{{ $user->name }}</p>

                                </div>
                                <div class="tab-pane" id="reversal{{ $user->id }}" role="tabpanel">
                                    <p>{{ $user->email }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                    <!-- /.modal-content-->
                </div>
                <!-- /.modal-dialog-->
            </div>
        @endforeach

    </div>

    <div class="container-fluid">
        <div class="modal fade" id="largeModalTesting" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="
                        background: rgb(255,255,1);
                        background: linear-gradient(to left, rgb(255,27,16) 0%, rgba(255,255,1) 90%);">
                        <h4 class="modal-title "> <i class="fas fa-home"></i> <strong>User Profile => Username</strong></h4>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">





                        <!-- MultiStep Form -->
                        <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
                            <h2><strong>Sign Up Your User Account</strong></h2>
                            <p>Fill all form field to go to next step</p>
                            <div class="row">
                                <div class="col-md-12 mx-0">
                                    <form id="msform">
                                        <!-- progressbar -->
                                        <ul id="progressbar">
                                            <li class="active" id="account"><strong>Account</strong></li>
                                            <li id="personal"><strong>Personal</strong></li>
                                            <li id="payment"><strong>Payment</strong></li>
                                            <li id="confirm"><strong>Finish</strong></li>
                                        </ul> <!-- fieldsets -->
                                        <fieldset>
                                            <div class="form-card">
                                                <h2 class="fs-title">Account Information</h2> <input type="email" name="email" placeholder="Email Id" /> <input type="text" name="uname" placeholder="UserName" /> <input type="password" name="pwd" placeholder="Password" /> <input type="password" name="cpwd" placeholder="Confirm Password" />
                                            </div> <input type="button" name="next" class="next action-button" value="Next Step" />
                                        </fieldset>
                                        <fieldset>
                                            <div class="form-card">
                                                <h2 class="fs-title">Personal Information</h2> <input type="text" name="fname" placeholder="First Name" /> <input type="text" name="lname" placeholder="Last Name" /> <input type="text" name="phno" placeholder="Contact No." /> <input type="text" name="phno_2" placeholder="Alternate Contact No." />
                                            </div> <input type="button" name="previous" class="previous action-button-previous" value="Previous" /> <input type="button" name="next" class="next action-button" value="Next Step" />
                                        </fieldset>
                                        <fieldset>
                                            <div class="form-card">
                                                <h2 class="fs-title">Payment Information</h2>
                                                <div class="radio-group">
                                                    <div class='radio' data-value="credit"><img src="https://i.imgur.com/XzOzVHZ.jpg" width="200px" height="100px"></div>
                                                    <div class='radio' data-value="paypal"><img src="https://i.imgur.com/jXjwZlj.jpg" width="200px" height="100px"></div> <br>
                                                </div> <label class="pay">Card Holder Name*</label> <input type="text" name="holdername" placeholder="" />
                                                <div class="row">
                                                    <div class="col-9"> <label class="pay">Card Number*</label> <input type="text" name="cardno" placeholder="" /> </div>
                                                    <div class="col-3"> <label class="pay">CVC*</label> <input type="password" name="cvcpwd" placeholder="***" /> </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-3"> <label class="pay">Expiry Date*</label> </div>
                                                    <div class="col-9"> <select class="list-dt" id="month" name="expmonth">
                                                            <option selected>Month</option>
                                                            <option>January</option>
                                                            <option>February</option>
                                                            <option>March</option>
                                                            <option>April</option>
                                                            <option>May</option>
                                                            <option>June</option>
                                                            <option>July</option>
                                                            <option>August</option>
                                                            <option>September</option>
                                                            <option>October</option>
                                                            <option>November</option>
                                                            <option>December</option>
                                                        </select> <select class="list-dt" id="year" name="expyear">
                                                            <option selected>Year</option>
                                                        </select> </div>
                                                </div>
                                            </div> <input type="button" name="previous" class="previous action-button-previous" value="Previous" /> <input type="button" name="make_payment" class="next action-button" value="Confirm" />
                                        </fieldset>
                                        <fieldset>
                                            <div class="form-card">
                                                <h2 class="fs-title text-center">Success !</h2> <br><br>
                                                <div class="row justify-content-center">
                                                    <div class="col-3"> <img src="https://img.icons8.com/color/96/000000/ok--v2.png" class="fit-image"> </div>
                                                </div> <br><br>
                                                <div class="row justify-content-center">
                                                    <div class="col-7 text-center">
                                                        <h5>You Have Successfully Signed Up</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </form>
                                </div>
                            </div>
                        </div>









                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content-->
            </div>
            <!-- /.modal-dialog-->
        </div>

    </div>
@section('scripts')
    @parent
    <script>

        $(document).ready(function(){

            var current_fs, next_fs, previous_fs; //fieldsets
            var opacity;

            $(".next").click(function(){

                current_fs = $(this).parent();
                next_fs = $(this).parent().next();

//Add Class Active
                $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

//show the next fieldset
                next_fs.show();
//hide the current fieldset with style
                current_fs.animate({opacity: 0}, {
                    step: function(now) {
// for making fielset appear animation
                        opacity = 1 - now;

                        current_fs.css({
                            'display': 'none',
                            'position': 'relative'
                        });
                        next_fs.css({'opacity': opacity});
                    },
                    duration: 600
                });
            });

            $(".previous").click(function(){

                current_fs = $(this).parent();
                previous_fs = $(this).parent().prev();

//Remove class active
                $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

//show the previous fieldset
                previous_fs.show();

//hide the current fieldset with style
                current_fs.animate({opacity: 0}, {
                    step: function(now) {
// for making fielset appear animation
                        opacity = 1 - now;

                        current_fs.css({
                            'display': 'none',
                            'position': 'relative'
                        });
                        previous_fs.css({'opacity': opacity});
                    },
                    duration: 600
                });
            });

            $('.radio-group .radio').click(function(){
                $(this).parent().find('.radio').removeClass('selected');
                $(this).addClass('selected');
            });

            $(".submit").click(function(){
                return false;
            })

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
