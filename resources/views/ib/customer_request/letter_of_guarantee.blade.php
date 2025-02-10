@extends('layouts.admin')
@section('content')
    <div class="row mb-3">
        <div class="col-md-8">
            <!-- Notifications-->

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
            <li class="breadcrumb-item active">Letter of guarantee requests</li>
        </ol>
    </nav>

    <h5>Letter of guarantee requests</h5>
    <hr/>


    <div class="card">
        <div class="card-body">
            <h6 class="mb-4">All Letter of guarantee requests</h6>

            <div class="table-responsive">
                <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                    <thead>
                        <tr>
                            <th width="10"> </th>
                            <th>Customer Name</th>
                            <th>Business Type</th>
                            <th>Location</th>
                            <th>Contract Amount</th>
                            <th>Guarantee Amount</th>
                            <th>Collateral Cover</th>
                            <th>Institution Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Inititator</th>
                            <th>Approver</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)

                            <tr data-entry-id="1" data-toggle="modal" data-target="#largeModal{{$request->id}}" id="loadingModel" data="{{$request->id}}">
                                <td></td>
                                <td>{{$request->name}}</td>
                                <td>{{$request->business_type}}</td>
                                <td>{{$request->location}}</td>
                                <td>{{number_format($request->contract_amount,2)}}</td>
                                <td>{{number_format($request->guarantee_amount,2)}}</td>
                                <td>{{number_format($request->collateral_cover,2)}}</td>
                                <td>{{$request->institutions->institute_name ?? 'N/A' }}</td>
                                <td>{{$request->start_date }}</td>
                                <td>{{$request->end_date}}</td>
                                <td>
                                    {{ $request->initiators->name ?? '--' }}
                                </td>
                                <td>
                                    {{ $request->approvers->name ?? '--' }}
                                </td>
                                <td>

                                        @if($request->status_id == 1)

                                        <h5><span class="badge badge-danger">Pending</span></h5>

                                        @endif
                                        @if($request->status_id == 2)

                                        <h5><span class="badge badge-warning">On Progress</span></h5>

                                        @endif
                                        @if($request->status_id == 4)

                                        <h5><span class="badge badge-success">Success</span></h5>

                                        @endif
                                        @if($request->status_id == 5)

                                        <h5><span class="badge badge-danger">Rejected</span></h5>

                                        @endif
                                </td>
                                <td>
                                    <form action="{{ url('ib/requests/actions') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="id" value="{{ $request->id }}">
                                        <input type="hidden" name="request_type" value="letter_guarantee_request">

                                        <button type="submit" name="status" value="6" title="Show" class="btn btn-primary btn-sm"><span><i class="fas fa-eye text-dark-pastel-green"></i></span></button>

                                        @if(Auth::user()->action_id==1 && $request->status_id == 1)
                                            <button type="submit" name="status" value="2" title="Initiate" class="btn btn-secondary btn-sm"><span><i class="fas fa-check text-dark-pastel-green"></i></span></button>
                                        @endif

                                        @if(Auth::user()->action_id==3 && $request->status_id == 1)
                                            <button type="submit" name="status" value="2" title="Initiate" class="btn btn-secondary btn-sm"><span><i class="fas fa-check text-dark-pastel-green"></i></span></button>
                                        @endif

                                        @if(Auth::user()->action_id==3 && $request->status_id == 2 && Auth::user()->id!=$request->initiator_id)
                                            <button type="submit" name="status" value="4" title="Approve" class="btn btn-success btn-sm"><span><i class="fas fa-check text-dark-pastel-green"></i></span></button>
                                            <button type="submit" name="status" value="5" title="Reject"  class="btn btn-danger btn-sm"><span><i class="fas fa-remove text-dark-pastel-green"></i></span></button>
                                        @endif

                                        @if(Auth::user()->action_id==2 && $request->status_id == 2)
                                            <button type="submit" name="status" value="4" title="Approve" class="btn btn-success btn-sm"><span><i class="fas fa-check text-dark-pastel-green"></i></span></button>
                                            <button type="submit" name="status" value="5" title="Reject"  class="btn btn-danger btn-sm"><span><i class="fas fa-remove text-dark-pastel-green"></i></span></button>
                                        @endif
                                    </form>
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
