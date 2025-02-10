@extends('layouts.admin')
@section('content')
    <div class="row mb-3">
        <div class="col-md-8">
            <!-- Notifications-->
            @if(session('notification') && session('color'))
                <div class="alert alert-{{ session('color') }} alert-dismissible fade show" role="alert">
                    {{ session('notification') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif


            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{$error}}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if(Session::has('message'))
                <div class="alert alert-success alert-dismissible">
                    <ul>
                        <li>{{Session::get('message')}}</li>
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(isset($notification) && isset($color))
                <div class="alert alert-{{ $color }} alert-dismissible fade show" role="alert">
                    {{ $notification }}
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
            <li class="breadcrumb-item"><a href="{{ url('ib/user') }}">Manage users</a></li>
            <li class="breadcrumb-item active">Assign user internet banking account</li>
        </ol>
    </nav>


    <h5>Assign user internet banking account</h5>
    <hr/>


    <div class="card">
        <div class="card-body">
            <h6>User internet banking registration form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ url('ib/user/add_account') }}" method="POST" >
                @csrf
                @method('POST')

                <input type="hidden" name="id" value="{{ $user->id }}">
                <input type="hidden" name="institution_id" value="{{ $user->institution_id }}">
                <input type="hidden" name="aCStatus" value="{{ $user->aCStatus }}">
                <div class="row">
{{--                    <div class="col-md-4">--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="name">User Name:<span class="text-danger">*</span></label>--}}
{{--                            <input type="text" id="account_name" value="{{ $user->name }}" class="form-control form-control-sm" disabled>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="col-md-4">--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="name">Account Name:<span class="text-danger">*</span></label>--}}
{{--                            <input type="text" id="account_name" name="account_name" value="{{ $user->name }}" class="form-control form-control-sm" >--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="col-md-4">--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="types">Account Type:<span class="text-danger">*</span></label>--}}
{{--                            <select name="account_type" class="form-control form-control-sm">--}}
{{--                                <option value="0" >Select Account Type</option>--}}
{{--                                @foreach($types as $type)--}}

{{--                                    <option value="{{ $type->id }}" @if($user->type->id ?? '' == $type->id ?? '')--}}
{{--                                    selected="selected" @endif>{{ $type->name }}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="name">Account Number:<span class="text-danger">*</span></label>
                            <input type="text" id="account_number" name="account_number"  class="form-control form-control-sm" >
                        </div>
                    </div>

{{--                    <input type="hidden" id="balance" name="balance" value="0" class="form-control form-control-sm" >--}}

{{--                    <div class="col-md-4">--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="types">Branch Names</label>--}}
{{--                            <select name="branch_id" class="form-control form-control-sm">--}}
{{--                                <option value="0" >Select Branch</option>--}}
{{--                                @foreach($branchs as $branch)--}}
{{--                                    <option value="{{ $branch->id }}" @if($user->branch->id ?? '' == $branch->id ?? '')--}}
{{--                                    selected="selected" @endif>{{ $branch->branchName }}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                    <div class="col-md-12">
                        <hr/>
                        <button type="submit" class="btn btn-success btn-sm">{{ trans('global.save') }}</button>
                        <a href="{{ url('ib/user') }}" class="btn btn-link btn-sm">Go back</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <h5>Internet Banking accounts</h5>
    <hr/>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th width="10"></th>
                        <th>Account Number</th>
                        <th>Account Name</th>
                        <th>Account Type</th>
                        <th>Branch</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($accounts as $trxn)
                        <tr data-entry-id="{{$trxn->id}}" data-toggle="modal" data-target="#largeModal{{ $trxn->id }}">
                            <td></td>
                            <td>{{$trxn->accountID}}</td>
                            <td>{{$trxn->accountName}}</td>
                            <td>{{$trxn->productName ?? ''}}</td>
                            <td>{{$trxn->branchName ?? ''}}</td>
                            <td>
                                @if($trxn->aCStatus=="Active")
                                    <span class="badge badge-success">{{ $trxn->aCStatus }}</span>
                                    <!-- There is a logical error with the status texts -->
                                @else
                                    <span class="badge badge-danger">{{ $trxn->aCStatus }}</span>
                                @endif
                            </td>
                            <td>
                               <!-- <a class="btn btn-primary btn-sm" href="{{ url('ib/accounts/edit',[$trxn->id]) }}">
                                    <i class="fas fa-edit text-dark-pastel-green"></i> Edit
                                </a>-->
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
        $(document).ready(function() {
            $('.datatable').DataTable();
        } );
    </script>
@endsection

@endsection
