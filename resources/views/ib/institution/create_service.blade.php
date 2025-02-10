@extends('layouts.admin')
@section('content')

    <div class="row mb-3">
        <div class="col-md-8">
            <!-- Notifications-->
            @if(session('notification') && session('color'))
                <div class="alert alert-{{ session('color') }} alert-dismissible fade show" role="alert">
                    {{ session('notification') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{$error}}</li>
                        @endforeach
                    </ul>
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
            <li class="breadcrumb-item"><a href="{{ url('ib/institutions/index') }}">Institutions</a></li>
            <li class="breadcrumb-item active">Institution Payment Services</li>
        </ol>
    </nav>


    <h5>{{ $institution->institute_name }} Payment Services</h5>
    <hr/>
    @can('view_internet')
        <div class="card">
            <div class="card-body">
                <h6 class="">Create Institution service form</h6>
                <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
                <form action="{{ url('ib/institution/service') }}" method="POST">
                    @csrf
                    @method('POST')

                    <div class="row">
                        <div class="col-md-3">
                            <div id="name_input_group" class="form-group">
                                <label for="name">Service Name:<span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control form-control-sm"  placeholder="Enter the service name here." required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div id="name_input_group" class="form-group">
                                <label for="name">Minimum Amount:<span class="text-danger">*</span></label>
                                <input type="text" id="minnimum_amount" name="minnimum_amount" class="form-control form-control-sm"  placeholder="Enter the service minnimum amount here." required>
                            </div>
                        </div>
                      
                        <div class="col-md-3">
                            <div id="name_input_group" class="form-group">
                                <label for="name">Service Code:<span class="text-danger">*</span></label>
                                <input type="text" id="code" name="code" class="form-control form-control-sm"  placeholder="Enter the service code here." required>
                                <input type="hidden" ame="institution_id" value="{{ $institution->id }}">
                            </div>
                        </div>
                      
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="types">Service Type:<span class="text-danger">*</span></label>
                                <select name="option_id" class="form-control  form-control-sm">
                                    <option value="0" >Select Service Type</option>
                                    <option value="1">One</option>
                                    <option value="2">Two</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <hr/>
                            <!-- <input class="btn btn-success form-control" type="submit" value="{{ trans('global.save') }}"> -->
                            <button type="submit" class="btn btn-success btn-sm">Save</button>
                            <button type="reset" class="btn btn-link btn-sm">Clear form</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


    @endcan

    <h5>Registered Payment Services.</h5>
    <hr/>
    <div class="card">
        <div class="card-body">
            <h6 class="mb-4">All registered Payment Services.</h6>
            <div class="table-responsive">
                <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                    <thead>
                    <tr><th width="10"> </th>
                        <th>Service Name</th>
                        <th>Service Type ID</th>
                        <th>Min. Amount</th>
                        <th>Service Code</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($services as $service)
                    <tr>
                        <td></td>
                        <td>{{$service->name}}</td>
                        <td>{{$service->service_type_id}}</td>
                        <td>{{ number_format($service->minimum_amount, 2) }}/=</td>
                        <td>{{$service->code}}</td>
                        
                    <td>
                        <a class="btn btn-primary btn-sm" title="Edit User" href="{{ url('ib/institution/service/edit/'.$service->id ) }}">
                            <i class="fas fa-edit text-dark-pastel-green"></i>
                        </a>
                    </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>



@section('scripts')
    @parent
    <script>
        $(function () {
            $('.datatable').DataTable();
        });
    </script>
@endsection

@endsection
