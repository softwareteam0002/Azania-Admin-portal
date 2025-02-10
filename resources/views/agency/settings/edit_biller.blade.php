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
    <hr />
</div>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb small" style="background-color:#f9f9f9; padding: .55rem 1rem;">
        <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ url('agency/view_biller') }}">Billers</a></li>
        <li class="breadcrumb-item active">Edit Biller</li>
    </ol>
</nav>

<h5>Edit Biller</h5>
<hr />


<div class="card mb-5">
    <div class="card-body">
        <h6>Edit Biller form</h6>
        <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
        <form action="{{ url('agency/update_biller') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">

                <div class="col-md-2">
                    <div class="form-group">
                        <label for="name">Biller name:<span class="text-danger">*</span></label>
                        <input type="text" name="biller_short_name" class="form-control form-control-sm" value="{{ $biller->biller_short_name }}">
                        <input type="hidden" name="id" value="{{ $biller->biller_id }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="name">Biller description:<span class="text-danger">*</span></label>
                        <textarea name="biller_description" id="biller_description" cols="30" rows="1" class="form-control form-control-sm" value="{{ $biller->biller_description }}"></textarea>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label for="name">Biller group:<span class="text-danger">*</span></label>
                        <select name="biller_group" id="biller_group" class="form-control form-control-sm">
                            @foreach($billergroups as $billergroup)
                            @if($billergroup->biller_group_id == $biller->biller_group)
                            <option value="{{ $billergroup->biller_group_id }}" selected>{{ $billergroup->biller_group_name }}</option>
                            @else
                            <option value="{{ $billergroup->biller_group_id }}">{{ $billergroup->biller_group_name }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>


                <div class="col-md-2">
                    <div class="form-group">
                        <label for="biller-status">Utility code:<span class="text-danger">*</span></label>
                        <input type="text" name="utility_code" class="form-control form-control-sm" value="{{ $biller->utility_code }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="biller-status">Institution name:<span class="text-danger">*</span></label>
                        <input type="text" name="biller_institution_name" class="form-control form-control-sm" value="{{ $biller->biller_institution_name }}.">
                    </div>
                </div>



                <div class="col-md-12">
                    <hr />
                    <button type="submit" class="btn btn-success btn-sm">Save</button>
                    <a href="{{ url('agency/view_biller') }}" class="btn btn-link btn-sm"><i class="fas fa-arrow-left"></i> Go back</a>
                </div>

            </div>



        </form>
    </div>
</div>



@section('scripts')
@parent
<script>
    $(function() {
        $('.datatable').DataTable()
    });
</script>
@endsection
@endsection