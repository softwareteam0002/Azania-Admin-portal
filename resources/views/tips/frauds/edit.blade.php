@extends('layouts.admin')
@section('title', 'Frauds')
@section('content')
<div class="sl-mainpanel">
      <nav class="breadcrumb sl-breadcrumb">
        <a class="breadcrumb-item" href="{{url('/home')}}">Dashboard</a>
        <a class="breadcrumb-item" href="{{url('tips/frauds')}}">Frauds</a>
        <span class="breadcrumb-item active">Edit</span>
      </nav>

<div class="sl-pagebody">
<!-- <div class="sl-page-title">
</div> -->
<div class="mt-12">
        <div class="row">
          <div class="col-12 col-sm-10 col-md-12">
            <div class="card card-primary">
              <div class="card-header">
              <a href="{{url('tips/frauds')}}" class="btn btn-sm btn-primary">View Frauds</a></div>
              <div class="card-body">
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
                   {!! Form::model($fraud, [
            'method' => 'PATCH',
            'url' => ['tips/frauds', $fraud->id],
            'role' => 'form'
        ]) !!}
                  <div class="row">
                    <div class="form-group col-6">
                      {!! Form::label('fullName', 'FULL NAME', array('class' => 'd-block')) !!}
                    {!! Form::text('fullName',null,['class'=>'form-control', 'placeholder'=>'Full Name', 'required' => 'required', 'readonly']) !!}
                    </div>
                    <div class="form-group col-6">
                      {!! Form::label('fspId', 'FSP', array('class' => 'd-block')) !!}
					{!! Form::select('fspId',$fsps, $fraud->fspId, ['class'=>'form-control', 'placeholder'=>'--SELECT FSP--', 'readonly']) !!}
                    </div>
                  </div>
				 <div class="row">
					 <div class="form-group col-6">
                      {!! Form::label('identifierType', 'IDENTIFIER TYPE', array('class' => 'd-block')) !!}
                    {!! Form::select('identifierType',['BANK'=>'BANK','MNO'=>'MNO'],$fraud->identifierType, ['class'=>'form-control', 'placeholder'=>'--SELECT fraud TYPE--', 'required' => 'required', 'readonly']) !!}
                    </div>
                    <div class="form-group col-6">
                      {!! Form::label('identifier', 'IDENTIFIER', array('class' => 'd-block')) !!}
                    {!! Form::text('identifier',null,['class'=>'form-control', 'placeholder'=>'Identifier', 'readonly']) !!}
                    </div>
                  </div>
				  <div class="row">
					 <div class="form-group col-6">
                      {!! Form::label('identityType', 'IDENTITY TYPE', array('class' => 'd-block')) !!}
                    {!! Form::select('identityType',['NIN'=>'NIN','TIN'=>'TIN'],$fraud->identityType, ['class'=>'form-control', 'placeholder'=>'--SELECT TYPE IDENTITY--', 'required' => 'required', 'readonly']) !!}
                    </div>
                    <div class="form-group col-6">
                      {!! Form::label('identityValue', 'IDENTITY VALUE', array('class' => 'd-block')) !!}
                    {!! Form::text('identityValue',null,['class'=>'form-control', 'placeholder'=>'Identity Value', 'readonly']) !!}
                    </div>
                  </div>
				   <div class="row">
				    <div class="form-group col-6">
                      {!! Form::label('reasons', 'REASONS', array('class' => 'd-block')) !!}
					 {!! Form::textarea('reasons',null,['class'=>'form-control', 'placeholder'=>'Reasons', 'rows'=>1]) !!}
                    </div>
					 <div class="form-group col-6">
                      {!! Form::label('status', 'STATUS', array('class' => 'd-block')) !!}
                    {!! Form::select('status',['WHITELISTED'=>'WHITELISTED','BLACKLISTED'=>'BLACKLISTED','FLAGED'=>'FLAGED'],$fraud->status, ['class'=>'form-control', 'placeholder'=>'--SELECT STATUS--', 'required' => 'required']) !!}
                    </div>
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

    </div><!-- sl-pagebody -->
@endsection
