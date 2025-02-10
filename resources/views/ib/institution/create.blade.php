@extends('layouts.admin')
@section('content')
    <div style="margin-bottom: 10px;" class="row">

    </div>
    <div class="card">
        <div class="card-header">
            Create Institution Internet Banking
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    @foreach($errors->all() as $error)
                        <li class="list-group-item">
                            {{ $error }}
                        </li>
                    @endforeach
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(Session::has('message'))
                <div class="alert alert-success alert-dismissible">
                    <ul>
                        <li>{{Session::get('message')}}</li>
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(isset($notification) && isset($color))
                <div class="alert alert-{{ $color }} alert-dismissible fade show" role="alert">
                    {{ $notification }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form action="{{ url('ib/institution/store') }}" method="POST">
                @csrf
                @method('POST')
                <div class="form-group">
                    <label for="name">Institution Name</label>
                    <input type="text" id="institute_name" name="institute_name" class="form-control" >
                </div>

                <div class="form-group">
                    <label for="name">Address</label>
                    <input type="text" id="address" name="address" class="form-control" >
                </div>

                <div class="form-group">
                    <label for="name">Description</label>
                    <input type="text" id="description" name="description" class="form-control" >
                </div>


                <div>
                    <input class="btn btn-success form-control" type="submit" value="{{ trans('global.save') }}">
                </div>
            </form>
        </div>
    </div>

@endsection
