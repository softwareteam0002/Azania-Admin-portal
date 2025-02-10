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
            <li class="breadcrumb-item"><a href="{{ url('/ib/institutions/index') }}">Institution Accounts</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/ib/institutions/accounts/'.$account -> id ) }}">Institution Account</a></li>
            <li class="breadcrumb-item active">Auth Matrix </li>
        </ol>
    </nav>

    <h5>Auth Matrix : {{ $account->accountName}} ( {{ $account->accountID }} )</h5>
    <hr/>

    <div class="card mb-5">
        <div class="card-header">
            <a class="card-link" data-toggle="modal" data-target="#myModal" href="javascript::void(0)">
                Create Service Auth Matrix
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class=" table-responsive-sm">
                <table class="table table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th>Account Number</th>
                        <th>Service</th>
                        <th>Matrix Role Description Lists</th>
                        <th>Signatories</th>
                        <th>Created By</th>
                        <th>Status</th>
                         <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($account_service_roles as $account_service_role)
                        <tr>
                            <td>{{$account_service_role->account_number ?? 'N/A'}}</td>
                            
                            @if($account_service_role->service)
                                <td>( {{$account_service_role->service->name ?? 'N/A' }} ) {{ $account_service_role->service->description ?? 'N/A' }}</td>
                            @else
                                <td>ALL Services</td>
                            @endif
                            
                            <td>
                                <h6>{{ $account_service_role->matrixRole->name }}</h6>
                                <dl>
                                    <dt>Is Sequencial</dt>
                                    <dd>- <span >@if($account_service_role->matrixRole->is_sequencial == 1) {{ 'YES' }} @else {{ 'NO' }} @endif </span></dd>
                                    <dt>Is Range</dt>
                                    <dd>- <span >@if($account_service_role->matrixRole->is_range == 1) {{ 'YES' }} @else {{ 'NO' }} @endif </span</dd>
                                    <dt>Is Any Of</dt>
                                    <dd>- <span >@if($account_service_role->matrixRole->is_any_of == 1) {{ 'YES' }} @else {{ 'NO' }} @endif </span</dd>
                                    <dt>Signatories</dt>
                                    <dd>- <span >{{ $account_service_role->matrixRole->signatories }}</span</dd>
                                    <dt>Min Amount</dt>
                                    <dd>- <span >{{ $account_service_role->matrixRole->min_amount }}</span</dd>
                                    <dt>Max Amount</dt>
                                    <dd>- <span >{{ $account_service_role->matrixRole->max_amount }}</span</dd>
                                </dl>     
                            </td>
                            <td>
                                <dl>
                                    @foreach($account_service_role->signatories as $signatory)
                                        <dt>{{ $signatory->user->name }}</dt>
                                    @endforeach
                                </dl>
                            </td>
                            <td>{{$account_service_role->creator->name ?? 'N/A'}}</td>
                             <td>
                                @if($account_service_role->deleted_at == NULL || empty($account_service_role->deleted_at))
                                    <span class="badge badge-success">Active</span>
                                @elseif(!empty($account_service_role->deleted_at))
                                    <span class="badge badge-danger">Disabled</span>
                                @endif

                            </td>
                            <td> 
                                <form action="{{ route('ib.auth_matrix.setup.disable', $account_service_role->id) }}" method="POST">
                                    @csrf
                                     @method('PUT')
                                <input type="hidden" name="id" value="{{ $account_service_role->id }}">
                                <button class="btn btn-warning btn-sm" title="Disable/Enable"><span><i class="fas fa-remove text-dark-pastel-green"></i></span</button>
                               </form>
                               </td>
                        </tr>

                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Service Auth Matrix Form</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <div class="card">
                    <div class="card-body">
                    <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
                    <p class="small text-danger">Approve order from top to bottom.</p>
                    <form action="{{ url('/ib/accounts/auth_matrix/setup/'.$account -> id ) }}" method="POST">
                        @csrf
                        @method('POST')
                        <input name="account_id" type="hidden" value="{{$account -> id}}"/>
                        <input name="account_number" type="hidden" value="{{$account -> accountID}}"/>
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="types">Service*</label>
                                    <select name="service_id" class="form-control">
                                        <option value="0" >-- Select Service --</option>
                                        <option value="-1" >Apply For ALL</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" > ( {{$service->name}} ) {{ $service->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="types">Matrix Role*</label>
                                    <select id="matrix_role_id" name="matrix_role_id" class="form-control">
                                        <option value="" >-- Select Matrix Role --</option>
                                        @foreach($matrix_roles as $role)
                                            <option data-signatories="{{ $role->signatories }}" data-is_any_of="@if($role->is_any_of == 1) {{ 'YES' }} @else {{ 'NO' }} @endif" data-is_sequencial="@if($role->is_sequencial == 1) {{ 'YES' }} @else {{ 'NO' }} @endif" data-is_range="@if($role->is_range == 1) {{ 'YES' }} @else {{ 'NO' }} @endif" data-min_amount="{{ $role->min_amount }}" data-max_amount="{{ $role->max_amount }}" value="{{ $role->id }}" > {{$role->name}} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">    
                            <div class="col-md-6 matrix-role-data">
                                <h6>Selected Matrix Role Description Lists</h6>    
                                <dl>
                                    <dt>Is Sequencial</dt>
                                    <dd>- <span id="is_sequencial"></span></dd>
                                    <dt>Is Range</dt>
                                    <dd>- <span id="is_range"></span</dd>
                                    <dt>Is Any Of</dt>
                                    <dd>- <span id="is_any_of"></span</dd>
                                    <dt>Signatories</dt>
                                    <dd>- <span id="signatories"></span</dd>
                                    <dt>Min Amount</dt>
                                    <dd>- <span id="min_amount"></span</dd>
                                    <dt>Max Amount</dt>
                                    <dd>- <span id="max_amount"></span</dd>
                                </dl>  
                            </div>
                            <div class="col-md-6">
                                <div class="table-responsive">
                                        <table class="table table-bordered">
                                        <thead>
                                            <tr style="background-color: #f9f9f9;">
                                                <th class="text-center" width="5%">Actions</th>
                                                <th class="text-left" width="95%">Approvers</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="approver-1">
                                                <td class="text-center" style="vertical-align: middle;">
                                                    <a class="btn btn-xs btn-danger remove-approver" data-id="approver-1" href="javascript::void(0)"><i
                                                        class="fa fa-trash"></i></a>
                                                </td>
                                                <td>
                                                    <select name="user_id[]" class="form-control">
                                                        <option value="" >-- Select Approvers --</option>
                                                        @foreach($institution_users as $user)
                                                            <option  value="{{ $user->id }}" > {{$user->name}} </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr id="addItem">
                                            <td class="text-center"><button type="button" id="button-add-item" data-toggle="tooltip"
                                                title="Add" class="btn btn-xs btn-primary" data-original-title="Add"><i
                                                    class="fa fa-plus"></i></button>
                                            </td>
                                            <td class="text-right" colspan="2">
                                                <input id="last_row_id" type="hidden" value="1"/>
                                            </td>
                                            </tr>
                                        </tbody>
                                        </table>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <hr/>
                                <button type="submit" class="btn btn-success btn-sm">Save</button>
                                <button type="reset" id="reset" class="btn btn-link btn-sm">Clear form</button>
                            </div>

                        </div>
                        
                    </form>
                </div>
                </div>
                
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

            </div>
        </div>
        </div>
@section('scripts')
    @parent
    <script>

        $(function () {
            $('.datatable').DataTable()

            $(".remove-approver").click(function(ev){
                let id_to_remove = $(this).data("id");
                $('.' + id_to_remove).remove();
            });

            $('#matrix_role_id').change(function(ev){
                $('#is_sequencial').html($(this).find(':selected').data('is_sequencial'));
                $('#is_range').html($(this).find(':selected').data('is_range'));
                $('#min_amount').html($(this).find(':selected').data('min_amount'));
                $('#max_amount').html($(this).find(':selected').data('max_amount'));
                $('#is_any_of').html($(this).find(':selected').data('is_any_of'));
                $('#signatories').html($(this).find(':selected').data('signatories'));
            });

            $('#matrix_role_id').trigger("change");

            $('#reset').click(function(){
                $('#is_sequencial').html('');
                $('#is_range').html('');
                $('#min_amount').html('');
                $('#max_amount').html('');
                $('#is_any_of').html('');
                $('#signatories').html('');
            });
        });

        $('#button-add-item').click(function(){
            let row_id = $('#last_row_id').val() + 1;
            $('#last_row_id').val(row_id);

            let html =      '<tr class="approver-'+ row_id +'">';
                    html += '<td class="text-center" style="vertical-align: middle;">';
                    html += '   <a class="btn btn-xs btn-danger remove-approver" data-id="approver-'+ row_id +'" href="javascript::void(0)">';
                    html += '       <i class="fa fa-trash"></i></a>';
                    html += '</td>';
                    html += '<td>';
                    html += '<select name="user_id[]" class="form-control">';
                    html +=     '<option value="" >-- Select Approvers Role --</option>';
                            @foreach($institution_users as $user)
                    html +=     '<option  value="{{ $user->id }}" > {{$user->name}} </option>';
                            @endforeach
                    html += '</select>';
                    html += '</td>';
                    html += '</tr>';
            $('#addItem').before(html);

            $(".remove-approver").click(function(ev){
                let id_to_remove = $(this).data("id");
                $('.' + id_to_remove).remove();
            });

        });

    </script>
@endsection
@endsection
