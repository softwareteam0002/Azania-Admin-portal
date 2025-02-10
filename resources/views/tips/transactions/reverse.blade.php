@extends('layouts.admin')
@section('title', 'Transactions')
@section('content')
    <div class="sl-mainpanel">
        <nav class="breadcrumb sl-breadcrumb">
            <a class="breadcrumb-item" href="{{url('/home')}}">Dashboard</a>
            <a class="breadcrumb-item" href="{{url('fsps')}}">Transactions</a>
            <span class="breadcrumb-item active">Reversal</span>
        </nav>

        <div class="sl-pagebody">
            <!-- <div class="sl-page-title">
            </div> -->
            <div class="mt-12">
                <div class="row">
                    <div class="col-12 col-sm-10 col-md-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <a href="{{url('tips/transactions')}}" class="btn btn-sm btn-primary">View Transactions</a></div>
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
                                {!! Form::model($transaction, [
                         'method' => 'PATCH',
                         'url' => ['tips/transactions', $transaction->id],
                         'role' => 'form'
                     ]) !!}
                                <div class="row">
                                    <input type="hidden" name="payer_fspId" value="{{$transaction->payer_fspId}}"/>
                                    <input type="hidden" name="payee_fspId" value="{{$transaction->payee_fspId}}"/>

                                    <div class="form-group col-6">
                                        {!! Form::label('payer_fullName', 'PAYER NAME', array('class' => 'd-block')) !!}
                                        {!! Form::text('payer_fullName',null,['class'=>'form-control', 'placeholder'=>'Payer Name', 'required' => 'required', 'readonly']) !!}
                                    </div>
                                    <div class="form-group col-6">
                                        {!! Form::label('payerRef', 'PAYER REF ID', array('class' => 'd-block')) !!}
                                        {!! Form::text('payerRef',null,['class'=>'form-control', 'placeholder'=>'Payer Ref ID', 'readonly']) !!}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-6">
                                        {!! Form::label('payeeRef', 'PAYEE REF ID', array('class' => 'd-block')) !!}
                                        {!! Form::text('payeeRef',null,['class'=>'form-control', 'placeholder'=>'Payee Ref ID', 'readonly']) !!}
                                    </div>
                                    <div class="form-group col-6">
                                        {!! Form::label('switchRef', 'SWITCH REFERENCE', array('class' => 'd-block')) !!}
                                        {!! Form::text('switchRef',null,['class'=>'form-control', 'placeholder'=>'Switch Reference', 'readonly']) !!}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-6">
                                        {!! Form::label('amount', 'AMOUNT', array('class' => 'd-block')) !!}
                                        {!! Form::text('amount',null,['class'=>'form-control', 'placeholder'=>'Amount', 'readonly']) !!}
                                    </div>
                                    <div class="form-group col-6">
                                        {!! Form::label('currency', 'CURRENCY', array('class' => 'd-block')) !!}
                                        {!! Form::text('currency',null,['class'=>'form-control', 'placeholder'=>'Currency', 'readonly']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('reversalReason', 'Reversal Reason', array('class' => 'd-block')) !!}
                                    {!! Form::textarea('reversalReason',null,['class'=>'form-control', 'placeholder'=>'Reversal Reason','required','rows'=>1]) !!}
                                </div>
                                <div class="form-group">
                                    {!! Form::submit('Reverse Transaction', ['class'=>'btn btn-primary btn-block']) !!}
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- sl-pagebody -->
@endsection
