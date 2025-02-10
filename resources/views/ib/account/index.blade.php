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
        <hr/>
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small" style="background-color:#f9f9f9; padding: .55rem 1rem;">
            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
            <li class="breadcrumb-item active">Internet Banking accounts</li>
        </ol>
    </nav>


    <h5>Internet Banking accounts</h5>
    <hr/>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th width="10"></th>
                        <th>Account Number</th>
                        <th>Account Name</th>
                        <th>Account Type</th>
                        <th>Branch</th>
                        <th>Transaction Limit</th>
                        <th>Institution</th>
                        <th>Customer Name</th>
                        <th>Status</th>
                       <th>Initiator</th>
                      <th>Approver</th>
                        <th>Approval Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($accounts as $trxn)

                        <tr data-entry-id="{{$trxn->id}}" data-toggle="modal" data-target="#largeModal{{ $trxn->id }}">
                            <td></td>
                            <td>{{$trxn->accountID}}</td>
                            <td>{{$trxn->accountName}}</td>
                            {{--<td>{{$trxn->types->name ?? ''}}</td>--}}
                            <td>{{$trxn->productName ?? ''}}</td>
                            <td>{{$trxn->branchName ?? ''}}</td>
                            <td>{{$trxn->minAmount ?? ''}} - {{$trxn->maxAmount ?? ''}}</td>
                            <td> {{$trxn->institutions->institute_name ?? 'N/A' }}</td>
                            <td>{{$trxn->users->name ?? 'N/A' }}</td>
                            <td>
                                @if($trxn->aCStatus=="Active")
                                  <span class="badge badge-success">{{ $trxn->aCStatus }}</span>
                                  <!-- There is a logical error with the status texts -->
                                @else
                                  <span class="badge badge-danger">{{ $trxn->aCStatus }}</span>
                                @endif
                            </td>
                          <td>{{$trxn->initiators->name ?? '--' }}</td>
                          <td>{{$trxn->approvers->name ?? '--'}}</td>                           
                          <td>
                                @if($trxn->isWaitingApproval == 0)
                                    <span class="badge badge-success">Approved</span>
				@elseif($trxn->isWaitingApproval == 2)
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-danger">Pending Approval</span>
                                @endif
                            </td>

                            <td>

                                <div class="row">


                                    @if($trxn->aCStatus=="Active" && $trxn->isWaitingApproval == 0)
                                    <button class="btn btn-danger btn-sm" title="Block" onclick="handleActivateBlock('{{ url('ib/update_acc_status') }}','{{$trxn->accountID}}','{{$trxn->accountName}}','{{number_format($trxn->balance,2)}}/=','{{$trxn->productName}}','{{$trxn->branchName ?? ''}}','Blocked','{{$trxn->id}}')" data-toggle="modal"   data-target="#block_order">
                                        <i class="fas fa-warning text-dark-pastel-green"></i>
                                    </button>
                                    @endif


                                    @if($trxn->aCStatus=="Blocked" && $trxn->isWaitingApproval == 0)
                                        <button class="btn btn-primary btn-sm" title="Activate" onclick="handleActivateBlock('{{ url('ib/update_acc_status') }}','{{$trxn->accountID}}','{{$trxn->accountName}}','{{number_format($trxn->balance,2)}}/=','{{$trxn->productName}}','{{$trxn->branchName ?? ''}}','Active','{{$trxn->id}}')" data-toggle="modal"   data-target="#block_order">
                                            <i class="fas fa-check text-dark-pastel-blue"></i>
                                        </button>
                                    @endif
                                         <form action="{{ url('ib/requests/actions') }}" method="POST">
                                     @csrf
                                     @method('PUT')

                                     <input type="hidden" name="action" value="Edit Accounts">
                                     <input type="hidden" name="request_type" value="ib_accounts_settings">
                                     <input type="hidden" name="module" value="IB">
                                     <input type="hidden" name="initiator_id" value="{{ $trxn->initiator_id }}">
                                     <input type="hidden" name="approver_id" value="{{ Auth::user()->id }}">
                                     <input type="hidden" name="id" value="{{ $trxn->id }}">
                                     @if(Auth::user()->action_id == 1 || Auth::user()->action_id == 3)
                                     <a href="{{ url('ib/accounts/edit/'. $trxn->id ) }}" class="btn btn-sm btn-primary">
                                         <span><i class="fas fa-edit text-dark-pastel-green"></i></span>
                                     </a> 
                                      @endif                            
				     @if((auth()->user()->id != $trxn->disabledBy_id) && $trxn->isBlocked == 1 && $trxn->isWaitingApproval == 1)
                                  <a  href="{{ route('ib.accounts.disableApproval', $trxn->id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-warning btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-yellow"></i></span>
                                  </a>    
                                    @endif

                                     @if(Auth::user()->id != $trxn->initiator_id && $trxn->isWaitingApproval==1 && $trxn->isBlocked == 0)
                                  <a  href="{{ route('ib.account.approve', $trxn->id) }}" type="submit" name="submit_value" value="approve"  class="btn btn-success btn-sm">
                                    <span><i class="fas fa-check text-dark-pastel-green"></i></span>
                                  </a>    

                                     @endif

                                 </form>


                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    @include('ib.dialogs.block_dialog')

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.datatable').DataTable();
        } );


    </script>
@endsection

@endsection

<script>
    function handleActivateBlock(url,account,username,balance,type,branch,status_value,id)
    {
        var form = document.getElementById('blockOrder');
        var action_plan="";
        if(status_value=="Blocked")
        {
            action_plan="BLOCKING";
        }
        else if(status_value=="Active")
        {
            action_plan="ACTIVATION";
        }
        else{
            action_plan="";
        }
        var htmlBody = "<h5>CONFIRM "+action_plan+"</h5>\n" +
            "    <hr/>\n" +
            "\n" +
            "    <div class=\"card\">\n" +
            "        <div class=\"card-body\">\n" +
            "            <h6 class=\"mb-3\">Request Details</h6>\n" +
            "            <div class=\"row\">\n" +
            "                <div class=\"col-md-6\">\n" +
            "                    <table class=\"table table-sm\">\n" +
            "                        <tbody>\n" +
            "                            <tr><td width=\"200px\" class=\"font-weight-bold\">ACCOUNT NUMBER:</td><td>"+ account +"</td></tr>\n" +
            "                            <tr><td>NAME:</td><td>"+ username +"</td></tr>\n" +
            "                            <tr><td>TYPE:</td><td>"+ type +"</td></tr>\n" +
            "                            <tr><td>BALANCE:</td><td>"+ balance +"</td></tr>\n" +
            "                            <tr><td>BRANCH:</td><td>"+ branch +"</td></tr>\n" +
            "                        </tbody>\n" +
            "                    </table>\n" +
            "                    <hr/>\n" +
            "                </div>\n" +
            "            </div>\n" +
            "        </div>\n" +
            "    </div>"
        var text = document.getElementById('blockingText').innerHTML = htmlBody;
        var status = document.getElementById('aCStatus').value = status_value;
        var id = document.getElementById('account_id').value=id;

        form.action = url;
    }
</script>
