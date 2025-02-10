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
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="id" name="id" class="form-control form-control-sm">
                        <div class="form-group">
                            <label for="name">From Amount:<span class="text-danger">*</span></label>
                            <input type="text" id="from_amount" name="from_amount" class="form-control form-control-sm">
                        </div>
                        <div class="form-group">
                            <label for="name">To Amount:<span class="text-danger">*</span></label>
                            <input type="text" id="to_amount" name="to_amount" class="form-control form-control-sm">
                        </div>
                        <div class="form-group">
                            <label for="name">Charge:<span class="text-danger">*</span></label>
                            <input type="text" id="charge" name="charge" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Charge</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- View Batch Entries Modal -->
    <div class="modal fade" id="viewBatchEntries" tabindex="-1" role="dialog"
         aria-labelledby="viewBatchEntriesTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">View Batch Entries</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST">

                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-responsive-sm table-striped table-sm table-hover">
                                <thead>
                                <tr>
                                    <th width="10">S/N</th>
                                    <th>From Amount (TZS)</th>
                                    <th>To Amount (TZS)</th>
                                    <th>Charge (TZS)</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody id="batchEntry"></tbody>
                            </table>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- approve/reject  Account Modal -->
    <div class="modal fade" id="approveAccountModal" tabindex="-1" role="dialog"
         aria-labelledby="approveAccountModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Confirm</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="approve/batch" method="post">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="approval" name="approval" class="form-control form-control-sm">
                        <input type="hidden" id="action" name="action" class="form-control form-control-sm">
                        <p class="approve"></p>
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
            <li class="breadcrumb-item active">Charges</li>
        </ol>
    </nav>

    <br>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Upload Interoperability Charges</h5>
                    <form action="{{route('interoperability.upload')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="">Charge Type: <small class="text-danger">*</small></label>
                            <select class="form-control" name="charge_type" required>
                                <option value="2" selected>Cash Deposit Charges</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Upload Charge File:</label>
                            <small class="text-danger mb-2">Accepted formats are .xls,.xlsx</small>
                            <input type="file" accept=".xls, .xlsx" class="form-control-file" name="file">
                        </div>
                        <div class="row">
                            <button type="submit" class="btn btn-primary btn-md mx-3">Upload</button>
                    </form>
                    <form action="{{route('templates.download')}}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-md"><i class="fas fa-download"></i> Download
                            Template
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="charge-batch-tab" data-toggle="pill" href="#charge-batch" role="tab"
                       aria-controls="charge-batch" aria-selected="false">Charge Batches</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="cash-deposit-tab" data-toggle="pill" href="#cash-deposit" role="tab"
                       aria-controls="cash-deposit" aria-selected="false">Cash Deposit Charges</a>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <!-- Charge Batch Tab -->
                <div class="card-body tab-pane fade show active" id="charge-batch" role="tabpanel"
                     aria-labelledby="charge-batch-tab">
                    <!-- Content for Charge Batch Tab -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-center">Charge Batches</h5>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                            <thead>
                            <tr>
                                <th width="10">S/N</th>
                                <th>Batch Number</th>
                                <th>Charge Type</th>
                                <th>Status</th>
                                <th>Approval</th>
                                <th>Created By</th>
                                <th>Approved By</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($agencyChargeBatches as $key => $agencyChargeBatch)
                                <tr data-database-id="{{$agencyChargeBatch->uuid}}">
                                    <td>{{$key + 1}}</td>
                                    <td>{{$agencyChargeBatch->batch_number }}</td>
                                    @if($agencyChargeBatch->charge_type == 1)
                                        <td>Withdraw Charges</td>
                                    @elseif($agencyChargeBatch->charge_type == 2)
                                        <td>Deposit Charges</td>
                                    @else
                                        <td>Government Levy</td>
                                    @endif
                                    @if($agencyChargeBatch->is_active == 1)
                                        <td><span class="badge badge-success">Active</span></td>
                                    @else
                                        <td><span class="badge badge-danger">Inactive</span></td>
                                    @endif

                                    @if($agencyChargeBatch->is_waitingApproval == 1)
                                        <td><span class="badge badge-warning">Waiting Approval</span></td>
                                    @elseif($agencyChargeBatch->is_waitingApproval == 2)
                                        <td><span class="badge badge-danger">Rejected</span></td>
                                    @else
                                        <td><span class="badge badge-success">Approved</span></td>
                                    @endif

                                    <td>{{$agencyChargeBatch->initiator->name}}</td>
                                    <td>{{$agencyChargeBatch->approver->name ?? 'N/A'}}</td>
                                    <td>{{ \Carbon\Carbon::parse($agencyChargeBatch->created_at)->format('Y-m-d H:i A') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm viewBatch"
                                                title="View">
                                            <i class="fas fa-eye"></i></button>
                                        @if($agencyChargeBatch->is_waitingApproval == 0)
                                            @if($agencyChargeBatch->is_active == 1)
                                                <button type="button" class="btn btn-danger btn-sm InactiveBatch"
                                                        title="Deactivate Account">
                                                    <i class="fas fa-lock"></i></button>
                                            @else
                                                <button type="button" class="btn btn-success btn-sm ActiveBatch"
                                                        title="Activate Account"><i class="fas fa-unlock"></i></button>
                                            @endif
                                        @endif

                                        @if($agencyChargeBatch->is_waitingApproval == 1)
                                            <button type="button" class="btn btn-success btn-sm approveAccount"
                                                    title="Approve"><i class="fas fa-check"></i></button>

                                            <button type="button" class="btn btn-danger btn-sm rejectAccount"
                                                    title="Reject"><i class="fas fa-times"></i></button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
              

                <!-- Cash Deposit Charges Tab -->
                <div class="tab-pane fade" id="cash-deposit" role="tabpanel" aria-labelledby="cash-deposit-tab">
                    <!-- Content for Cash Deposit Charges Tab -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-center">Cash Deposit Charges</h5>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                            <thead>
                            <tr>
                                <th width="10">S/N</th>
                                <th>From Amount (TZS)</th>
                                <th>To Amount(TZS)</th>
                                <th>Charge (TZS)</th>
                                <th>Status</th>
                                <th>Approval</th>
                                <th>Added By</th>
                                <th>Approved By</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($agencyDepositCharges as $key => $agentsDepositCharge)
                                <tr>
                                    <td>{{$key + 1}}</td>
                                    <td>{{ number_format($agentsDepositCharge->from_amount) }}</td>
                                    <td>{{ number_format($agentsDepositCharge->to_amount) }} </td>
                                    <td>{{ number_format($agentsDepositCharge->charge) }} </td>
                                    @if($agentsDepositCharge->is_active == 1)
                                        <td><span class="badge badge-success">Active</span></td>
                                    @else
                                        <td><span class="badge badge-danger">Inactive</span></td>
                                    @endif

                                    @if($agentsDepositCharge->is_waitingApproval == 1)
                                        <td><span class="badge badge-warning">Waiting Approval</span></td>
                                    @elseif($agentsDepositCharge->is_waitingApproval == 2)
                                        <td><span class="badge badge-danger">Rejected</span></td>
                                    @else
                                        <td><span class="badge badge-success">Approved</span></td>
                                    @endif

                                    <td>{{$agentsDepositCharge->initiator->name}}</td>
                                    <td>{{$agentsDepositCharge->approver->name ?? 'N/A'}}</td>

                                    <td>
                                        <button type="button" class="btn btn-primary editWithdrawCharge">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
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

    @section('scripts')
        @parent
        <script>
            $('.datatable').DataTable({});

            //edit withdraw charge
            $(document).ready(function () {
                $('.editWithdrawCharge').on('click', function () {
                    $('#editWithdrawCharge').modal('show');

                    $tr = $(this).closest('tr');
                    var data = $tr.children("td").map(function () {
                        return $(this).text();
                    }).get();

                    $('#id').val(data[0]);
                    $('#from_amount').val(data[1]);
                    $('#to_amount').val(data[2]);
                    $('#charge').val(data[3]);
                });
            });

            //view batch
            $(document).on('click', '.viewBatch', function () {
                $('#viewBatchEntries').modal('show');

                $tr = $(this).closest('tr');
                var data = $tr.children("td").map(function () {
                    return $(this).text();
                }).get();
                var batchNo = data[1];
                console.log(batchNo);

                $.ajax({
                    type: 'POST',
                    url: '/api/agency/interoperability/get_entries',
                    data: {batchNo: batchNo},
                    success: function (response) {
                        var tableBody = $('#batchEntry');
                        tableBody.empty();

                        // Populate the table body with the charges data
                        $.each(response.charges, function (index, charge) {
                            var badgeClass = charge.status === 'Active' ? 'badge-success' : 'badge-danger';
                            var rowHtml = '<tr>' +
                                '<td>' + (index + 1) + '</td>' +
                                '<td>' + charge.from_amount + '</td>' +
                                '<td>' + charge.to_amount + '</td>' +
                                '<td>' + charge.charge + '</td>' +
                                '<td><span class="badge ' + badgeClass + '">' + charge.status + '</span></td>' +
                                '</tr>';

                            // Append the row to the table body
                            tableBody.append(rowHtml);
                        });

                    },
                    error: function (error) {
                        // Handle the error case
                        console.error('Error fetching data:', error);
                    }
                });
            });
            //approve modal
            $(document).on('click', '.approveAccount', function () {
                $('#approveAccountModal').modal('show');
                $tr = $(this).closest('tr');
                var data = $tr.children("td").map(function () {
                    return $(this).text();
                }).get();

                var statusElement = document.querySelector('.approve');
                var databaseId = $tr.data('database-id');
                var action = 1;
                if (data[4] === 'Waiting Approval') {
                    statusElement.textContent = 'Are you sure you want to approve this batch?';
                }

                $('#approval').val(databaseId);
                $('#action').val(action);
            });

            //reject batch
            $(document).on('click', '.rejectAccount', function () {
                $('#approveAccountModal').modal('show');

                $tr = $(this).closest('tr');
                var data = $tr.children("td").map(function () {
                    return $(this).text();
                }).get();

                var statusElement = document.querySelector('.approve');
                var databaseId = $tr.data('database-id');
                var action = 2;
                if (data[4] === 'Waiting Approval') {
                    statusElement.textContent = 'Are you sure you want to reject this batch?';
                }

                $('#approval').val(databaseId);
                $('#action').val(action);
            });

            //activate batch
            $(document).on('click', '.ActiveBatch', function () {
                $('#approveAccountModal').modal('show');

                $tr = $(this).closest('tr');
                var data = $tr.children("td").map(function () {
                    return $(this).text();
                }).get();
                console.log(data);
                var statusElement = document.querySelector('.approve');
                var databaseId = $tr.data('database-id');
                var action = 3;

                if (data[3] === 'Inactive') {
                    statusElement.textContent = 'Are you sure you want to activate this batch?';
                }

                $('#approval').val(databaseId);
                $('#action').val(action);
            });
            //deactivate batch
            $(document).on('click', '.InactiveBatch', function () {
                $('#approveAccountModal').modal('show');

                $tr = $(this).closest('tr');
                var data = $tr.children("td").map(function () {
                    return $(this).text();
                }).get();
                console.log(data);
                var statusElement = document.querySelector('.approve');
                var databaseId = $tr.data('database-id');
                var action = 3;

                if (data[3] === 'Active') {
                    statusElement.textContent = 'Are you sure you want to deactivate this batch?';
                }

                $('#approval').val(databaseId);
                $('#action').val(action);
            });

        </script>
        <script>
            // Function to format numbers with thousands separators and limit to 2 decimal places
            function formatNumberWithCommasAndDecimals(number) {
                return number.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                });
            }

            // Function to handle input change for amount fields
            function handleAmountInput(inputId) {
                const inputElement = document.getElementById(inputId);
                const value = inputElement.value.replace(/,/g, ''); // Remove existing commas
                const floatValue = parseFloat(value);

                if (!isNaN(floatValue)) {
                    // Format the number with thousands separators and limit to 2 decimal places
                    inputElement.value = formatNumberWithCommasAndDecimals(floatValue);
                } else {
                    // Clear the input if it's not a valid number
                    inputElement.value = '';
                }
            }

            // Event listeners for the amount fields
            document.getElementById('from_amount').addEventListener('input', () => {
                handleAmountInput('from_amount');
            });

            document.getElementById('to_amount').addEventListener('input', () => {
                handleAmountInput('to_amount');
            });

            document.getElementById('charge').addEventListener('input', () => {
                handleAmountInput('charge');
            });
        </script>

    @endsection

@endsection
