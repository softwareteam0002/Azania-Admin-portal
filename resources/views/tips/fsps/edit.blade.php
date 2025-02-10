@extends('layouts.admin')
@section('title', 'FSPs | Edit')
@section('content')
    <div class="sl-mainpanel">
        <nav class="breadcrumb sl-breadcrumb">
            <a class="breadcrumb-item" href="{{url('/home')}}">Dashboard</a>
            <a class="breadcrumb-item" href="{{url('/tips/fsps')}}">FSPs</a>
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
                                <a href="{{url('tips/fsps')}}" class="btn btn-sm btn-primary">View FSPs</a></div>
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
                                {!! Form::model($fsp, [
                         'method' => 'PATCH',
                         'url' => ['tips/fsps', $fsp->id],
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
                                        {!! Form::label('fsp_bin', 'FSP BIN', array('class' => 'd-block')) !!}
                                        {!! Form::text('fsp_bin',null,['class'=>'form-control', 'placeholder'=>'FSP BIN']) !!}
                                    </div>
                                    <div class="form-group col-6">
                                        {!! Form::label('swift_code', 'FSP SWIFT CODE', array('class' => 'd-block')) !!}
                                        {!! Form::text('swift_code',null,['class'=>'form-control', 'placeholder'=>'FSP SWIFT CODE']) !!}
                                    </div>
                                    <div class="form-group col-6">
                                        {!! Form::label('fsp_type', 'FSP TYPE', array('class' => 'd-block')) !!}
                                        {!! Form::select('fsp_type',['BANK'=>'BANK','MNO'=>'MNO','WALLET'=>'WALLET'],$fsp->fsp_type ??'', ['class'=>'form-control', 'placeholder'=>'--SELECT FSP TYPE--', 'required' => 'required']) !!}
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
