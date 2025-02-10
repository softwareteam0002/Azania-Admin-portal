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
    <hr />
</div>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb small" style="background-color:#f9f9f9; padding: .55rem 1rem;">
        <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
        <li class="breadcrumb-item active">GEPG Institutions</li>
    </ol>
</nav>

<h5>GEPG Institutions</h5>
<hr />
<div class="card mb-5">
    <div class="card-body">
        <h6>Create GEPG Institution form</h6>
        <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
        <form action="{{ url('agency/view_gepg_institution') }}" method="POST">
            @csrf
            @method('POST')
            <div class="row">

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">GEPG Institution name:<span class="text-danger">*</span></label>
                        <input type="text" name="institution_name" class="form-control form-control-sm" placeholder="GEPG Institution name here.">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Institution code:<span class="text-danger">*</span></label>
                        <input type="text" name="institution_code" class="form-control form-control-sm" placeholder="Enter Institution code here.">
                    </div>
                </div>
				<!--<div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Institution charges:</label>
                        <input type="text" name="institution_charges" class="form-control form-control-sm" placeholder="Enter Institution here.">
                    </div>
                </div>-->

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Collection account:</label>
                        <input type="text" name="collection_account" class="form-control form-control-sm" placeholder="Enter Collection account here.">
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
</div>



<h5>All GEPG Institutions</h5>
<hr />
<div class="card">
    <div class="card-body">
        <div class=" table-responsive-sm">
            <table class="table table-striped table-sm table-hover datatable">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>GEPG Institution name</th>
                        <th>Institution code</th>
			            <!--<th>Institution charges</th>-->
			             <th>Collection account</th>
                       <!--  <th>Status</th> -->
                        <th>Initiator</th>
                        <th>Approver</th>
                        <th>Approval Status</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($gepgInstutions as $gepgInstution)
                    <tr>
                        <td></td>
                        <td>{{$gepgInstution->institution_name ?? ''}}</td>
                        <td>{{$gepgInstution->institution_code ?? ''}}</td>
			           <!--<td>{{$gepgInstution->institution_charges ?? ''}}</td>-->
                        <td>{{$gepgInstution->collection_account ?? ''}}</td>
                        <!-- <td>{{$gepgInstution->gepgStatus->status ?? 'Not Defined'}}</td> -->
                        <td>{{$gepgInstution->initiator->name ?? '--'}}</td>
                        <td>{{$gepgInstution->approver->name ?? '--'}}</td>
                         <td>
                                @if($gepgInstution->isWaitingApproval == 0)
                                    <span class="badge badge-success">Approved</span>
                                @elseif($gepgInstution->isWaitingApproval == 2)
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-danger">Pending Approval</span>
                                @endif
                            </td>
                        <td>
                           @if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)
                            <a title="Edit Institution" href="{{ url('agency/view_gepg_institution/edit/'. $gepgInstution->institution_id ) }}" class="btn btn-sm btn-primary">
                                <span><i class="fas fa-edit text-dark-pastel-green"></i></span>
                            </a>
                            @endif
                            @if($gepgInstution->initiator_id != Auth::user()->id && $gepgInstution->isWaitingApproval==1 && (Auth::user()->action_id == 2 || Auth::user()->action_id == 3))
                            <a  href="{{ route('agency.view_gepg_institution.approve', $gepgInstution->institution_id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-success btn-sm">
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
    $(function() {
        $('.datatable').DataTable()
    });
</script>
@endsection
@endsection