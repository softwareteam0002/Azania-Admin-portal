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
        <li class="breadcrumb-item active">Payment Solutions</li>
    </ol>
</nav>


<h5>Payment Solution</h5>
<hr />

<div class="card">
    <div class="card-body">
        <label>Institutions:</label>
        <div class="input-group">
            <select class="custom-select" id="institutionID" aria-label="Example select with button addon">
                <option selected>Select an institution</option>
                @foreach($institutions as $institution)
                <option id="instituteOption" value="{{$institution->id}}">{{$institution->institute_name}}</option>
                @endforeach
            </select>
            <div class="input-group-append">
                <button class="btn btn-primary" onclick="attrClick()">Submit</button>
            </div>
        </div>
    </div>
</div>

<script>
   function attrClick(){
       var v = $("#institutionID").val();
       window.location.replace("{{ url('ib/payments_solution/institution/') }}/"+v);;
   }
</script>




<hr />
<div class="card d-none">
    <div class="card-body">
        <div class="table-responsive-sm">
            <table class="table table-striped table-sm table-hover datatable">
                <thead>
                    <tr>
                        <th>Institution Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($institutions as $institution)
                    <tr>
                        <td>{{$institution->institute_name}}</td>
                        <td>
                            <!--- Added Button for Payment Service Activation-->
                            <a class="btn btn-primary btn-sm" href="{{ url('ib/payments_solution/payments/'.$institution->id) }}"><i class="fas fa-eye" title="View Payment Services"></i> </a>
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
    $(document).ready(function() {
        $('.datatable').DataTable();
    });
</script>
@endsection

@endsection