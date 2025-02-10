@extends('layouts.admin')
@section('title', 'Settlements')
@section('content')
<div class="sl-mainpanel">
      <nav class="breadcrumb sl-breadcrumb">
        <a class="breadcrumb-item" href="{{url('/home')}}">Dashboard</a>
        <a class="breadcrumb-item" href="{{url('tips/settlements')}}">Settlements</a>
        <span class="breadcrumb-item active">List</span>
      </nav>

<div class="sl-pagebody">
<!-- <div class="sl-page-title">
</div> -->
 <div class="row">
              <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                  <div class="card-header">
                    <div class="row">
                      <div class="float-left">
                       <!-- <a href="{{url('tips/settlements/create')}}" class="btn btn-sm btn-primary">Add Transaction</a>-->
                      </div>
                    </div>
                    <div class="float-right">
                      <form>
                        <div class="input-group">
                          <input type="text" name="search" id="search" class="form-control" placeholder="Search" >

                          <div class="input-group-btn">
                            <button class="btn btn-secondary" id="searchBtn"><i class="fas fa-search"></i></button>
                          </div>
                        </div>
                      </form>
                    </div>
                    <!-- <h4>Permissions</h4> -->
                  </div>
                  <div class="card-body p-1">
                    @if (session('success'))
                      <div class="alert alert-success">
                          {{ session('success') }}
                      </div>
                    @endif
					 @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                    <div class="table-responsive" style="overflow-x:auto;">
                      <table class="table table-striped">
                        <thead>
                           <tr>
                          <th>SETTLEMENT DATE</th>
                          <th>WINDOW ID</th>
						  <th>DESCRIPTION</th>
                          <th>OUTGOING VOLUME</th>
						  <th>OUTGOING VALUE</th>
						  <th>INCOMING VOLUME</th>
						  <th>INCOMING VALUE</th>
						  <th>POSITION TYPE</th>
						  <th>POSITION AMOUNT</th>
						  <th>POSITION LEDGER NAME</th>
						  <th>POSITION LEDGER AMOUNT</th>
						  <th>FEE TYPE</th>
                          <th>FEE AMOUNT</th>
						  <th>FEE LEDGER INT.CHANGE NAME</th>
						  <th>FEE LEDGER INT.CHANGE AMOUNT</th>
						  <th>FEE LEDGER PROCESSING NAME</th>
						  <th>FEE LEDGER PROCESSING AMOUNT</th>
						  <th>STATUS</th>
                         <!-- <th width="15%">Action</th>-->
                        </tr>
                        </thead>
                       <tbody id="tableData">
                        @foreach($settlements as $settlement)
                         <tr>
                          <td>{{$settlement->settlementWindow_date}}</td>
                          <td>{{$settlement->settlementWindow_id}}</td>
                          <td>{{$settlement->settlementWindow_description}}</td>
						  <td>{{$settlement->outgoingTransactions_volume}}</td>
						  <td>{{$settlement->outgoingTransactions_currency}}{{ number_format($settlement->outgoingTransactions_value, 2, '.', ',')}}</td>
                          <td>{{$settlement->incomingTransactions_volume}}</td>
						  <td>{{$settlement->incomingTransactions_currency}}{{ number_format($settlement->incomingTransactions_value, 2, '.', ',')}}</td>
						  <td>{{$settlement->position_type}}</td>
						  <td>{{$settlement->position_currency}}{{ number_format($settlement->position_amount, 2, '.', ',')}}</td>
						   <td>{{$settlement->position_ledger_name}}</td>
						  <td>{{ number_format($settlement->position_ledger_amount, 2, '.', ',')}}</td>
						  <td>{{$settlement->type_fee}}</td>
						  <td>{{$settlement->fee_currency}} {{ number_format($settlement->fee_amount, 2, '.', ',')}}</td>
						  <td>{{$settlement->fee_ledger_name_interchange}}</td>
						  <td>{{ number_format($settlement->fee_ledger_amount_interchange, 2, '.', ',')}}</td>
						   <td>{{$settlement->fee_ledger_name_processing}}</td>
						  <td>{{ number_format($settlement->fee_ledger_amount_processing, 2, '.', ',')}}</td>
						  <td>{{$settlement->status ?? 'N/A'}}</td>
                          <!--<td>
						  <a href="{{ url('tips/settlements/'. $settlement->id)}}" class="btn btn-success btn-sm">View</a>
                            <a href="{{ url('tips/settlements/'. $settlement->id .'/reverse')}}" class="btn btn-primary btn-sm">Reverse</a>
                            </td>-->
                        </tr>
                        @endforeach
                       </tbody>
                      </table>
                    </div>
                  </div>
                   <div class="card-footer text-right">
				   {{ $settlements->links() }}
                  </div>
                </div>
              </div>
            </div>

    </div><!-- sl-pagebody -->
@endsection
