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
            <li class="breadcrumb-item active">transactionClasses</li>
        </ol>
    </nav>

    <h5>Transaction Classes</h5>
    <hr/>

    <div class="card mb-5">
        <div class="card-body">
            <h6>Create Transaction Classes form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ url('ib/view_transaction_class') }}" method="POST">
                @csrf
                @method('POST')
                <div class="row">
			   <div class="col-md-3">
                       <div class="form-group">
                    <label for="types">Transfer Type:<span class="text-danger">*</span></label>
                    <select name="transfer_type_id" class="form-control" required>
                        <option value="" >Select Transfer Type</option>
                        @foreach($transferTypes as $transferType)
                            <option value="{{ $transferType->id }}" >{{ $transferType->name }}</option>
                        @endforeach
                    </select>
                </div>
                    </div>
			<div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Transaction Class name:<span class="text-danger">*</span></label>
                            <select name="class_name" id="class_name" class="form-control" required>
				<option value="" >Select Class Name</option>
                        @foreach($classes as $class)
                            <option value="{{ $class }}" >{{ $class }}</option>
                        @endforeach

                    </select>
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



    <h5>All transactionClasses</h5>


    <hr/>
    <div class="card">
        <div class="card-body">
            <div class=" table-responsive-sm">
                <table class="table table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th width="10">
                        </th>
                        <th>Transfer Type</th>
                        <th>Class Name</th>
                        <th>Action</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($transactionClasses as $class)

                        <tr>
                            <td></td>
                            <td>{{$class->transfer_types->name ?? ''}}</td>
                            <td>{{$class->class_name}}</td>
                             <td>
                                      @if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)
									 <a href="{{ url('ib/view_transaction_class/edit/'. $class->id ) }}" class="btn btn-sm btn-primary">
                                         <span><i class="fas fa-edit text-dark-pastel-green"></i></span>
                                     </a>
									 @endif
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
            $('.datatable').DataTable()
        });

    </script>
@endsection
@endsection
