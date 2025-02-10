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
            <li class="breadcrumb-item active">Fixed Deposit Rates(FDR)</li>
        </ol>
    </nav>


    <h5>Fixed Deposit Rates(FDR)</h5>
    <hr/>

    <div class="card mb-4">
        <div class="card-body">
            <h6>Add a Fixed Deposit Rate</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form action="{{ url('ib/fdr/store') }}" method="POST">
                @csrf
                @method('POST')
                <p class="small text-primary">Amount Range.</p>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">From Amount:<span class="text-danger">*</span></label>
                            <input type="text" name="from_amount" class="form-control form-control-sm number" placeholder="Enter Amount." required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">To Amount:<span class="text-danger">*</span></label>
                            <input type="text" name="to_amount" class="form-control form-control-sm number" placeholder="Enter Amount." required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="types">Tenure</label>
                            <select name="tenure" class="form-control form-control-sm number">
                                @foreach($tenures as $tenure)
                                    <option value="{{ $tenure }}">{{ $tenure }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Rate:<span class="text-danger">*</span></label>
                            <input type="text" name="rate" class="form-control form-control-sm number" placeholder="Enter Rate" required>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12">
                        <hr/>
                        <button type="submit" class="btn btn-success btn-sm">Save</button>
                        <button type="reset" class="btn btn-link btn-sm">Clear form</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <h5>All Fixed Deposit Rates</h5>

    <hr/>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th width="10"></th>
                        <th>Tenure</th>
                        <th>Rate</th>
                        <th>Interval Amount</th>
                        <th>Amount Limit</th>
                        <th>Approval Status</th>
                        <th>Initiator</th>
                        <th>Approver</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($fdrs as $fdr)
                        <tr>

                            <td></td>
                            <td>{{$fdr->tenure_of_term_deposit}}</td>
                            <td>{{$fdr->existing_rates ?? 'N/A'}}</td>
                            <td>{{ number_format($fdr->amount_interval, 2,'.',',') ?? 'N/A' }} </td>
                             <td>{{ number_format($fdr->amount_limit, 2,'.',',') ?? 'N/A' }} </td>
                            <td>
                                @if($fdr->isWaitingApproval == 0)
                                    <span class="badge badge-success">Approved</span>
                                @elseif($fdr->isWaitingApproval == 1)
                                    <span class="badge badge-danger">Pending Approval</span>
                                @endif
                            </td>
                            <td>
                                {{ $fdr->initiators->name ?? '--' }}
                            </td>
                            <td>
                                {{ $fdr->approvers->name ?? '--' }}
                            </td>
                            <td>



                                    <form action="{{ url('ib/requests/actions') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="id" value="{{ $fdr->id }}">
                                        <input type="hidden" name="approver_id" value="{{ Auth::user()->id }}">
                                        <input type="hidden" name="request_type" value="fdr">
										 @if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)
                                        <a href="{{ url('ib/fdr/edit',[$fdr->id])}}" class="btn btn-sm btn-primary"
                                           title="Edit FDR">
                                            <span><i class="fas fa-edit text-dark-pastel-green"></i></span>
                                        </a>
										@endif
                                        @if(Auth::user()->id != $fdr->initiator_id && $fdr->isWaitingApproval==1)
                                        <button type="submit" name="submit_value" value="approved" title="Approve" class="btn btn-success btn-sm"><span><i class="fas fa-check text-dark-pastel-green"></i></span></button>
                                        <button type="submit" name="submit_value" value="rejected" title="Reject"  class="btn btn-danger btn-sm"><span><i class="fas fa-remove text-dark-pastel-green"></i></span></button>
                                        @endif
                                    </form>


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
            $('.datatable').DataTable({
        'ordering': false,
        'sorting': false
    });
        } );
    </script>

    <script>
          $('input.number').keyup(function(event) {

        // skip for arrow keys
        if(event.which >= 37 && event.which <= 40) return;

        // format number
        $(this).val(function(index, value) {
          return value
          .replace(/\D/g, "")
          .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
          ;
        });
      });
    </script>
@endsection

@endsection
