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
            <li class="breadcrumb-item active">Billers</li>
        </ol>
    </nav>

    <h5>Billers</h5>
    <hr/>


    <div class="card mb-5">
        <div class="card-body">
            <h6>Create Biller form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ url('agency/view_biller') }}" method="POST">
                @csrf
                @method('POST')
                <div class="row">

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="name">Biller name:<span class="text-danger">*</span></label>
                            <input type="text" name="biller_short_name" class="form-control form-control-sm"
                                   placeholder="Enter biller name here.">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Biller description:</label>
                            <textarea name="biller_description" id="biller_description" cols="30" rows="1"
                                      class="form-control form-control-sm"
                                      placeholder="Enter the biller description."></textarea>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="biller-status">Biller group:<span class="text-danger">*</span></label>
                            <select name="biller_group" id="biller_group" class="form-control form-control-sm">
                                @foreach($billergroups as $billergroup)
                                    <option
                                        value="{{ $billergroup->biller_group_name }}">{{ $billergroup->biller_group_name
                                         }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="biller-status">Utility code:<span class="text-danger">*</span></label>
                            <input type="text" name="utility_code" class="form-control form-control-sm" maxlength="2"
                                   id="pin" placeholder="Enter the biller utility code here.">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="biller-status">Institution name:<span class="text-danger">*</span></label>
                            <input type="text" name="biller_institution_name" class="form-control form-control-sm"
                                   placeholder="Enter the biller institution name here.">
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
    </div>


    <h5>All Billers</h5>
    <hr/>
    <div class="card">
        <div class="card-body">
            <div class=" table-responsive-sm">
                <table class="table table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th>Biller name</th>
                        <th>Biller description</th>
                        <th>Biller status</th>
                        <th>Initiator</th>
                        <th>Approver</th>
                        <th>Approval Status</th>
                        <th>Action</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($billers as $biller)
                        <tr>
                            <td>{{$biller->biller_short_name ?? ''}}</td>
                            <td>{{$biller->biller_description ?? ''}}</td>
                            @if($biller->biller_status == 1)
                                <td><span class="badge badge-success">Active</span></td>
                            @else
                                <td><span class="badge badge-danger">Inactive</span></td>
                            @endif

                            <td>{{$biller->initiator->name ?? '--'}}</td>
                            <td>{{$biller->approver->name ?? '--'}}</td>
                            <td>
                                @if($biller->isWaitingApproval == 0)
                                    <span class="badge badge-success">Approved</span>
                                @elseif($biller->isWaitingApproval == 2)
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-warning">Pending Approval</span>
                                @endif
                            </td>
                            <td>
                                @if($biller->biller_status != 1)
                                    <a title="Activate Bank" href="{{url('agency/biller/change_status/'.encrypt
                                    ($biller->id))}}"
                                       class="btn btn-sm btn-success">
                                        <span><i class="fas fa-unlock text-dark-pastel-green"></i></span>
                                    </a>
                                @elseif($biller->biller_status == 1)
                                    <a title="Deactivate Bank" href="{{url('agency/biller/change_status/'.encrypt
                                    ($biller->id))}}" class="btn btn-sm btn-danger">
                                        <span><i class="fas fa-lock text-dark-pastel-green"></i></span>
                                    </a>
                                @endif
                                @if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)
                                    <form action="{{ url('agency/edit_biller') }}" method="POST" class='d-inline'>
                                        @csrf
                                        <input type="hidden" value="{{ $biller->id }}" name="id">
                                        <button type="submit" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit text-dark-pastel-green"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($biller->initiator_id != Auth::user()->id && $biller->isWaitingApproval==1 && (Auth::user()->action_id == 2 || Auth::user()->action_id == 3))
                                    <a href="{{ route('agency.view_biller.approve', $biller->id) }}"
                                       type="submit" name="submit_value" value="approve" class="btn btn-success btn-sm">
                                        <span><i class="fas fa-check text-dark-pastel-green"></i></span>
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
