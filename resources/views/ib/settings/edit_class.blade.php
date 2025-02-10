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
            <li class="breadcrumb-item"><a href="{{ url('ib/view_bank/') }}">Classes</a></li>
            <li class="breadcrumb-item active">Edit Class</li>
        </ol>
    </nav>

    <h5>Edit Class</h5>
    <hr/>

    <div class="card mb-5">
        <div class="card-body">
            <h6>Edit Class form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ url('ib/update_class') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">

                    <input type="hidden" name="action" value="Edit Class">
                    <input type="hidden" name="id" value="{{ $class->id }}">
                    <input type="hidden" name="old_details" value="{{ $class }}">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Class name:</label>
                            <input type="text" name="name" class="form-control form-control-sm" value="{{ $class->class_name }}" placeholder="Enter class name here.">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">From Amount</label>
                            <input type="text" name="fromAmount" class="form-control form-control-sm" value="{{ $class->fromAmount }}" placeholder="Enter From Amount here.">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">To Amount</label>
                            <input type="text" name="toAmount" class="form-control form-control-sm" value="{{ $class->toAmount }}" placeholder="Enter To Amount here.">
                        </div>
                    </div>

                  @if(isset($class->institution_id))
                        <div class="col-md-3">
                        <div class="form-group">
                            <label for="types">Institution Name</label>
                            <select name="institution_id" class="form-control form-control-sm">
                                <option value="0" >Select Institution Name</option>
                                @foreach($institutions as $institution)
                                    <option value="{{ $institution->id }}"  @if($class->institution_id==$institution->id)
                                    selected="selected" @endif>{{ $institution->institute_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif

                    <div class="col-md-12">
                        <hr/>
                        <button type="submit" class="btn btn-success btn-sm">Save</button>
                        <a href="{{ url('ib/view_class') }}" class="btn btn-link btn-sm"><i class="fas fa-arrow-left"></i> Go back</a>
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
