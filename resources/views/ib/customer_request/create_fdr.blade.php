@extends('layouts.admin')
@section('content')
    
    <div class="card">
        <div class="card-header">
            Add Fixed Deposit Rate
        </div>

        <div class="card-body">
            @if(Session::has('color'))
                <div class="alert alert-{{ Session::get('color') }} alert-dismissible fade show" role="alert">
                    {{ Session::get('notification') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form action="{{ url('ib/fdr/store') }}" method="POST">
                @csrf
                @method('POST')
                <div class="row">
                    <div class="col-xl-3 col-lg-6 col-12 form-group">
                        <label for="name">Tenure of Term Deposit*</label>
                        <input type="text" name="tenure_of_term_deposit" class="form-control" required>
                    </div>
			
		    <div class="col-xl-3 col-lg-6 col-12form-group">
                        <label for="name">Existing  rates*</label>
                        <input type="text" name="existing_rates" class="form-control"  required>
                    </div>

		   <div class="col-xl-3 col-lg-6 col-12form-group">
                        <label for="name">Revised  rates*</label>
                        <input type="text" name="revised_rates" class="form-control"  required>
                    </div>

                     <div class="col-xl-3 col-lg-6 col-12form-group">
                        <label for="name">Existing  rates Senior*</label>
                        <input type="text" name="existing_rates_sr" class="form-control"  required>
                    </div>

		     	
		     <div class="col-xl-3 col-lg-6 col-12form-group">
                        <label for="name">Existing  rates Junior</label>
                        <input type="text" name="existing_rates_jr" class="form-control"  required>
                    </div>
			
		     <div class="col-xl-3 col-lg-6 col-12form-group">
                        <label for="name">Amount Interval</label>
                        <input type="text" name="amount_interval" class="form-control"  required>
                    </div>

      
                      <br/><br/>

	
	

                    <div class="col-12 form-group mg-t-8">
                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                      
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
