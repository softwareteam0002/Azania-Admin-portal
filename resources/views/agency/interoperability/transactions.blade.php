@extends('layouts.admin')
@section('content')
    <!-- View Trnx Modal -->
    <div class="modal fade" id="viewTrnxModal" tabindex="-1" role="dialog" aria-labelledby="viewTrnxModalTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Transaction Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-left">
                                    <p>Transaction Date:</p>
                                    <h5 class="mt-n3 pb-2 start-date"></h5>

                                    <p>Terminal ID:</p>
                                    <h5 class="mt-n3 pb-2 terminal-id"></h5>

                                    <p>RRN:</p>
                                    <h5 class="mt-n3 pb-2 rrn"></h5>

                                    <p>From Account:</p>
                                    <h5 class="mt-n3 pb-2 from-account"></h5>
                                    <p>To Account:</p>
                                    <h5 class="mt-n3 pb-2 to-account"></h5>

                                </div>
                                <div class="col-md-4 text-center">

                                    <div class="card">
                                        <div class="card-body">
                                            <p class="">Transaction ID:</p>
                                            <h4 class="trx-id"></h4>
                                        </div>
                                    </div>

                                    <div class="card">
                                        <div class="card-body">
                                            <p>Transaction Status:</p>
                                            <h4 class="status"></h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-right">
                                    <p>Transaction Time:</p>
                                    <h5 class="mt-n3 pb-2 time"></h5>
                                    <p>Transaction Type:</p>
                                    <h5 class="mt-n3 pb-2 type"></h5>
                                    <p>Amount:</p>
                                    <h5 class="mt-n3 pb-2 amount"></h5>
                                    <p>Code:</p>
                                    <h5 class="mt-n3 pb-2 code"></h5>
                                    <p>is Reversed:</p>
                                    <h5 class="mt-n3 pb-2 reversed"></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning" id="reverseBtn">Reverse Transaction</button>
                </div>
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
            <li class="breadcrumb-item active">Transactions</li>
        </ol>
    </nav>

    <br>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="table-striped table-responsive table-hover">
                        <table id="intTrnxs">
                            <div class="text-center" id="loader">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
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
            var transactionData;
            $('.datatable').DataTable({});
            $('#loader').show();
            $.ajax({
                url: '/api/agency/interoperability/transactions',
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    $('#loader').hide();
                    transactionData = $('#intTrnxs').DataTable({
                        data: data.transactions,
                        columns: [
                            {
                                title: 'S/N', render: function (data, type, row, meta) {
                                    return meta.row + 1;
                                }
                            },
                            {title: 'Transaction Type', data: 'transaction_type'},
                            {title: 'Transaction ID', data: 'transaction_id'},
                            {
                                title: 'Transaction Date',
                                data: function (row) {
                                    return moment(row.created_at).format('YYYY-MM-DD HH:mm:ss A');
                                }
                            },
                            {title: 'Terminal ID', data: 'terminal_id'},
                            {
                                title: 'RRN',
                                data: function (row) {
                                    return row.rrn != null ? row.rrn : 'N/A'
                                }
                            },
                            {
                                title: 'Amount (TZS)',
                                data: function (row) {
                                    const formattedAmount = new Intl.NumberFormat('en-TZ', {
                                        style: 'currency',
                                        currency: 'TZS'
                                    }).format(row.amount);
                                    return formattedAmount.substring(3);
                                }
                            },
                            {
                                title: 'Code',
                                data: function (row) {
                                    return row.auth_code != null ? row.auth_code : 'N/A';
                                }
                            },
                            {
                                title: 'Status',
                                data: function (row) {
                                    if (row.response_code === 200) {
                                        return "SUCCESS";
                                    } else if (row.response_code == null) {
                                        return 'IN-PROGRESS';
                                    } else {
                                        return 'FAILED';
                                    }
                                }
                            },
                            {
                                title: 'Actions',
                                data: function (row) {
                                    return '<button type="button" class="btn btn-primary" id="view-trnx" ' +
                                        'data-toggle="modal"> <i ' +
                                        'class="fas ' +
                                        'fa-eye"></i></button>';
                                }
                            },
                            // Add more columns as needed
                        ],
                        createdRow: function (row, data) {
                            var statusBadge;
                            if (data.response_code == 200) {
                                statusBadge = '<span class="badge badge-success">SUCCESS</span>';
                            } else if (data.response_code === null) {
                                statusBadge = '<span class="badge badge-warning">IN-PROGRESS</span>';
                            } else {
                                statusBadge = '<span class="badge badge-danger">FAILED</span>';
                            }

                            $('td', row).eq(8).html(statusBadge);
                        }
                    });
                },
                error: function (error) {
                    $('#loader').hide();
                    console.error('Error fetching data:', error);
                }
            });

            $(document).on('click', '#view-trnx', function () {
                $('#viewTrnxModal').modal('show');
                var $tr = $(this).closest('tr');
                var rowData = transactionData.row($tr).data();
                var statusElement = document.querySelector('.status');

                document.querySelector('.terminal-id').textContent = rowData.terminal_id;
                document.querySelector('.type').textContent = rowData.transaction_type;
                document.querySelector('.amount').textContent = rowData.amount;
                document.querySelector('.code').textContent = rowData.auth_code;
                document.querySelector('.from-account').textContent = rowData.from_account;
                document.querySelector('.to-account').textContent = rowData.to_account;
                if (rowData.is_reversed === 1) {
                    document.querySelector('.reversed').textContent = 'Yes';
                } else {
                    document.querySelector('.reversed').textContent = 'No';
                }
                if (rowData.response_code == 200) {
                    statusElement.textContent = 'SUCCESS';
                    statusElement.classList.add('text-success');
                    statusElement.classList.remove('text-warning', 'text-danger');
                } else if (rowData.response_code == null) {
                    statusElement.textContent = 'IN-PROGRESS';
                    statusElement.classList.add('text-warning');
                    statusElement.classList.remove('text-success', 'text-danger');
                } else {
                    statusElement.textContent = 'FAILED';
                    statusElement.classList.add('text-danger');
                    statusElement.classList.remove('text-warning', 'text-success');
                }

                document.querySelector('.rrn').textContent = rowData.rrn ?? 'N/A';
                document.querySelector('.trx-id').textContent = rowData.transaction_id;
                document.querySelector('.start-date').textContent = moment(rowData.created_at).format('YYYY-MM-DD');
                document.querySelector('.time').textContent = moment(rowData.created_at).format('h:mm:ss A');

                if (rowData.response_code == 200 && rowData.transaction_code !== 'BI') {
                    $('#reverseBtn').show();
                } else {
                    $('#reverseBtn').hide();
                }

                $('#trxn-id').val(data[2]);
                $('#auth-code').val(data[8]);

            });

        </script>
    @endsection
@endsection
