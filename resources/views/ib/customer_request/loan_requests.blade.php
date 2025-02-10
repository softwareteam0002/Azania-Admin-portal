@extends('layouts.admin')
@section('content')
    <div class="row mb-3">
        <div class="col-md-8">
            <!-- Notifications-->
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

            @if(session('notification') && session('color'))
                <div class="alert alert-{{ session('color') }} alert-dismissible fade show" role="alert">
                    {{ session('notification') }}
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
            <li class="breadcrumb-item active">Loan requests</li>
        </ol>
    </nav>

    <h5>Loan requests</h5>
    <hr/>


    <div class="card">
        <div class="card-body">
            <h6 class="mb-4">All loan requests</h6>
            <div class="table-responsive">
                <table class="table table-striped table-hover datatable">
                    <thead>
                        <tr>
                            <th width="10">
                            </th>
                            <th>National ID</th>
                            <th>Borrower name</th>
                            <th>Loan date</th>
                            <th>Loan type</th>
                            <th>Amount</th>
                            <th>NIN</th>
                            <th>Description</th>
                            <th>Initiator</th>
                            <th>Approver</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($requests as $request)
                        <tr data-entry-id="1" data-toggle="modal" data-target="#largeModal{{$request->id}}" id="loadingModel" data="{{$request->id}}">
                            <td>
                            </td>
                            <td>{{$request->nin}}</td>
                            <td>
                                @if($request->institute_id==null)
                                {{$request->users->name}}
                                @else
                                {{$request->instutions->institute_name}}
                                @endif
                            </td>
                            <td>{{$request->loan_date}}</td>
                            @if($request['type'])
                                <td>{{$request['type']->name}}</td>
                            @else
                                <td>No type identified</td>
                            @endif
                            <td>{{number_format($request->amount,2)}}</td>
                            <td>{{$request->nin}}</td>
                            <td>{{$request->description}}</td>
                            <td>
                                {{ $request->initiators->name ?? '--' }}
                            </td>
                            <td>
                                {{ $request->approvers->name ?? '--' }}
                            </td>
                            @if($request->status_id == 1)
                                <td>
                                    <h5><span class="badge badge-danger">Pending</span></h5>
                                </td>
                            @elseif($request->status_id == 2)
                                <td>
                                    <h5><span class="badge badge-warning">On Progress</span></h5>
                                </td>
                            @elseif($request->status_id == 4)
                                <td>
                                    <h5><span class="badge badge-success">Success</span></h5>
                                </td>
                            @elseif($request->status_id == 5)
                                <td>
                                    <h5><span class="badge badge-danger">Rejected</span></h5>
                                </td>
                            @else
                                <td>
                                    <h5><span class="badge badge-danger">Failed</span></h5>
                                </td>
                            @endif
                            <td>


                                <form action="{{ url('ib/requests/actions') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id" value="{{ $request->id }}">
                                    <input type="hidden" name="request_type" value="loan_request">

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
