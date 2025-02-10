@extends('layouts.admin')
@section('title', 'Messages')
@section('content')
<div class="sl-mainpanel">
      <nav class="breadcrumb sl-breadcrumb">
        <a class="breadcrumb-item" href="{{url('/home')}}">Dashboard</a>
        <a class="breadcrumb-item" href="{{url('/tips/messages/create')}}">Messages</a>
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
                        <a href="{{url('tips/messages/create')}}" class="btn btn-sm btn-primary">New Message</a>
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
						  <th>SENDER</th>
						  <th>REFERENCE</th>
						  <th>SENDER FSPID</th>
						  <th>RECIPIENT</th>
						  <th>RECIPIENT FSPID</th>
						  <th>SUBJECT</th>
						  <th>NOTIFICATION_BODY_CONTENTS</th>
						  <th>NOTIFICATION TYPE</th>
						  <th>STATUS</th>
						  <th>FLAG</th>
                         <!-- <th width="15%">Action</th>-->
                        </tr>
                        </thead>
                       <tbody id="tableData">
                        @foreach($messages as $message)
                         <tr>
                          <td>{{$message->sender_user}}</td>
                          <td>{{$message->sender_reference}}</td>
                          <td>{{$message->	sender_fspId}}</td>
						  <td>{{$message->recipients_user}}</td>
						  <td>{{$message->recipients_fspId}}</td>
						  <td>{{$message->subject}}</td>
                          <td>{!! $message->body !!}</td>
						  <td>{{$message->notificationType}}</td>
						  <td>
						   @if($message->status == 1 && $message->flag == 'OUTGOING')
						  <span class="badge badge-success">DELIVERED</span>
						 @elseif($message->status == 2 && $message->flag == 'OUTGOING')
						 <span class="badge badge-danger">FAILED</span>
						 @elseif($message->status == 0 && $message->flag == 'OUTGOING')
						 <span class="badge badge-warning">PENDING</span>
						 @else
						<span class="badge badge-success">RECEIVED</span>
						@endif
						  </td>
						  <td>{{$message->flag}}</td>
                         <!-- <td>
						 <a href="{{ url('messages/'. $message->id)}}" class="btn btn-success btn-sm">View</a>
                            <a href="{{ url('messages/'. $message->id .'/edit')}}" class="btn btn-primary btn-sm">Update message</a>
                            </td>-->
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
