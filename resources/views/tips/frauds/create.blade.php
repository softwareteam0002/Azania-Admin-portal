@extends('layouts.admin')
@section('content')
<div class="sl-mainpanel">
      <nav class="breadcrumb sl-breadcrumb">
        <a class="breadcrumb-item" href="{{url('/home')}}">Dashboard</a>
        <a class="breadcrumb-item" href="{{url('tips/frauds')}}">Frauds</a>
        <span class="breadcrumb-item active">Create</span>
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
              {!! Form::open(['url' => 'tips/frauds', 'files' => false, 'role' => 'form']) !!}
                  {!! csrf_field() !!}
				   <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
                 <div class="row">
                    <div class="form-group col-6">
                       <label for="name" class="d-block">FULL NAME <span class="text-danger">*</span></label>
                    {!! Form::text('fullName',null,['class'=>'form-control', 'placeholder'=>'Full Name', 'required' => 'required']) !!}
                    </div>
                    <div class="form-group col-6">
                      <label for="name" class="d-block">FSP <span class="text-danger">*</span></label>
					{!! Form::select('fspId',$fsps, '', ['class'=>'form-control', 'placeholder'=>'--SELECT FSP--']) !!}
                    </div>
                  </div>
				 <div class="row">
					 <div class="form-group col-6">
                        <label for="name" class="d-block">IDENTIFIER TYPE <span class="text-danger">*</span></label>
                    {!! Form::select('identifierType',['BANK'=>'BANK','MNO'=>'MNO'],'', ['class'=>'form-control', 'placeholder'=>'--SELECT fraud TYPE--', 'required' => 'required']) !!}
                    </div>
                    <div class="form-group col-6">
                        <label for="name" class="d-block">IDENTIFIER <span class="text-danger">*</span></label>
                    {!! Form::text('identifier',null,['class'=>'form-control', 'placeholder'=>'Identifier']) !!}
                    </div>
                  </div>
				  <div class="row">
					 <div class="form-group col-6">
                       <label for="name" class="d-block">IDENTITY TYPE <span class="text-danger">*</span></label>
                    {!! Form::select('identityType',['NIN'=>'NIN','TIN'=>'TIN'],'', ['class'=>'form-control', 'placeholder'=>'--SELECT TYPE IDENTITY--', 'required' => 'required']) !!}
                    </div>
                    <div class="form-group col-6">
                      <label for="name" class="d-block">IDENTITY VALUE <span class="text-danger">*</span></label>
                    {!! Form::text('identityValue',null,['class'=>'form-control', 'placeholder'=>'Identity Value']) !!}
                    </div>
                  </div>
				   <div class="row">
				    <div class="form-group col-6">
                      {!! Form::label('reasons', 'REASONS', array('class' => 'd-block')) !!}
					 {!! Form::textarea('reasons',null,['class'=>'form-control', 'placeholder'=>'Reasons', 'rows'=>1]) !!}
                    </div>
					 <div class="form-group col-6">
                     <label for="name" class="d-block">STATUS <span class="text-danger">*</span></label>
                    {!! Form::select('status',['BLACKLISTED'=>'BLACKLISTED','FLAGED'=>'FLAGED'],'', ['class'=>'form-control', 'placeholder'=>'--SELECT STATUS--', 'required' => 'required']) !!}
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
