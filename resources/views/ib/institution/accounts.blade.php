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

            <!-- Create Action Notifications -->
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
            <li class="breadcrumb-item"><a href="{{ url('ib/institutions/index') }}">Institutions</a></li>
            <li class="breadcrumb-item active">Institution Accounts</li>
        </ol>
    </nav>


    <h5>Institution Accounts</h5>

    <hr/>
    <div class="card mb-5">
        <div class="card-body">
            <h6>Create Instution Account form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ url('ib/institution/account_store') }}" method="POST">
                @csrf
                @method('POST')
                <input type="hidden" name="institution_id" value="{{ $institution->id }}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Account number:<span class="text-danger">*</span></label>
                            <input type="text" id="account_number" name="account_number" class="form-control form-control-sm" placeholder="Enter account number here.">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Account type:<span class="text-danger">*</span></label>
                            <select name="account_type" class="form-control form-control-sm">
                                @foreach($accounttypes as $accounttype)
                                    <option value="{{ $accounttype->id }}" >{{ $accounttype->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Account branch:<span class="text-danger">*</span></label>
                            <select name="branch_id" class="form-control form-control-sm">
                                @foreach($branchs as $branch)
                                    <option value="{{ $branch->id }}" >{{ $branch->branchName . " - " . $branch->branch_code }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Min Amount:<span class="text-danger">*</span></label>
                            <input type="number" id="min_amount" name="min_amount" class="form-control form-control-sm" placeholder="Minimum Transaction Amount" min="1" value="1" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Max Amount:<span class="text-danger">*</span></label>
                            <input type="number" id="max_amount" name="max_amount" class="form-control form-control-sm" min="1" placeholder="Maxmum Transaction Amount" required>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <hr/>
                        <button type="submit" class="btn btn-success btn-sm">{{ trans('global.save') }}</button>

                    </div>
                </div>
            </form>

{{--            <a href="{{ url('ib/institutions/index') }}" class="btn btn-link btn-sm">Go back</a>--}}
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
                        <th>Transaction Limit</th>
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
                            <td>{{$trxn->branches->branchName ?? ''}}</td>
                            <td>{{$trxn->minAmount ?? ''}} - {{$trxn->maxAmount ?? ''}}</td>
                            <td>
                                @if($trxn->aCStatus=="Active")
                                  <span class="badge badge-success">{{ $trxn->aCStatus }}</span>
                                  <!-- There is a logical error with the status texts -->
                                @else
                                  <span class="badge badge-danger">{{ $trxn->aCStatus }}</span>
                                @endif
                            </td>
                            <td>
							 @if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)
                               <a class="btn btn-primary btn-sm" href="{{ url('ib/accounts/edit',[$trxn->id]) }}">
                                    <i class="fas fa-edit text-dark-pastel-green"></i> Edit
                                </a>
								@endif
                                 <a class="btn btn-info btn-sm" href="{{ url('ib/accounts/auth_matrix/setup',[$trxn->id]) }}">
                                    <i class="fas fa-gear text-dark-pastel-blue"></i> Auth Matrix
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


{{--<!--  Create instution Route: {{ url('ib/institution/create') }} -->--}}

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.datatable').DataTable();
        } );
    </script>
@endsection

@endsection
