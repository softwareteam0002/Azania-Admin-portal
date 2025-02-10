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
            <li class="breadcrumb-item active">Matrix Roles</li>
        </ol>
    </nav>

    <h5>Matrix Roles</h5>
    <hr/>

    <div class="card mb-5">
        <div class="card-body">
            <h6>Create Matrix Role form</h6>
            <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
            <form id="matrix-form" action="{{ url('ib/view_matrix_role') }}" method="POST">
                @csrf
                @method('POST')
                <div class="row">

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Role name:</label>
                            <input type="text" id="name" name="name" class="form-control form-control-sm" placeholder="Enter role name here.">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_sequencial" name="is_sequencial" value="1">
                            <label class="form-check-label" for="is_sequencial">Is Sequential</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_range" name="is_range" value="1">
                            <label class="form-check-label" for="is_range">Is Range</label>
                        </div>
                    </div>
                    <div class ="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_any_of" name="is_any_of" value="1">
                            <label class="form-check-label" for="is_any_of">Is Any Of</label>
                        </div>
                    </div>
                    <div class="col-md-3 range_control">
                        <div class="form-group">
                            <label for="min_amount">Min Amount :</label>
                            <input id="min_amount"  type="number" min="1" name="min_amount" class="form-control form-control-sm" placeholder="Enter minimum amount code here.">
                        </div>
                    </div>
                    <div class="col-md-3 range_control" >
                        <div class="form-group ">
                            <label for="max_amount">Max Amount :</label>
                            <input id="max_amount" type="number" min="1" name="max_amount" class="form-control form-control-sm" placeholder="Enter maximum amount here.">
                        </div>
                    </div>
                    <div class="col-md-3 any_of_control">
                        <div class="form-group">
                            <label for="signatories">Any Of How Many :</label>
                            <input id="signatories" type="number" min="1" name="signatories" class="form-control form-control-sm" placeholder="Enter Number Of Signatories here.">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <hr/>
                        <button id="submit"  type="submit" class="btn btn-success btn-sm">Save</button>
                        <button id="reset" type="reset" class="btn btn-link btn-sm">Clear form</button>
                    </div>

                </div>
                <div id="edit-append">
                </div>
            </form>
        </div>
    </div>
    <h5>All Matrix Roles</h5>
    <hr/>

    <div class="card">
        <div class="card-body">
            <div class=" table-responsive-sm">
                <table class="table table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th width="10">
                        </th>
                        <th>Role name</th>
                        <th>Is Sequential</th>
                        <th>Is Range</th>
                        <th>Is Any Of</th>
                        <th>Signatories</th>
                        <th>Min Amount</th>
                        <th>Max Amount</th>
                        <th>Created By</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($matrixRoles as $role)

                        <tr>
                            <td></td>
                            <td>{{$role->name ?? 'N/A'}}</td>
                            <td>@if($role->is_sequencial == 1) {{ 'YES' }} @else {{ 'NO' }} @endif</td>
                            <td>@if($role->is_range == 1) {{ 'YES' }} @else {{ 'NO' }} @endif</td>
                            <td>@if($role->is_any_of == 1) {{ 'YES' }} @else {{ 'NO' }} @endif</td>
                            <td>{{$role->signatories ?? 'N/A'}}</td>
                            <td>{{$role->min_amount ?? 'N/A'}}</td>
                            <td>{{$role->max_amount ?? 'N/A'}}</td>
                            <td>{{$role->creator->name ?? 'N/A'}}</td>
                            <td>
                                @can('ib_settings_natrix_role_edit')

                                @endcan
                                <button class ="btn btn-sm btn-warning btn-matrix-role-edit" 
                                data-id="{{ $role->id }}"
                                data-name="{{ $role->name }}"
                                data-is_sequencial = "{{ $role->is_sequencial }}"
                                data-is_range = "{{ $role->is_range }}"
                                data-signatories = "{{ $role->signatories }}"
                                data-is_any_of = "{{ $role->is_any_of }}"
                                data-min_amount = "{{ $role->min_amount }}"
                                data-max_amount = "{{ $role->max_amount }}"
                                >
                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                </button>
                                @can('ib_settings_natrix_role_delete')
                                    
                                @endcan
                                <form class="form-delete" action="{{ url('ib/view_matrix_role/delete') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $role->id }}" />
                                        <button class ="btn btn-sm btn-danger">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
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
@section('scripts')
    @parent
    <script>
        $(function () {
            $('.datatable').DataTable()
            $('#is_range').trigger('change');
            $('#is_any_of').trigger('change');

            $('.btn-matrix-role-edit').click(function(e){
                //console.log($(this).data('id'));
                $("#matrix-form").prop('action', '{{ url("ib/view_matrix_role/update") }}');
                $("#edit-append").html('');
                $("#edit-append").html('<input type="hidden" name="id" value="'+ $(this).data('id') +'" />');

                if($(this).data('is_sequencial') == 1){ $('#is_sequencial').prop('checked', true); $('#is_sequencial').trigger('change'); }
                else{ $('#is_sequencial').prop('checked', false); $('#is_sequencial').trigger('change');}

                if($(this).data('is_range') == 1)
                {
                    $('#is_range').prop('checked', true) ;
                    $('#is_range').trigger('change');

                    $('#max_amount').val($(this).data('max_amount'));
                    $('#min_amount').val($(this).data('min_amount'));
                }
                else
                {
                    $('#is_range').prop('checked', false) ;
                    $('#is_range').trigger('change');
                    $('#max_amount').val(null);
                    $('#min_amount').val(null);
                }

                if($(this).data('is_any_of') == 1)
                {
                    $('#is_any_of').prop('checked', true) ;
                    $('#is_any_of').trigger('change');

                    $('#signatories').val($(this).data('signatories'))
                }
                else
                {
                    $('#is_any_of').prop('checked', false) ;
                    $('#is_any_of').trigger('change');
                    $('#signatories').val(null)
                }
                
                $('#submit').html('Update');
                $('#name').val($(this).data('name'));
                
            });
        });

        $('#is_range').change(function() {
            if(this.checked) {
                $('.range_control').css({
                    'display' : 'flex'
                })
            } else {
                $('.range_control').css({
                    'display' : 'none'
                })
            }
            //$('#textbox1').val(this.checked);        
        });

        
        $('#is_sequencial').change(function() {
            if(this.checked) {
                if($('#is_any_of').prop('checked') == true)
                {
                    $('#is_any_of').prop('checked', false);
                    $('#is_any_of').trigger('change');
                }
            }
        });

        $('#is_any_of').change(function() {
            if(this.checked) {
                $('.any_of_control').css({
                    'display' : 'flex'
                })

                if($('#is_sequencial').prop('checked') == true)
                {
                    $('#is_sequencial').prop('checked', false);
                    $('#is_sequencial').trigger('change');
                }

            } else {
                $('.any_of_control').css({
                    'display' : 'none'
                })
            }
                    
        });

    </script>
@endsection
@endsection

