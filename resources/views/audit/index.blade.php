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
            <li class="breadcrumb-item active">Audit Trail</li>
        </ol>
    </nav>


    <h5>Audit Trail</h5>
    <hr/>
    <div class="card"> 
        <div class="card-body">
            <h6 class="mb-2">All Activities</h6>
            <div class="table-responsive-sm">
                <table class="table table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th width="10"></th>
                        <th>User Name</th>
                        <th>Module</th>
                        <th>Action</th>
                        <th>Action Time</th>
                        <th>Reason</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($requests as $request)
                        <tr data-entry-id="1" data-toggle="modal" data-target="#largeModal{{$request->id}}" id="loadingModel" data="{{$request->id}}">
                            <td>
                            </td>
                            <td>{{ $request->users->name ?? '--'}}</td>
                            <td>{{ $request->module }}</td>
                            <td>{{ $request->action }}</td>
                            <td>{{ $request->action_time }}</td>
                            <td>{{ $request->reason ?? 'N/A' }}</td>
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
