@extends('layouts.admin')
@section('content')
<div class="sl-mainpanel">
      <nav class="breadcrumb sl-breadcrumb">
        <a class="breadcrumb-item" href="{{url('/home')}}">Dashboard</a>
        <a class="breadcrumb-item" href="{{url('tips/institutions')}}">Institutions</a>
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
              {!! Form::open(['url' => 'tips/institutions', 'files' => false, 'role' => 'form']) !!}
                  {!! csrf_field() !!}
				     <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
                 <div class="row">
                    <div class="form-group col-6">
                   <label for="name" class="d-block">FSP NAME <span class="text-danger">*</span></label>
                    {!! Form::text('name',null,['class'=>'form-control', 'placeholder'=>'FSP Name', 'required' => 'required']) !!}
                    </div>
                    <div class="form-group col-6">
                      <label for="name" class="d-block">FSP ID <span class="text-danger">*</span></label>
                    {!! Form::text('fspId',null,['class'=>'form-control', 'placeholder'=>'FSP ID']) !!}
                    </div>
                  </div>
				 <div class="row">
				  <div class="form-group col-6">
                     <label for="name" class="d-block">FSP SWIFT CODE</label>
                    {!! Form::text('swift_code',null,['class'=>'form-control', 'placeholder'=>'FSP SWIFT CODE']) !!}
                    </div>
                    <div class="form-group col-6">
                     <label for="name" class="d-block">FSP BIN <span class="text-danger">*</span></label>
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
                     <label for="name" class="d-block">FSP TYPE <span class="text-danger">*</span></label>
                    {!! Form::select('fsp_type',['BANK'=>'BANK','MNO'=>'MNO'],'', ['class'=>'form-control', 'placeholder'=>'--SELECT FSP TYPE--', 'required' => 'required']) !!}
                    </div>
					<div class="form-group col-6">
                     <label for="name" class="d-block">IS SOURCE FSP <span class="text-danger">*</span></label>
                    {!! Form::select('is_source',['1'=>'YES','0'=>'NO'],'', ['class'=>'form-control', 'placeholder'=>'--SET AS SOURCE FSP--', 'required' => 'required']) !!}
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
