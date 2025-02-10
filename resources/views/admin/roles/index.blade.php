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
            <li class="breadcrumb-item active">Roles</li>
        </ol>
    </nav>

    <h5>Roles</h5>
    <hr/>

    <div class="row">
	
        <div class="card">
            <div class="card-body">
                <h6>Create Role form</h6>
                <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>

                <form action="{{ url('admin/roles') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">Title:<span class="text-danger">*</span></label>
                                <input type="text" id="title" placeholder="Enter role title here" name="title"
                                       class="form-control form-control-sm" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="title">Permissions:<span class="text-danger">*</span></label>
                            <br/>


                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="pt-4"><strong>Agent banking</strong></h6>
                                    <hr/>
                                    <div class="row pt-6">
                                        @foreach($permissions as $permission)
                                            @if(stristr($permission->title, "ab_"))
                                                <div class="col-md-3 role-list">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox"
                                                               name="permissions[]"
                                                               value="{{ $permission->id}}">
                                                        <label
                                                            class="form-check-label font-sm">{{ $permission->name ?? '--' }}</label>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <h6 class="pt-4"><strong>User management</strong></h6>
                                    <hr/>
                                    <div class="row pt-4">
                                        @foreach($permissions as $permission)
                                            @if(stristr($permission->title, "um_"))
                                                <div class="col-md-4 role-list">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox"
                                                               name="permissions[]"
                                                               value="{{ $permission->id}}">
                                                        <label
                                                            class="form-check-label font-sm">{{ $permission->name ?? '--' }}</label>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                            </div>


                        </div>
                    </div>

                    <div class="col-md-12">
                        <hr/>
                        <button type="submit" class="btn btn-success btn-sm">Save</button>
                        <button type="reset" class="btn btn-link btn-sm">Clear form</button>
                    </div>
            </div>


            </form>
        </div>
    </div>
    </div>

   <div class="col">
    <h5>All Roles</h5>
    <hr/>
	
	    <div class="card">
        <div class="card-body table-responsive">
           <table class="table table-bordered table-striped table-hover datatable">
    <thead>
        <tr>
            <th>Title</th>
            <th>Permissions</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($roles as $key => $role)
            <tr>
                <td>{{ $role->title ?? '--' }}</td>
                <td style='width:70%'>
                    @php
                        $permissionsToShow = $role->permissions->take(6); // Display only the first 3 permissions
                        $remainingPermissions = $role->permissions->count() - $permissionsToShow->count(); // Remaining permissions count
                    @endphp

                    @foreach($permissionsToShow as $key => $item)
                        <span class="badge badge-info">{{ $item->name }}</span>
                    @endforeach

                    @if($remainingPermissions > 0)
                        <span class="badge badge-secondary" data-toggle="tooltip" data-placement="top"
                              title="{{ $role->permissions->pluck('name')->skip(3)->join(', ') }}">
                            +{{ $remainingPermissions }} more
                        </span>
                    @endif
                </td>
                <td>
                    <a class="btn btn-sm btn-primary" href="{{ url('admin/roles', $role->id) }}">
                        <i class="fas fa-edit"></i>
                    </a>

                    <form action="{{ url('admin/roles/delete') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="role_id" value="{{ $role->id }}">
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
        </div>
        </form>
    </div>
   </div>


    @section('scripts')
        @parent
        <script>
            $(function () {
                //load the data table
                $('.datatable').DataTable({})
            })
        </script>
		<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
    @endsection
@endsection
