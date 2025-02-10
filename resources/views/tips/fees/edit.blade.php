@extends('layouts.admin')
@section('title', 'Fees | Edit')
@section('content')
<div class="sl-mainpanel">
      <nav class="breadcrumb sl-breadcrumb">
        <a class="breadcrumb-item" href="{{url('/home')}}">Dashboard</a>
        <a class="breadcrumb-item" href="{{url('tips/fees')}}">Fees</a>
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
                   {!! Form::model($fee, [
            'method' => 'PATCH',
            'url' => ['tips/fees', $fee->id],
            'role' => 'form'
        ]) !!}
                   <div class="row">
				    <div class="form-group col-6">
                      {!! Form::label('transfer_type', 'TRANSFER TYPE', array('class' => 'd-block')) !!}
                    {!! Form::select('transfer_type',['IFT'=>'INTRABANK TRANSFER','IBT'=>'INTERBANK TRANSFER','B2W'=>'BANK TO WALLET'],$fee->transfer_type, ['class'=>'form-control', 'placeholder'=>'--SELECT TRANSFER TYPE--', 'required' => 'required']) !!}
                    </div>
				 <div class="form-group col-6">
                      {!! Form::label('fee_type', 'FEE TYPE', array('class' => 'd-block')) !!}
                    {!! Form::select('fee_type',['INTERVAL'=>'INTERVAL','FIXED'=>'FIXED','PERCENTAGE'=>'PERCENTAGE'],$fee->fee_type, ['class'=>'form-control', 'placeholder'=>'--SELECT FEE TYPE--', 'required' => 'required']) !!}
                    </div>
                  </div>
				 <div class="row">
				 <div class="form-group col-6">
                      {!! Form::label('minimum', 'MINIMUM', array('class' => 'd-block')) !!}
                    {!! Form::text('minimum',null,['class'=>'form-control', 'placeholder'=>'Minimum', 'required' => 'required']) !!}
                    </div>
				   <div class="form-group col-6">
                      {!! Form::label('maximum', 'MAXIMUM', array('class' => 'd-block')) !!}
                    {!! Form::text('maximum',null,['class'=>'form-control', 'placeholder'=>'Maximum', 'required' => 'required']) !!}
                    </div>
                  </div>
				  <div class="form-group col-6">
                      {!! Form::label('fee', 'FEE', array('class' => 'd-block')) !!}
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

    </div><!-- sl-pagebody -->
@endsection
