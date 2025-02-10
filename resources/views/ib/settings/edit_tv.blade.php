@extends('layouts.admin')
@section('content')
    <div class="row mb-3">
        <div class="col-md-8">
            <!-- Notifications-->
            @if(Session::has('color'))
                <div class="alert alert-{{ Session::get('color') }} alert-dismissible fade show" role="alert">
                    {{ Session::get('notification') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
        </div>

        <div class="col-md-4 text-right">
            <!-- Date and Time-->
            <p id="todayDate" class="small"><b></b></p>
            <h5 id="todayTime" class=""><b></b></h5>
        </div>
        <hr/>
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small" style="background-color:#f9f9f9; padding: .55rem 1rem;">
            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('ib/view_tv') }}">Television Service Providers</a></li>
            <li class="breadcrumb-item active">Edit Television Service Provider</li>
        </ol>
    </nav>

    <h5> Edit Television Service Providers</h5>
    <hr/>

    <div class="card mb-5">
        <div class="card-body">
            <h6>Edit Television Service Providers form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ url('ib/update_tv') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">TV name:</label>
                            <input type="text" name="name" class="form-control form-control-sm" value="{{ $tv->name }}">
                            <input type="hidden" name="id" value="{{ $tv->id }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Description:</label>
                            <textarea name="description" class="form-control form-control-sm" id="" cols="30" rows="4" value="Enter descriton here.">{{ $tv->description }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <hr/>
                        <button type="submit" class="btn btn-success btn-sm">Save</button>
                        <a href="{{ url('ib/view_tv') }}" class="btn btn-link btn-sm"><i class="fas fa-arrow-left"></i> Go back</a>
                    </div>

                </div>

                    

            </form>
        </div>
    </div>


@section('scripts')
    @parent
    <script>
        $(function () {
            $('.datatable').DataTable()
        });

    </script>
@endsection
@endsection
