@extends('layouts.admin')
@section('title', 'Institutions')
@section('content')
<div class="sl-mainpanel">
      <nav class="breadcrumb sl-breadcrumb">
        <a class="breadcrumb-item" href="{{url('/home')}}">Dashboard</a>
        <a class="breadcrumb-item" href="{{url('tips/institutions')}}">Institutions</a>
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
              <a href="{{url('tips/institutions')}}" class="btn btn-sm btn-primary">View Institutions</a></div>
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
                   {!! Form::model($institution, [
            'method' => 'PATCH',
            'url' => ['tips/institutions', $institution->id],
            'role' => 'form'
        ]) !!}
                    <div class="row">
                    <div class="form-group col-6">
                      {!! Form::label('name', 'FSP NAME', array('class' => 'd-block')) !!}
                    {!! Form::text('name',null,['class'=>'form-control', 'placeholder'=>'FSP Name', 'required' => 'required']) !!}
                    </div>
                    <div class="form-group col-6">
                      {!! Form::label('fspId', 'FSP ID', array('class' => 'd-block')) !!}
                    {!! Form::text('fspId',null,['class'=>'form-control', 'placeholder'=>'FSP ID']) !!}
                    </div>
                  </div>
				 <div class="row">
				  <div class="form-group col-6">
                      {!! Form::label('swift_code', 'FSP SWIFT CODE', array('class' => 'd-block')) !!}
                    {!! Form::text('swift_code',null,['class'=>'form-control', 'placeholder'=>'FSP SWIFT CODE']) !!}
                    </div>
                    <div class="form-group col-6">
                      {!! Form::label('fsp_bin', 'FSP BIN', array('class' => 'd-block')) !!}
                    {!! Form::text('fsp_bin',null,['class'=>'form-control', 'placeholder'=>'FSP BIN']) !!}
                    </div>

                  </div>
				  <div class="row">
                    <div class="form-group col-6">
                      {!! Form::label('primary_color', 'PRIMARY COLOR', array('class' => 'd-block')) !!}
                    {!! Form::text('primary_color',null,['class'=>'form-control', 'placeholder'=>'Primary Color', 'required' => 'required']) !!}
                    </div>
                    <div class="form-group col-6">
                      {!! Form::label('secondary_color', 'SECONDARY COLOR', array('class' => 'd-block')) !!}
                    {!! Form::text('secondary_color',null,['class'=>'form-control', 'placeholder'=>'Secondary Color']) !!}
                    </div>
					 <div class="form-group col-6">
                      {!! Form::label('fsp_type', 'FSP TYPE', array('class' => 'd-block')) !!}
                    {!! Form::select('fsp_type',['BANK'=>'BANK','MNO'=>'MNO'],$institution->fsp_type, ['class'=>'form-control', 'placeholder'=>'--SELECT FSP TYPE--', 'required' => 'required']) !!}
                    </div>
					<div class="form-group col-6">
                      {!! Form::label('is_source', 'IS SOURCE FSP?', array('class' => 'd-block')) !!}
                    {!! Form::select('is_source',['1'=>'YES','0'=>'NO'],$institution->is_source, ['class'=>'form-control', 'placeholder'=>'--SET AS SOURCE FSP--', 'required' => 'required']) !!}
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
