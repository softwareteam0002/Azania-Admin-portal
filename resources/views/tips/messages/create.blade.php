@extends('layouts.admin')
@section('content')
<div class="sl-mainpanel">
      <nav class="breadcrumb sl-breadcrumb">
        <a class="breadcrumb-item" href="{{url('/home')}}">Dashboard</a>
        <a class="breadcrumb-item" href="{{url('/tips/messages')}}">Messages</a>
        <span class="breadcrumb-item active">Create</span>
      </nav>

<div class="sl-pagebody">
<div class="mt-12">
        <div class="row">
          <div class="col-12 col-sm-10 col-md-12">
            <div class="card card-primary">
              <div class="card-header">
              <a href="{{url('messages')}}" class="btn btn-sm btn-primary">View Messages</a></div>
              <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
              {!! Form::open(['url' => 'messages', 'files' => true, 'role' => 'form']) !!}
			   <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
                 <div class="row">
                     <div class="form-group col-6">
					  <label for="name" class="d-block">RECIPIENT FSP <span class="text-danger">*</span></label>
					{!! Form::select('recipients_fspId',$fsps, '', ['class'=>'form-control', 'placeholder'=>'--SELECT RECIPIENT FSP--']) !!}
                    </div>
					 <div class="form-group col-6">
					  <label for="name" class="d-block">RECIPIENT FULL NAME <span class="text-danger">*</span></label>
                    {!! Form::text('recipients_user',null,['class'=>'form-control', 'placeholder'=>'Recipient Full Name', 'required' => 'required']) !!}
                    </div>

                  </div>
				 <div class="row">
                   	<div class="form-group col-6">
                      <label for="name" class="d-block">NOTIFICATION TYPE <span class="text-danger">*</span></label>
                    {!! Form::select('notificationType',['COMMUNICATION_MESSAGE'=>'COMMUNICATION_MESSAGE','NOTIFICATION_MESSAGE'=>'NOTIFICATION_MESSAGE'],'COMMUNICATION_MESSAGE', ['class'=>'form-control', 'placeholder'=>'--SELECT NOTIFICATION TYPE--', 'required' => 'required']) !!}
                    </div>
					<div class="form-group col-6">
                      <label for="name" class="d-block">FLAG <span class="text-danger">*</span></label>
                    {!! Form::select('flag',['OUTGOING'=>'OUTGOING','INCOMING'=>'INCOMING'],'OUTGOING', ['class'=>'form-control', 'placeholder'=>'--SELECT TYPE IDENTITY--', 'required' => 'required', 'disabled']) !!}
                    </div>
                  </div>
				    <div class="form-group">
                       <label for="name" class="d-block">SUBJECT <span class="text-danger">*</span></label>
                    {!! Form::text('subject',null,['class'=>'form-control', 'placeholder'=>'Subject']) !!}
                    </div>
				    <div class="form-group ">
                      <label for="name" class="d-block">BODY <span class="text-danger">*</span></label>
					 {!! Form::textarea('body',null,['class'=>'message-body form-control', 'placeholder'=>'Body', 'rows'=>3, 'id'=>'message-body']) !!}
                    </div>

                  <div class="form-group">
                     {!! Form::submit('Save', ['class'=>'btn btn-primary btn-block']) !!}
                  </div>
                  {!! Form::close() !!}
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
@endsection
