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
        <li class="breadcrumb-item active">Images upload</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-12">
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
    </div>
</div>

<!-- Dashboard Image  -->
<div class="row mt-2">
    <div class="col-md-12">
        <h5>Dashboard Page Images</h5>
        <hr />
        <div class="card">
            <div class="card-body">
                <h6 class="">Dashboard image upload form</h6>
                <p class="small text-danger">All fields with an asterisk(*) are mandatory</p>
                <form method="POST" action="{{ route('ib.upload.image') }}" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="input">Image:<span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" id="image_name" name="image_name" required>
                                <p class="small">Format is .JPG, .GIF or .PNG</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="input">Title:<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="title" name="title" placeholder="Enter the title here.">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="input">Description:<span class="text-danger">*</span></label>
                                <textarea name="desc" class="form-control form-control-sm" id="desc" cols="30" rows="4" placeholder="Enter the description here." required></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr />
                            <button type="submit" class="btn btn-success btn-sm">Upload</button>
                            <button type="reset" class="btn btn-link btn-sm">Clear form</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <h6 class="">Uploaded images</h6>
        <hr />

        <div class="card">
            <div class="card-body">

                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Image</th>
                            <th scope="col">Title</th>
                            <th scope="col">Description</th>
                            <th scope="col">Initiator</th>
                            <th scope="col">Approver</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ibImages as $dashboard)
                        <tr>
                            <td width="400px"><img src="{{ asset('images/'.$dashboard->image_name) }}" class="rounded" width="100ox" height="auto" alt=""></td>
                            <td>{{ $dashboard->title }}</td>
                            <td>{{ $dashboard->description }}</td>
                            <td>{{ $dashboard->initiators->name ?? '--' }}</td>
                            <td>{{ $dashboard->approvers->name ?? '--' }}</td>
                            <td>{{ $dashboard->status }}</td>
                            <td>

                                <form class="d-inline" action="{{ url('ib/image_upload/delete') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $dashboard->id }}">
                                    <input type="hidden" name="i" value="dashboard">
                                    <button href="" title="Delete image" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>


                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>


            </div>
        </div>



    </div>
</div>

<!-- Login Image  -->
<div class="row mt-2">
    <div class="col-md-12">
        <h5>Login Page Images</h5>
        <hr />
        <div class="card">
            <div class="card-body">
                <h6 class="">Login page image upload form</h6>
                <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
                <form enctype="multipart/form-data" method="POST" id="loginImageUploadForm">
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="input">Image:<span class="text-danger">*</span></label>
                                <input type="file" class="form-control form-control-sm" name="image_name" id="image_name" required>
                                <p class="small">Format is .JPG, .GIF or .PNG</p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr />
                            <button type="submit" onclick="loginImage('{{ url('ib/image_upload/store') }}')" class="btn btn-success btn-sm">Upload</button>
                            <button type="reset" class="btn btn-link btn-sm">Clear form</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <h6 class="">Uploaded images</h6>
        <hr />

        <div class="card">
            <div class="card-body">


                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Image</th>
                            <th scope="col">Title</th>
                            <th scope="col">Description</th>
                            <th scope="col">Initiator</th>
                            <th scope="col">Approver</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ibLoginImages as $ibLoginImage)
                        <tr>
                            <td width="400px"><img src="{{ asset('images/'.$ibLoginImage->image_name) }}" class="rounded" width="100ox" height="auto" alt=""></td>
                            <td>{{ $ibLoginImage->title ?? 'N/A' }}</td>
                            <td>{{ $ibLoginImage->description ?? 'N/A' }}</td>
                            <td>{{ $ibLoginImage->initiators->name ?? '--' }}</td>
                            <td>{{ $ibLoginImage->approvers->name ?? '--' }}</td>
                            <td><span class="badge badge-success">{{ $ibLoginImage->status ?? 'N/A' }}</span></td>
                            <td>

                                <form class="d-inline" action="{{ url('ib/image_upload/delete') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $ibLoginImage->id }}">
                                    <input type="hidden" name="i" value="login">
                                    <button href="" title="Delete image" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>


            </div>
        </div>


    </div>
</div>


<script>
    function loginImage(url) {
        var form = document.getElementById('loginImageUploadForm');

        form.action = url;
    }

    function dashboardImage(url) {
        var form = document.getElementById('dashboardImageUploadForm');

        form.action = url;
    }


    //show the edit div
    $(".editImgBtn").on("click", function(e) {
        e.preventDefault();
        var tag = $(this).attr("data-target");
        $("#" + tag).removeClass("d-none");

    });
    //hide the edit dig
    $(".hideEditImgBtn").on("click", function(e) {
        e.preventDefault();
        var tag = $(this).attr("data-target");
        $("#" + tag).addClass("d-none");

    });
</script>


@endsection
