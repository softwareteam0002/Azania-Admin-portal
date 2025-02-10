

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
        <li class="breadcrumb-item"><a href="{{ url('/admin/roles') }}">Roles</a></li>
        <li class="breadcrumb-item active">Edit Role</li>
    </ol>
</nav>

<h5>Edit Role</h5>
<hr />
<div class="card">
    <div class="card-body">
        <h6>Edit Role form</h6>
        <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>

<form action="{{ url('/admin/roles/update') }}" method="POST">

            @csrf
         @method('PUT')
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="name">Title:<span class="text-danger">*</span></label>
                        <input type="text" id="title" placeholder="Enter role title here" value="{{ $role->title }}" name="title" class="form-control form-control-sm" required>
                        <input type="hidden" value="{{ $role->id }}" name="role_id" >
                    </div>
                </div>
                 </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="title">Permissions:<span class="text-danger">*</span></label>
                        <br/>
                       <div class="row pt-6">
                        @foreach($permissions as  $permission)
                           
                                 <div class="col-md-6 role-list">
                                <div class="form-check form-check-inline">
                                   
                                    <input type="checkbox" id="permissions{{$permission->id}}" name="permissions[]"  value="{{$permission->id}}"  
                  {{in_array($permission->id, $rolePermissions) ? 'checked' : ''}} >  &nbsp; {{$permission->name}}
                                </div>
                                 </div>
                              @endforeach
                      </div>
                     </div>
                </div>

                <div class="col-md-12">
                    <hr />
                    <button type="submit" class="btn btn-success btn-sm">Save</button>
                    <a href="{{ url('/admin/roles') }}" class="btn btn-sm btn-link"><i class="fas fa-arrow-left"></i> Go back</a>
                </div>
            </div>


        </form>
    </div>
</div>


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

