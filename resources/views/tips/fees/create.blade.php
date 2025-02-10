@extends('layouts.admin')
@section('content')
<div class="sl-mainpanel">
      <nav class="breadcrumb sl-breadcrumb">
        <a class="breadcrumb-item" href="{{url('/home')}}">Dashboard</a>
        <a class="breadcrumb-item" href="{{url('tips/fees/create')}}">Fees</a>
        <span class="breadcrumb-item active">Create</span>
      </nav>

<div class="sl-pagebody">
<div class="mt-12">
        <div class="row">
          <div class="col-12 col-sm-10 col-md-12">
            <div class="card card-primary">
              <div class="card-header">
              <a href="{{url('tips/fees')}}" class="btn btn-sm btn-primary">View Fees</a></div>
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
              {!! Form::open(['url' => 'fees', 'files' => false, 'role' => 'form']) !!}
                  {!! csrf_field() !!}
				      <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
                 <div class="row">
				 <div class="form-group col-6">
                       <label for="name" class="d-block">TRANSFER TYPE <span class="text-danger">*</span></label>
                    {!! Form::select('transfer_type',['IFT'=>'INTRABANK TRANSFER','IBT'=>'INTERBANK TRANSFER','B2W'=>'BANK TO WALLET'],'', ['class'=>'form-control', 'placeholder'=>'--SELECT TRANSFER TYPE--', 'required' => 'required']) !!}
                    </div>
				 <div class="form-group col-6">
                       <label for="name" class="d-block">FEE TYPE <span class="text-danger">*</span></label>
                    {!! Form::select('fee_type',['INTERVAL'=>'INTERVAL','FIXED'=>'FIXED','PERCENTAGE'=>'PERCENTAGE'],'', ['class'=>'form-control', 'placeholder'=>'--SELECT FEE TYPE--', 'required' => 'required']) !!}
                    </div>
                  </div>
				 <div class="row">
				  <div class="form-group col-6">
                      <label for="name" class="d-block">MINIMUM <span class="text-danger">*</span></label>
                    {!! Form::text('minimum',null,['class'=>'form-control', 'placeholder'=>'Minimum', 'required' => 'required']) !!}
                    </div>
				   <div class="form-group col-6">
                      <label for="name" class="d-block">MAXIMUM <span class="text-danger">*</span></label>
                    {!! Form::text('maximum',null,['class'=>'form-control', 'placeholder'=>'Maximum', 'required' => 'required']) !!}
                    </div>
                  </div>
				   <div class="form-group col-6">
                     <label for="name" class="d-block">FEE <span class="text-danger">*</span></label>
                    {!! Form::text('fee',null,['class'=>'form-control', 'placeholder'=>'Fee']) !!}
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
