@extends('layouts.admin')
@section('title', 'FSPs')
@section('content')
	@if (session('success'))
		<div class="alert alert-success">
			{{ session('success') }}
		</div>
	@endif
	@if (session('error'))
		<div class="alert alert-danger">
			{{ session('error') }}
		</div>
	@endif
    <div class="sl-mainpanel">
        <nav class="breadcrumb sl-breadcrumb">
            <a class="breadcrumb-item" href="{{url('/home')}}">Dashboard</a>
            <a class="breadcrumb-item" href="{{url('/tips/fsps')}}">FSPs</a>
            <span class="breadcrumb-item active">List</span>
        </nav>

        <div class="sl-pagebody">
            <!-- <div class="sl-page-title">
            </div> -->
            <div class="row">
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="float-left">
                                    <a href="{{url('tips/fsps/create')}}" class="btn btn-sm btn-primary">Add FSP</a>
                                </div>
                            </div>
                            <div class="float-right">
                                <form>
                                    <div class="input-group">
                                        <input type="text" name="search" id="search" class="form-control" placeholder="Search" >

                                        <div class="input-group-btn">
                                            <button class="btn btn-secondary" id="searchBtn"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- <h4>Permissions</h4> -->
                        </div>
                       <!-- <div class="card-body p-1">
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif-->
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>FSP Name</th>
                                        <th>FSP ID</th>
                                        <th>FSP SWIFT CODE</th>
                                        <th>FSP BIN</th>
                                        <th>FSP TYPE</th>
										<th>FSP STATUS</th>
                                        <th width="15%">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="tableData">
                                    @foreach($fsps as $fsp)
                                        <tr>
                                            <td>{{$fsp->name}}</td>
                                            <td>{{$fsp->fspId}}</td>
                                            <td>{{$fsp->swift_code ?? 'N/A'}}</td>
                                            <td>{{$fsp->fsp_bin ?? 'N/A'}}</td>
                                            <td>{{$fsp->fsp_type}}</td>
											<td>
											@if($fsp->status == "0")
											<span class="badge badge-success">Active</span>
											@else($fsp->status == "1")
											<span class="badge badge-danger">Inactive</span>
											@endif
											</td>
                                            <td class="d-flex flex-row justify-content-between">
                                                <div>
                                                <a href="{{ url('tips/fsps/'. $fsp->id .'/edit')}}" class="btn btn-primary btn-sm">Edit</a>
                                                {!! Form::open(['url' => 'tips/fsps/'. $fsp->id, 'role' => 'form']) !!}
                                                {!! csrf_field() !!}
                                                </div>
												@if($fsp->fsp_type == "MNO" && $fsp->status == "0")
												<div>
                                                <a href="{{ url('tips/fsps/activate/'.$fsp->id)}}" class="btn btn-success btn-sm"><i class="fa fa-lock" title="deactivate"></i></a>
                                                {!! Form::open(['url' => 'tips/fsps/activate'. $fsp->id, 'role' => 'form']) !!}
                                                {!! csrf_field() !!}
                                                </div>
												@endif
												@if($fsp->fsp_type == "MNO" && $fsp->status == "1")
												<div>
                                                <a href="{{ url('tips/fsps/deactivate/'.$fsp->id)}}" class="btn btn-warning btn-sm"><i class="fa fa-unlock" title="activate"></i></a>
                                                {!! Form::open(['url' => 'tips/fsps/deactivate'. $fsp->id, 'role' => 'form']) !!}
                                                {!! csrf_field() !!}
                                                </div>
												@endif
                                                <div>
                                                @method('DELETE')
                                                {!! Form::submit('Delete', ['class'=>'btn btn-danger btn-sm']) !!}
                                                {!! Form::close() !!}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
								
                            </div>
							
                        </div>
						
                        <div class="card-footer text-right">
						{{ $fsps->links() }}
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- sl-pagebody -->
@endsection
