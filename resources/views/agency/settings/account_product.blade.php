@extends('layouts.admin')
@section('content')
    <div class="row mb-3">
        <div class="col-md-8">
            @include('error_handler')
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
            <li class="breadcrumb-item active">Account Product</li>
        </ol>
    </nav>

    <h5>Account Products</h5>
    <hr/>

    <div class="card mb-5">
        <div class="card-body">
            <h6>Create Account Product form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ url('ib/view_account_product') }}" method="POST">
                @csrf
                @method('POST')
                <div class="row">

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Account Product Name:</label>
                            <input type="text" name="account_product" class="form-control form-control-sm" placeholder="Enter Account Product Name here.">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Account Product Code:</label>
                            <input type="text" name="account_product_code" class="form-control form-control-sm" placeholder="Enter Account Product Code here.">
                        </div>
                    </div>
                     <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Account Description:</label>
                            <input type="text" name="account_description" class="form-control form-control-sm" placeholder="Enter Account Product Description Code here.">
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



    <h5>All Account Products</h5>
    <hr/>

    <div class="card">
        <div class="card-body">
            <div class=" table-responsive-sm">
                <table class="table table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th width="10">
                        </th>
                        <th>Account Product Name</th>
                        <th>Account Product Code</th>
                        <th>Account Description</th>
                        <th>Action</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($account_products as $account_product)

                        <tr>
                            <td></td>
                            <td>{{$account_product->account_product_type_code_name ?? 'N/A'}}</td>
                            <td>{{$account_product->account_product_type_code ?? 'N/A'}}</td>
                            <td>{{$account_product->account_description ?? 'N/A'}}</td>

                            <td>
				@if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)
                                <form action="{{ url('agency/requests/actions') }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <input type="hidden" name="id" value="{{ $account_product->id }}">
                                   
                                    <a href="{{ url('agency/account_product/edit/'. $account_product->id ) }}" class="btn btn-sm btn-primary">
                                        <span><i class="fas fa-edit text-dark-pastel-green"></i></span>
                                    </a>
									
                                  
                                </form>
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

