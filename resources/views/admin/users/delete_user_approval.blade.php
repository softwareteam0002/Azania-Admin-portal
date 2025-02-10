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
            @include('error_handler')
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
            <h6> Delete System User Approval form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ route('admin.users.deleteActApproval', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="name">Full Name:<span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ $user->name }}" class="form-control form-control-sm" readonly>
                        </div>
                    </div>


                    <div class="col">
                        <div class="form-group">
                            <label for="name">Email:<span class="text-danger">*</span></label>
                            <input type="text" name="email" value="{{ $user->email }}" class="form-control form-control-sm" readonly>
                        </div>
                    </div>

                    <input type="text" name="id" value="{{Crypt::encrypt($user->id) }}" class="form-control form-control-sm" hidden>

                </div>


                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="types">Select Level:<span class="text-danger">*</span></label>
                            <select name="action_id" class="form-control form-control-sm">
                                @foreach($actions as $action)
                                    <option value="{{ $action->id ?? ''}}" @if($action->id == $user->action_id) selected @endif>{{ $action->action_type ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                    <div class="form-group">
                        <label> Roles:<span class="text-danger">*</span></label>
                        <br />

                         @foreach($roles as $role)
                            <div class="form-check form-check-inline">
                                <input type="checkbox" class="form-check-input" name="roles[]"
                                       id="roles{{ $role->id }}" value="{{ $role->id }}" @if(in_array($role->id, $user_roles)) checked @endif>&nbsp; {{$role->title}}
                            </div>
                        @endforeach
                    </div>
                </div>

                    <div class="col-md-4">

                    </div>
                </div>
                    <div class="col-md-12">
                        <hr/>
                        <input type="submit" name="approve" value="approve" class="btn btn-success btn-sm" value="Approve" />
                        <input type="submit" name="reject" value="reject" class="btn btn-primary btn-sm" value="Reject" />
                        <a href="{{ url('admin/users') }}" class="btn btn-link btn-sm"><i class="fas fa-arrow-left"></i> Go back</a>
                    </div>

                </div>

            </form>
        </div>
    </div>


    @endsection
