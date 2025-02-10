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
        <li class="breadcrumb-item active">Biller Groups</li>
    </ol>
</nav>

<h5>Billers</h5>
<hr />

<!---

    [biller_id]
      ,[biller_short_name]
      ,[biller_description]
      ,[biller_status]
      ,[biller_group]
      ,[utility_code]
      ,[biller_institution_name]

-->

<div class="card mb-5">
    <div class="card-body">
        <h6>Create bille groups form</h6>
        <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
        <form action="{{ url('agency/view_biller_group') }}" method="POST">
            @csrf
            @method('POST')
            <div class="row">

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Biller group name:<span class="text-danger">*</span></label>
                        <input type="text" name="biller_group_name" class="form-control form-control-sm" placeholder="Enter biller name here.">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Biller description:<span class="text-danger">*</span></label>
                        <textarea name="biller_group_description" id="biller_description" cols="30" rows="1" class="form-control form-control-sm" placeholder="Enter the biller description."></textarea>
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



<h5>All biller groups</h5>
<hr />
<div class="card">
    <div class="card-body">
        <div class=" table-responsive-sm">
            <table class="table table-striped table-sm table-hover datatable">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>Biller group name</th>
                        <th>Biller group description</th>
                        <th>Initiator</th>
                        <th>Approver</th>
                        <th>Approval Status</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($biller_groups as $biller_group)
                    <tr>
                        <td></td>
                        <td>{{$biller_group->biller_group_name ?? ''}}</td>
                        <td>{{$biller_group->biller_group_description ?? ''}}</td>
                        <td>{{$biller_group->initiator->name ?? '--'}}</td>
                        <td>{{$biller_group->approver->name ?? '--'}}</td>
                        <td>
                                @if($biller_group->isWaitingApproval == 0)
                                    <span class="badge badge-success">Approved</span>
                                @elseif($biller_group->isWaitingApproval == 2)
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-danger">Pending Approval</span>
                                @endif
                            </td>
                        <td>
                             @if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)
                            <a title="Edit Biller Group" href="{{ url('agency/view_biller_group/edit/'. $biller_group->biller_group_id ) }}" class="btn btn-sm btn-primary">
                                <span><i class="fas fa-edit text-dark-pastel-green"></i></span>
                            </a>
                            @endif
                            @if($biller_group->initiator_id != Auth::user()->id && $biller_group->isWaitingApproval==1 && (Auth::user()->action_id == 2 || Auth::user()->action_id == 3))
                            <a  href="{{ route('agency.view_biller_group.approve', $biller_group->biller_group_id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-success btn-sm">
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