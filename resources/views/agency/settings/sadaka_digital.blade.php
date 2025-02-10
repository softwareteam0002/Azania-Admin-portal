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
        <li class="breadcrumb-item active">Sadaka Digitals</li>
    </ol>
</nav>

<h5>Sadaka Digital</h5>
<hr />
<div class="card mb-5">
    <div class="card-body">
        <h6>Create Sadaka Digital form</h6>
        <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
        <form action="{{ url('agency/view_sadaka_digital') }}" method="POST">
            @csrf
            @method('POST')
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Account Number:<span class="text-danger">*</span></label>
                        <input type="text" name="account_number" class="form-control form-control-sm" placeholder="Enter Account Number here.">
                    </div>
                </div>
		<div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Sadaka Digital charges:</label>
                        <input type="text" name="charge" class="form-control form-control-sm" placeholder="Enter Sadaka Digital here.">
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



<h5>Sadaka Digital Detals</h5>
<hr />
<div class="card">
    <div class="card-body">
        <div class=" table-responsive-sm">
            <table class="table table-striped table-sm table-hover datatable">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>Account Number</th>
                        <th>Charge</th>
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
                        <td>{{$gepgInstution->account_number ?? ''}}</td>
                        <td>{{$gepgInstution->charge ?? ''}}</td>
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
                            <a title="Edit Sadaka Digital" href="{{ url('agency/view_sadaka_digital/edit/'. $gepgInstution->id ) }}" class="btn btn-sm btn-primary">
                                <span><i class="fas fa-edit text-dark-pastel-green"></i></span>
                            </a>
                            @endif
                            @if($gepgInstution->initiator_id != Auth::user()->id && $gepgInstution->isWaitingApproval==1 && (Auth::user()->action_id == 3 || Auth::user()->action_id == 2))
                            <a  href="{{ route('agency.view_sadaka_digital.approve', $gepgInstution->id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-success btn-sm">
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