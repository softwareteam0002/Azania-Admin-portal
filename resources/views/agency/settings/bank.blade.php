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
            <li class="breadcrumb-item active">Banks</li>
        </ol>
    </nav>

    <h5>Banks</h5>
    <hr/>
    <!--<div class="card mb-5">
    <div class="card-body">
        <h6>Create Bank form</h6>
        <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
        <form action="{{ url('agency/view_bank') }}" method="POST">
            @csrf
    @method('POST')
    <div class="row">

        <div class="col-md-6">
            <div class="form-group">
                <label for="name">Bank name:<span class="text-danger">*</span></label>
                <input type="text" name="bank_name" class="form-control form-control-sm" placeholder="Enter bank name here.">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="name">Bank code:<span class="text-danger">*</span></label>
                <input type="text" name="bank_code" class="form-control form-control-sm" placeholder="Enter bank code here.">
            </div>
        </div>

        <div class="col-md-12">
            <hr />
            <button type="submit" class="btn btn-success btn-sm">Save</button>
            <button type="reset" class="btn btn-link btn-sm">Clear form</button>
        </div>

    </div>


</form>
</div>
</div>-->
    <div class="card mb-5">
        <div class="card-header">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#add-bank" role="tab"
                       aria-controls="pills-home" aria-selected="true">Add Bank</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#upload-banks" role="tab"
                       aria-controls="pills-profile" aria-selected="false">Upload Banks</a>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="card-body  tab-pane fade show active" id="add-bank" role="tabpanel"
                     aria-labelledby="pills-home-tab">
                    <h6>Create Bank form</h6>
                    <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
                    <form action="{{ url('agency/view_bank') }}" method="POST">
                        @csrf
                        @method('POST')
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Bank name:<span class="text-danger">*</span></label>
                                    <input type="text" name="bank_name" class="form-control form-control-sm"
                                           placeholder="Enter bank name here.">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Bank code:<span class="text-danger">*</span></label>
                                    <input type="text" name="bank_code" class="form-control form-control-sm"
                                           placeholder="Enter bank code here.">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <hr/>
                                <button type="submit" class="btn btn-success btn-sm">Save</button>
                                <button type="reset" class="btn btn-link btn-sm">Clear form</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="upload-banks" role="tabpanel" aria-labelledby="pills-profile-tab">
                    <div class="card-body  tab-pane fade show active" id="add-bank" role="tabpanel"
                         aria-labelledby="pills-home-tab">
                        <a href="{{url('agency/download_template')}}" class="btn btn-primary btn-sm mb-4">Download
                            Template</a>
                        <h5>Upload Bank File</h5>
                        <p class="small text-danger">Files accepted are (.xlsx, .xls) only.</p>
                        <form action="{{ url('agency/upload_bank_file') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('POST')
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <input name="file" type="file" class="file" required>
                                </div>
                                <hr/>
                                <div class="col-md-12 mt-2">
                                    <button type="submit" class="btn btn-success btn-md">Upload</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <h5>All banks</h5>
    <hr/>
    <div class="card">
        <div class="card-body">
            <div class=" table-responsive-sm">
                <table class="table table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>Bank name</th>
                        <th>Bank code</th>
                        <th>Status</th>
                        <th>Initiator</th>
                        <th>Approver</th>
                        <th>Approval Status</th>
                        <th>Action</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($banks as $bank)
                        <tr>
                            <td></td>
                            <td>{{$bank->bank_name ?? ''}}</td>
                            <td>{{$bank->bank_code ?? ''}}</td>
                            <td>@if($bank->bank_status == 1)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif</td>
                            <td>{{$bank->initiator->name ?? '--'}}</td>
                            <td>{{$bank->approver->name ?? '--'}}</td>
                            <td>
                                @if($bank->isWaitingApproval == 0)
                                    <span class="badge badge-success">Approved</span>
                                @elseif($bank->isWaitingApproval == 2)
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-warning">Pending Approval</span>
                                @endif
                            </td>
                            <td>
                                @if($bank->bank_status != 1)
                                    <a title="Activate Bank"
                                       href="{{ url('agency/bank/change_status/'. encrypt($bank->bank_id) ) }}"
                                       class="btn btn-sm btn-success">
                                        <span><i class="fas fa-unlock text-dark-pastel-green"></i></span>
                                    </a>
                                @elseif($bank->bank_status == 1)
                                    <a title="Deactivate Bank"
                                       href="{{ url('agency/bank/change_status/'. encrypt($bank->bank_id) ) }}"
                                       class="btn btn-sm btn-danger">
                                        <span><i class="fas fa-lock text-dark-pastel-green"></i></span>
                                    </a>
                                @endif
                                @if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)
                                    <a title="Edit bank" href="{{ url('agency/view_bank/edit/'. $bank->bank_id ) }}"
                                       class="btn btn-sm btn-primary">
                                        <span><i class="fas fa-edit text-dark-pastel-green"></i></span>
                                    </a>
                                @endif
                                @if( $bank->isDeleted == 0)
                                    <form action="{{ route('agency.view_bank.deleteBank', $bank->bank_id) }}"
                                          method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                          class="d-inline">
                                        <input type="hidden" name="_method" value="PUT">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="bank_id" value="{{ $bank->bank_id }}">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @endif
                                @if(Auth::user()->id != $bank->initiator_id && $bank->isWaitingApproval==1 && $bank->isDeleted == 0)
                                    <a href="{{ route('agency.bank.approve', $bank->bank_id) }}" type="submit"
                                       name="submit_value" value="approve" class="btn btn-success btn-sm">
                                        <span><i class="fas fa-check text-dark-pastel-green"></i></span>
                                    </a>
                                @endif
                                @if((auth()->user()->id != $bank->deletedBy_id) && $bank->isDeleted == 1 && $bank->isWaitingApproval == 1)
                                    <a title="Delete"
                                       href="{{ route('agency.view_bank.deleteApproval', $bank->bank_id) }}"
                                       type="submit" name="submit_value" value="approve" class="btn btn-danger btn-sm">
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
            $(function () {
                $('.datatable').DataTable()
            });
        </script>
    @endsection
@endsection
