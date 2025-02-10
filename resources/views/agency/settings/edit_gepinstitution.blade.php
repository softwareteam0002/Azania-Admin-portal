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
            <li class="breadcrumb-item"><a href="{{ url('agency/view_gepg_institution') }}">GEPG Institutions</a></li>
            <li class="breadcrumb-item active">Edit GEPG Institution</li>
        </ol>
    </nav>

    <h5>Edit GEPG Institution</h5>
    <hr/>

    <div class="card mb-5">
        <div class="card-body">
            <h6>Edit GEPG Institution form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ url('agency/update_gepg_institution') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">GEPG Institution name:<span class="text-danger">*</span></label>
                            <input type="text" name="institution_name" class="form-control form-control-sm"
                                   value="{{  $gepInstitution->institution_name }}">
                            <input type="hidden" name="id" value="{{  $gepInstitution->institution_id }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Institution code:<span class="text-danger">*</span></label>
                            <input type="text" name="institution_code" class="form-control form-control-sm"
                                   value="{{ $gepInstitution->institution_code ??  '' }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Institution charges:</label>
                        <input type="text" name="institution_charges" class="form-control form-control-sm" value="{{ $gepInstitution->institution_charges ??  '' }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Collection account:</label>
                        <input type="text" name="collection_account" class="form-control form-control-sm" value="{{ $gepInstitution->collection_account ??  '' }}">
                    </div>
                </div>

                    <div class="col-md-12">
                        <hr/>
                        <button type="submit" class="btn btn-success btn-sm">Save</button>
                        <a href="{{ url('agency/view_gepg_institution') }}" class="btn btn-link btn-sm"><i class="fas fa-arrow-left"></i> Go back</a>
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