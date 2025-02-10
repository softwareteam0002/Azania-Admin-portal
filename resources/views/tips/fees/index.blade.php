@extends('layouts.admin')
@section('title', 'Fees')
@section('content')
<div class="sl-mainpanel">
      <nav class="breadcrumb sl-breadcrumb">
        <a class="breadcrumb-item" href="{{url('/home')}}">Dashboard</a>
        <a class="breadcrumb-item" href="{{url('tips/fees')}}">Fees</a>
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
                        <a href="{{url('tips/fees/create')}}" class="btn btn-sm btn-primary">Add Fee</a>
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
						  <th>TRANSFER TYPE</th>
                          <th>FEE TYPE</th>
                          <th>MINIMUM</th>
						  <th>MAXIMUM</th>
                          <th>FEE</th>
						  <th>STATUS</th>
                          <th width="15%">Action</th>
                        </tr>
                        </thead>
                       <tbody id="tableData">
                        @foreach($fees as $fee)
                         <tr>
						 <td>{{$fee->transfer_type}}</td>
                          <td>{{$fee->fee_type}}</td>
                          <td>{{$fee->minimum}}</td>
                          <td>{{$fee->maximum}}</td>
						  <td>{{$fee->fee}}</td>
                          <td>
						  @if($fee->status == 0)
						  <span class="badge badge-success">ACTIVE</span>
						 @else
						<span class="badge badge-danger">INACTIVE</span>
						@endif
						  </td>
                          <td class="d-flex flex-row justify-content-between">
                              <div>
                            <a href="{{ url('tips/fees/'. $fee->id .'/edit')}}" class="btn btn-primary btn-sm">Edit</a>
                           <!--  <a href= "{{ asset('fees/'.$fee->id.'/destroy') }}"class="btn btn-danger btn-sm">Delete</a>-->
                            {!! Form::open(['url' => 'tips/fees/'. $fee->id, 'role' => 'form']) !!}
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
