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
        <li class="breadcrumb-item active">Permissions</li>
    </ol>
</nav>

<h5>Permissions</h5>
<hr />


@can('um_permissions_create')
<div class="card">
    <div class="card-body">
        <h6>Create Permission form</h6>
        <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>

        <form action="{{ url('admin/permissions') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                        <label for="name">Name:<span class="text-danger">*</span></label>
                        <input type="text" id="name" placeholder="Enter permission name here" name="name" class="form-control form-control-sm" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="title">Title:<span class="text-danger">*</span></label>
                        <input type="text" id="title" placeholder="Enter permission title here" name="title" class="form-control form-control-sm" value="{{ old('title', isset($permission) ? $permission->title : '') }}" required>
                    </div>
                </div>

                <div class="col-md-12">
                    <hr />
                    <button type="submit" class="btn btn-success btn-sm">Save</button>
                    <button type="reset" class="btn btn-link btn-sm">Clear form</button>
                </div>
            </div>


        </form>
    </div>
</div>
@endcan


<h5>All Permissions</h5>
<hr />

@can('um_permissions_view')
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Title</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $key => $permission)
                    <tr>
                        <td> {{ $permission->name ?? '--' }}</td>
                        <td> {{ $permission->title ?? '--' }}</td>



                        <td>
                            @can('um_permissions_edit')
                            <a class="btn btn-sm btn-primary" href="{{ url('/admin/permissions', $permission->id) }}">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endcan

                            @can('um_permissions_delete')
                            <form action="{{ url('/admin/permissions/delete') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="permission_id" value="{{ $permission->id }}">
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                            @endcan

                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endcan


@section('scripts')
@parent
<script>
    $(function() {
        //load the data table
        $('.datatable').DataTable({})
    })
</script>
@endsection
@endsection