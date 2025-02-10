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
            <li class="breadcrumb-item"><a href="{{ url('ib/view_bank/') }}">Banks</a></li>
            <li class="breadcrumb-item active">Edit bank</li>
        </ol>
    </nav>

    <h5>Edit Bank</h5>
    <hr/>

    <div class="card mb-5">
        <div class="card-body">
            <h6>Edit bank form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ url('ib/update_bank') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">

                    <input type="hidden" name="action" value="Edit Bank">
                    <input type="hidden" name="request_type" value="ib_bank_settings">
                    <input type="hidden" name="module" value="IB">
                    <input type="hidden" name="old_details" value="{{ $bank }}">
                    <input type="hidden" name="initiator_id" value="{{ Auth::user()->id }}">
                    <input type="hidden" name="approver_id" value="0">

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Bank name:</label>
                            <input type="hidden" name="id" value="{{ $bank->id }}">
                            <input type="text" name="name" class="form-control form-control-sm" value="{{ $bank->name }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Bank Code:</label>
                            <input type="text" name="bank_code" class="form-control form-control-sm" value="{{ $bank->bank_code }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">BIC/Swift Code:</label>
                            <input type="text" name="swift_code" class="form-control form-control-sm" placeholder="Enter swift code here." value="{{ $bank->swift_code }}">
                        </div>
                    </div>
                     <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Short Name:</label>
                            <input type="text" name="shortName" class="form-control form-control-sm" placeholder="Enter bank short name here." value="{{ $bank->shortName }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Description:</label>
                            <textarea name="description" class="form-control form-control-sm" id="" cols="30" rows="4" >{{ $bank->description }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Address:</label>
                            <textarea name="address" class="form-control form-control-sm" id="" cols="30" rows="4" >{{ $bank->address }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <hr/>
                        <button type="submit" class="btn btn-success btn-sm">Save</button>
                        <a href="{{ url('ib/view_bank') }}" class="btn btn-link btn-sm"><i class="fas fa-arrow-left"></i> Go back</a>
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
