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
            <li class="breadcrumb-item active">System Users</li>
        </ol>
    </nav>

    <h5>System Users</h5>
    <hr/>

    <div class="card mb-5">
        <div class="card-body">
            <h6>Create user form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ url('admin/users') }}" method="POST">
                @csrf
                @method('POST')
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="name">First Name:<span class="text-danger">*</span></label>
                            <input type="text" name="fname" class="form-control form-control-sm"
                                   placeholder="Enter first name here" required>
                        </div>
                    </div>

                    <div class="col">
                        <div class="form-group">
                            <label for="name">Last Name:<span class="text-danger">*</span></label>
                            <input type="text" name="lname" class="form-control form-control-sm"
                                   placeholder="Enter last name here" required>
                        </div>
                    </div>

                    <div class="col">
                        <div class="form-group">
                            <label for="name">Email:<span class="text-danger">*</span></label>
                            <input type="text" name="email" class="form-control form-control-sm"
                                   placeholder="Enter valid email address here" required>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="types">Select Level:<span class="text-danger">*</span></label>
                            <select name="action_id" class="form-control form-control-sm">
                                @foreach($actions as $action)
                                    <option value="{{ $action->id ?? ''}}">{{ $action->action_type ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group">
                            <label> Roles:<span class="text-danger">*</span></label>
                            <br/>

                            @foreach($roles as $role)
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" class="form-check-input" name="roles[]"
                                           id="roles{{ $role->id }}" value="{{ $role->id }}">&nbsp; {{$role->title}}
                                </div>
                            @endforeach
                        </div>
                    </div>


                    <div class="col-md-12">
                        <hr/>
                        <button type="submit" class="btn btn-success btn-sm">{{ trans('global.save') }}</button>
                        <button type="reset" class="btn btn-link btn-sm">Clear form</button>
                    </div>
                </div>


            </form>
        </div>
    </div>

    <h5>Registered Users</h5>
    <hr/>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive-sm">
                <table class="table table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th>{{ trans('global.user.fields.name') }}</th>
                        <th>{{ trans('global.user.fields.email') }}</th>
                        <th>User Action</th>
                        <th>{{ trans('global.user.fields.roles') }}</th>
                        <th>Initiator</th>
                        <th>Approver</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $key => $user)

                        <tr data-entry-id="{{ $user->id }}" data-toggle="modal">
                            <td>
                                {{ $user->name ?? '' }}
                            </td>
                            <td>
                                {{ $user->email ?? '' }}
                            </td>
                            <td>
                                {{ $user->actions->action_type ?? '' }}
                            </td>
                            <td>
                                @foreach($user->roles as $key => $item)
                                    <span class="badge badge-info">{{ $item->title }}</span>
                                @endforeach
                            </td>
                            <td>{{$user->initiators->name ?? '--'}}</td>
                            <td>{{$user->approvers->name ?? '--'}}</td>
                            <td>
                                @if($user->isWaitingApproval == 0)
                                    <span class="badge badge-success">Approved</span>
                                @elseif($user->isWaitingApproval == 2)
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-danger">Pending Approval</span>
                                @endif

                                @if($user->status == 0)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                @endif

                            </td>
                            <td>
                                @can('um_users_view')

                                    <form action="{{ url('admin/user/show') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                                        <button type="submit" class="btn btn-primary btn-sm" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </form>

                                @endcan

                                {{--@can('user_edit')--}}
                                <a class="btn btn-sm btn-info" href="{{ route('admin.users.edit', $user->id) }}"
                                   title='Edit'>
                                    <i class="fas fa-edit"></i>
                                </a>
                                {{--@endcan--}}

                                {{--@can('user_delete')--}}
                                @if( $user->isDeleted == 0)
                                    <form action="{{ route('admin.users.deleteUser', $user->id) }}" method="POST"
                                          onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                          class="d-inline">
                                        <input type="hidden" name="_method" value="PUT">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @endif
                                {{--@endcan--}}

                                <form action="{{ route('admin.users.reset') }}" method="POST"
                                      onsubmit="return confirm('{{ trans('global.areYouSure') }}');" class="d-inline">
                                    <input type="hidden" name="_method" value="POST">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <button type="submit" class="btn btn-warning btn-sm" title="Reset password">
                                        <i class="fas fa-user-lock"></i>
                                    </button>
                                </form>
                                @if(Auth::user()->id != $user->initiator_id && $user->isWaitingApproval==1 && $user->status == 0 && $user->isDeleted == 0)
                                    <a href="{{ route('admin.users.approve', $user->id) }}" type="submit"
                                       name="submit_value" value="approve" class="btn btn-success btn-sm">
                                        <span><i class="fas fa-check text-dark-pastel-green"></i></span>
                                        @endif
                                        @if((auth()->user()->id != $user->deletedBy_id) && $user->isDeleted == 1 && $user->isWaitingApproval == 1)
                                            <a title="Delete"
                                               href="{{ route('admin.users.deleteApproval', $user->id) }}" type="submit"
                                               name="submit_value" value="approve" class="btn btn-danger btn-sm">
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
            $(document).ready(function () {
                $('.datatable').DataTable({

                    "ordering": false
                });

            });
        </script>
        <script>
            $(document).ready(function () {

                var current_fs, next_fs, previous_fs; //fieldsets
                var opacity;

                $(".next").click(function () {

                    current_fs = $(this).parent();
                    next_fs = $(this).parent().next();

                    //Add Class Active
                    $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

                    //show the next fieldset
                    next_fs.show();
                    //hide the current fieldset with style
                    current_fs.animate({
                        opacity: 0
                    }, {
                        step: function (now) {
                            // for making fielset appear animation
                            opacity = 1 - now;

                            current_fs.css({
                                'display': 'none',
                                'position': 'relative'
                            });
                            next_fs.css({
                                'opacity': opacity
                            });
                        },
                        duration: 600
                    });
                });

                $(".previous").click(function () {

                    current_fs = $(this).parent();
                    previous_fs = $(this).parent().prev();

                    //Remove class active
                    $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

                    //show the previous fieldset
                    previous_fs.show();

                    //hide the current fieldset with style
                    current_fs.animate({
                        opacity: 0
                    }, {
                        step: function (now) {
                            // for making fielset appear animation
                            opacity = 1 - now;

                            current_fs.css({
                                'display': 'none',
                                'position': 'relative'
                            });
                            previous_fs.css({
                                'opacity': opacity
                            });
                        },
                        duration: 600
                    });
                });

                $('.radio-group .radio').click(function () {
                    $(this).parent().find('.radio').removeClass('selected');
                    $(this).addClass('selected');
                });

                $(".submit").click(function () {
                    return false;
                })

            });


            $(function () {
                let deleteButtonTrans = '{{ trans('
        global.datatables.delete ') }}'
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.users.massDestroy') }}",
                    className: 'btn-danger',
                    action: function (e, dt, node, config) {
                        var ids = $.map(dt.rows({
                            selected: true
                        }).nodes(), function (entry) {
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
                                .done(function () {
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

    @endsection
@endsection
