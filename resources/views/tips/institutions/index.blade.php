@extends('layouts.admin')
@section('title', 'institutions')
@section('content')
<div class="sl-mainpanel">
      <nav class="breadcrumb sl-breadcrumb">
        <a class="breadcrumb-item" href="{{url('/home')}}">Dashboard</a>
        <a class="breadcrumb-item" href="{{url('tips/institutions')}}">Institutions</a>
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
                        <a href="{{url('tips/institutions/create')}}" class="btn btn-sm btn-primary">Add Institution</a>
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
                  <div class="card-body p-1">
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
                    <div class="table-responsive">
                      <table class="table table-striped">
                        <thead>
                           <tr>
                          <th>INSTITUTION NAME</th>
                          <th>INSTITUTION FSPID</th>
						  <th>INSTITUTION SWIFTCODE</th>
                          <th>INSTITUTION BIN </th>
						  <th>INSTITUTION TYPE</th>
						  <th>INSTITUTION STATUS</th>
                          <th>PRIMARY COLOR </th>
						  <th>SECONDARY COLOR</th>
						  <th>IS SOURCE FSP</th>
						  <!--<th>LOGO</th>-->
                          <th width="15%">Action</th>
                        </tr>
                        </thead>
                       <tbody id="tableData">
                        @foreach($institutions as $institution)
                         <tr>
                          <td>{{$institution->name}}</td>
						  <td>{{$institution->fspId	}}</td>
						  <td>{{$institution->swift_code}}</td>
                          <td>{{$institution->fsp_bin}}</td>
                          <td>{{$institution->fsp_type}}</td>
						  <td>
						  @if($institution->status == 0)
						  <span class="badge badge-success">ACTIVE</span>
						 @else
						<span class="badge badge-danger">INACTIVE</span>
						@endif
						  </td>
                          <td>{{$institution->primary_color}}</td>
						  <td>{{$institution->secondary_color}}</td>
						  <td>
						  @if($institution->is_source == 1)
						  <span class="badge badge-success">YES</span>
						 @else
						<span class="badge badge-danger">NO</span>
						@endif
						  </td>
						 <!-- <td>{{$institution->logo}}</td>-->
                          <td class="d-flex flex-row justify-content-between">
                              <div>

                            <a href="{{ url('tips/institutions/'. $institution->id .'/edit')}}" class="btn btn-primary btn-sm">Edit</a>
                           <!--  <a href= "{{ asset('institutions/'.$institution->id.'/destroy') }}"class="btn btn-danger btn-sm">Delete</a>-->
                            {!! Form::open(['url' => 'tips/institutions/'. $institution->id, 'role' => 'form']) !!}
                                              {!! csrf_field() !!}
                              </div>

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
                  </div>
                </div>
              </div>
            </div>

    </div><!-- sl-pagebody -->
@endsection
