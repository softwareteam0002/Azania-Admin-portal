@extends('layouts.admin')
@section('content')
    <!-- Edit Charges Modal -->
    <div class="modal fade" id="editWithdrawCharge" tabindex="-1" role="dialog"
         aria-labelledby="editWithdrawChargeTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Edit Charge</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="update/service_account" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="id" name="id" class="form-control form-control-sm">
                        <div class="form-group">
                            <label for="name">Account Name:<span class="text-danger">*</span></label>
                            <input type="text" id="account_name" name="account_name" class="form-control
                            form-control-sm">
                        </div>
                        <div class="form-group">
                            <label for="name">Account Number:<span class="text-danger">*</span></label>
                            <input type="text" id="account_number" name="account_number" class="form-control
                            form-control-sm">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Account</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- Status Service Account Modal -->
    <div class="modal fade" id="statusAccountModal" tabindex="-1" role="dialog"
         aria-labelledby="statusAccountModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Confirm</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="status/service_account" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="service_account_id" name="service_account_id"
                               class="form-control form-control-sm">
                        <p class="status"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Yes</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
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
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small" style="background-color:#f9f9f9; padding: .55rem 1rem;">
            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
            <li class="breadcrumb-item">Agency Interoperability</li>
            <li class="breadcrumb-item active">Service Accounts</li>
        </ol>
    </nav>

    <br>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title text-center">Interoperability Service Accounts</h5>
                        </div>
                    </div>
                        <div class="table-responsive">
                            <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                                <thead>
                                <tr>
                                    <th width="10">S/N</th>
                                    <th>Account Name</th>
                                    <th>Account Number</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($serviceAccounts as $key => $serviceAccount)
                                    <tr>
                                        <td>{{$key + 1}}</td>
                                        <td>{{ $serviceAccount->account_name}}</td>
                                        <td>{{ $serviceAccount->account_number }} </td>
                                        @if($serviceAccount->is_active == 1)
                                            <td><span class="badge badge-success">Active</span></td>
                                        @else
                                            <td><span class="badge badge-danger">Inactive</span></td>
                                        @endif

                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm editWithdrawCharge"
                                                    title="Edit Account"><i class="fas fa-edit"></i></button>
                                            @if($serviceAccount->is_active == 1)
                                                <button type="button" class="btn btn-danger btn-sm statusAccount"
                                                        title="Deactivate Account">
                                                    <i class="fas fa-lock"></i></button>
                                            @else
                                                <button type="button" class="btn btn-success btn-sm statusAccount"
                                                        title="Activate
                                        Account"><i class="fas fa-unlock"></i></button>
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        @parent
        <script>
            $('.datatable').DataTable({});

            // //edit withdraw charge
            $(document).ready(function () {
                $('.editWithdrawCharge').on('click', function () {
                    $('#editWithdrawCharge').modal('show');

                    $tr = $(this).closest('tr');
                    var data = $tr.children("td").map(function () {
                        return $(this).text();
                    }).get();

                    $('#id').val(data[0]);
                    $('#account_name').val(data[1]);
                    $('#account_number').val(data[2]);
                });
            });
            //activate/deactivate service account
            $(document).ready(function () {
                $('.statusAccount').on('click', function () {

                    $('#statusAccountModal').modal('show');

                    $tr = $(this).closest('tr');
                    var data = $tr.children("td").map(function () {
                        return $(this).text();
                    }).get();

                    var statusElement = document.querySelector('.status');

                    if (data[3] === 'Active') {
                        statusElement.textContent = 'Are you sure you want to deactivate this account?';
                    } else {
                        statusElement.textContent = 'Are you sure you want to activate this account?';
                    }

                    $('#service_account_id').val(data[0]);
                });
            });
        </script>

    @endsection

@endsection
