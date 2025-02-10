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
			<li class="breadcrumb-item"><a href="{{ url('') }}">Internet Banking</a></li>
            <li class="breadcrumb-item active">ATM Card Request</li>
        </ol>
    </nav>
    <h5>ATM Card Request</h5>
    <hr/>
	<div class="card">
		<div class="card-header">
			<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
			  <li class="nav-item">
				<a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pending-card-requests" role="tab" aria-controls="pills-home" aria-selected="true">Pending Card Requests</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#approved-card-requests" role="tab" aria-controls="pills-profile" aria-selected="false">Approved Card Requests</a>
			  </li>
			</ul>
			<div class="tab-content" id="pills-tabContent">
			<div class="card-body table-responsive tab-pane fade show active" id="pending-card-requests" role="tabpanel" aria-labelledby="pills-home-tab">
            
                <table class="table table-striped table-hover datatable">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Account ID</th>
                            <th>Name on Card</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Requested Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
					@foreach ($card_requests as $index => $card_request)
					@if ($card_request->status_id == 1)
                        <tr>
                            <td>{{$index+1}}</td>
                            <td>{{$card_request->accountID}}</td>
                            <td>{{$card_request->name_on_card}}</td>
                            <td>{{$card_request->description}}</td>
                            <td><span class="badge badge-warning">Pending</span></td>
                            <td>{{$card_request->created_at}}</td>
							
							<td>
                                <a class="btn btn-success" title="Approve" href="{{url('ib/card-request/approve',[$card_request->id])}}"><b><i class="fas fa-check"></i></b></a>
								<a class="btn btn-danger" title="Reject" href="{{url('ib/card-request/reject',[$card_request->id])}}"><b><i class="fas fa-times"></i></b></a>
                            </td>
							
                        </tr>
                   
                    </tbody>
					@endif
					@endforeach
                </table>
			</div>
			<div class="tab-pane fade table-responsive" id="approved-card-requests" role="tabpanel" aria-labelledby="pills-profile-tab">
            
                <table class="table table-striped table-hover datatable">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Account ID</th>
                            <th>Name on Card</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Requested Date</th>
                            <th>Approved Date</th>
                            <th>Approved By</th>
                            
                        </tr>
                    </thead>
                    <tbody>
					@foreach ($card_requests as $index => $card_request)
					@if ($card_request->updated_at != null)
                        <tr>
                            <td>{{$index+1}}</td>
                            <td>{{$card_request->accountID}}</td>
                            <td>{{$card_request->name_on_card}}</td>
                            <td>{{$card_request->description}}</td>
							<td><span class="badge badge-success">Success</span></td>
                            <td>{{$card_request->created_at}}</td>
                            <td>{{$card_request->updated_at}}</td>
							<td>{{$card_request->approver_id}}</td>
                        </tr>
                    </tbody>
					@endif
					@endforeach
                </table>
			</div>
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
