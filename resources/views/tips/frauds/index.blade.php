@extends('layouts.admin')
@section('title', 'Frauds')
@section('content')
<div class="sl-mainpanel">
      <nav class="breadcrumb sl-breadcrumb">
        <a class="breadcrumb-item" href="{{url('/home')}}">Dashboard</a>
        <a class="breadcrumb-item" href="{{url('tips/frauds')}}">Frauds</a>
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
                        <a href="{{url('tips/frauds/create')}}" class="btn btn-sm btn-primary">Add Fraud</a>
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
                    <div class="table-responsive" style="overflow-x:auto;">
                      <table class="table table-striped">
                        <thead>
                           <tr>
						  <th>FULLNAME</th>
						  <th>FSPID</th>
						  <th>IDENTIFIER</th>
						  <th>IDENTIFIER TYPE</th>
						  <th>IDENTITY TYPE</th>
						  <th>IDENTITY VALUE</th>
						  <th>REASONS</th>
						  <th>STATUS</th>
						  <th>FRAUD REGISTER ID</th>
						  <th>PARTICIPANT ID</th>
						  <th>DATE CREATED</th>
						  <th>ERROR CODE</th>
						  <th>ERROR MESSAGE</th>
                          <th width="15%">Action</th>
                        </tr>
                        </thead>
                       <tbody id="tableData">
                        @foreach($frauds as $fraud)
                         <tr>
                          <td>{{$fraud->fullName}}</td>
                          <td>{{$fraud->fspId}}</td>
                          <td>{{$fraud->identifier}}</td>
						  <td>{{$fraud->identifierType}}</td>
						  <td>{{$fraud->identityType}}</td>
						  <td>{{$fraud->identityValue}}</td>
                          <td>{{$fraud->reasons}}</td>
						  <td>
						   @if($fraud->status == 'WHITELISTED')
						  <span class="badge badge-success">{{$fraud->status}}</span>
						 @elseif($fraud->status == 'BLACKLISTED')
						 <span class="badge badge-danger">{{$fraud->status}}</span>
						 @else
						<span class="badge badge-warning">{{$fraud->status}}</span>
						@endif
						  </td>
						  <td>{{$fraud->fraudRegisterId}}</td>
						  <td>{{$fraud->participantId}}</td>
						  <td>{{$fraud->createdDate}}</td>
						  <td>{{$fraud->errorCode}}</td>
						  <td>{{$fraud->errorDescription}}</td>
                          <td>
						 <!-- <a href="{{ url('frauds/'. $fraud->id)}}" class="btn btn-success btn-sm">View</a>-->
                            <a href="{{ url('tips/frauds/'. $fraud->id .'/edit')}}" class="btn btn-primary btn-sm">Update Fraud</a>
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
