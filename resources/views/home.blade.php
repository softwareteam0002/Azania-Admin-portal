@extends('layouts.admin')
@section('content')
    <div class="content">
        <div class="row">
            <!-- Users Card -->
            <div class="col-xl-4 col-md-6">
                <div class="card bg-primary text-white mb-4">
                    <div class="card-header">Users</div>
                    <div class="card-body"><span id="users">0</span></div>
                </div>
            </div>
            <!-- Agents Card -->
            <div class="col-xl-4 col-md-6">
                <div class="card bg-warning text-white mb-4">
                    <div class="card-header">Agents</div>
                    <div class="card-body"><span id="agents">0</span></div>
                </div>
            </div>
            <!-- Devices Card -->
            <div class="col-xl-4 col-md-6">
                <div class="card bg-danger text-white mb-4">
                    <div class="card-header">Devices</div>
                    <div class="card-body"><span id="devices">0</span></div>
                </div>
            </div>
        </div>

        <!-- Transactions Data -->
        <div class="row">
            <div class="col-md-12">
                <h5>Total Transactions - <span id="transactions_total">0</span></h5>
                <hr/>
            </div>

            <!-- Transactions by Type Chart -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <canvas id="transactions_by_type_chart"></canvas>
                        <h6 class="text-center mt-2">Transactions by Type</h6>
                    </div>
                </div>
            </div>

            <!-- Transactions by Channel and Status -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <canvas id="transactions_by_status_chart"></canvas>
                        <h6 class="text-center mt-2">Transactions by Status</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Chart.js -->
    <script  src="{{ asset('public/js/Chart.min.js') }}"></script>

    @section('scripts')
        @parent
        <script>
            $(document).ready(function () {
                // Fetch transactions data via AJAX
                $.ajax({
                    url: '{{ route("all.transactions") }}',
                    method: 'GET',
                    success: function (response) {
                        var trxns = response;
                        $("#transactions_total").html(trxns.length.toLocaleString());

                        // Variables for each chart
                        var trxns_count_IB = 0, trxns_count_MB = 0, trxns_count_AB = 0;
                        var trxns_count_UTILITY = 0, trxns_count_MINISTATEMENT = 0, trxns_count_BANKTRANSFER = 0,
                            trxns_count_BALANCEINQUIRY = 0, trxns_count_REVERSAL = 0, trxns_count_FULLSTATEMENT = 0,
                            trxns_count_B2W = 0, trxns_count_DEPOSIT = 0, trxns_count_WITHDRAWAL = 0;
                        var trxns_count_SUCCESS = 0, trxns_count_FAILED = 0, trxns_count_ONPROGRESS = 0;

                        // Parse transaction data
                        trxns.forEach(function (trxn) {
							console.log(trxn);
                            // By Type
                            switch (true) {
                                case trxn.trxn_name.startsWith('UTILITY'):
                                    trxns_count_UTILITY++;
                                    break;
                                case trxn.trxn_name.startsWith('BANKTOWALLET'):
                                    trxns_count_B2W++;
                                    break;
                                case  trxn.trxn_name === 'TIPS-MNO':
                                    trxns_count_B2W++;
                                    break;
                                case trxn.trxn_name === 'MINISTATEMENT':
                                    trxns_count_MINISTATEMENT++;
                                    break;
                                case trxn.trxn_name === 'BANKTRANSFER':
                                    trxns_count_BANKTRANSFER++;
                                    break;
                                case trxn.trxn_name === 'TIPS-BANK':
                                    trxns_count_BANKTRANSFER++;
                                    break;
									case trxn.trxn_name === 'PAYMENT~11':
                                    trxns_count_UTILITY++;
                                    break;
                                case trxn.trxn_name === 'BALANCE_INQUIRY':
                                    trxns_count_BALANCEINQUIRY++;
                                    break;
									case trxn.trxn_name === 'BALANCE_INQUIRY_CARD':
                                    trxns_count_BALANCEINQUIRY++;
                                    break;
                                case trxn.trxn_name === 'REVERSAL':
                                    trxns_count_REVERSAL++;
                                    break;
                                case trxn.trxn_name === 'FULLSTATEMENT':
                                    trxns_count_FULLSTATEMENT++;
                                    break;
                                case trxn.trxn_name === 'DEPOSIT':
                                    trxns_count_DEPOSIT++;
                                    break;
								case trxn.trxn_name === 'DEPOSIT_CARD':
                                    trxns_count_DEPOSIT++;
                                    break;
                                case trxn.trxn_name === 'WITHDRAWAL_CARD':
                                    trxns_count_WITHDRAWAL++;
                                    break;
                                default:
                                    break; // Handle any other cases if necessary
                            }

                            // By Status
                            switch (trxn.response_code) {
                                case '200':
                                    trxns_count_SUCCESS++;
                                    break;
                                case '00':
                                    trxns_count_SUCCESS++;
                                    break;
                                case null:
                                    trxns_count_ONPROGRESS++;
                                    break;
                                default:
                                    trxns_count_FAILED++;
                                    break;
                            }
                        });

                        // Call function to update all charts
                        updateCharts(
                            trxns_count_IB, trxns_count_MB, trxns_count_AB,
                            trxns_count_UTILITY, trxns_count_MINISTATEMENT, trxns_count_BANKTRANSFER,
                            trxns_count_BALANCEINQUIRY, trxns_count_REVERSAL, trxns_count_FULLSTATEMENT,
                            trxns_count_B2W, trxns_count_DEPOSIT, trxns_count_WITHDRAWAL,
                            trxns_count_SUCCESS, trxns_count_FAILED, trxns_count_ONPROGRESS
                        );
                    }
                });

                // Update charts function
                function updateCharts(ibCount, mbCount, abCount, utilityCount, miniStatementCount, bankTransferCount,
                                      balanceInquiryCount, reversalCount, fullstatementCount, walletCount,
                                      depositCount, withdrawalCount, successCount, failedCount, onProgressCount) {
                    // Chart Colors
                    var chartColors = {
                        red: 'rgb(255, 99, 132)',
                        green: 'rgb(75, 192, 192)',
                        blue: 'rgb(54, 162, 235)',
                        orange: 'rgb(255, 159, 64)',
                        purple: 'rgb(96,20,252)',
                        grey: 'rgb(201, 203, 207)',
                        coral: 'rgb(255, 127, 80)',
                        pink: 'rgb(190,0,255)',
                        gold: 'rgb(255,242,0)',
                    };

                    // Transactions by Type Chart
                    var typeCtx = document.getElementById('transactions_by_type_chart').getContext('2d');
                    new Chart(typeCtx, {
                        type: 'pie',
                        data: {
                            datasets: [{
                                data: [utilityCount, miniStatementCount, bankTransferCount, balanceInquiryCount,
                                    reversalCount, fullstatementCount, walletCount, depositCount, withdrawalCount],
                                backgroundColor: [chartColors.orange, chartColors.purple, chartColors.blue,
                                    chartColors.green, chartColors.red, chartColors.grey, chartColors.coral,
                                    chartColors.gold, chartColors.pink]
                            }],
                            labels: ['Utility', 'Mini Statement', 'Bank Transfer', 'Balance Inquiry', 'Reversals',
                                'Full Statement', 'Bank To Wallet', 'Deposit', 'Withdrawal']
                        },
                        options: {responsive: true}
                    });

                    // Transactions by Status Chart
                    var statusCtx = document.getElementById('transactions_by_status_chart').getContext('2d');
                    new Chart(statusCtx, {
                        type: 'pie',
                        data: {
                            datasets: [{
                                data: [successCount, failedCount, onProgressCount],
                                backgroundColor: [chartColors.green, chartColors.red, chartColors.grey]
                            }],
                            labels: ['Success', 'Failed', 'On Progress']
                        },
                        options: {responsive: true}
                    });
                }
            });
            $(document).ready(function () {
                $.ajax({
                    url: '{{ route("all.users") }}',
                    method: 'GET',
                    success: function (data) {
                        $("#users").html(data.users.toLocaleString());
                        $("#agents").html(data.agents.toLocaleString());
                        $("#devices").html(data.devices.toLocaleString());
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching user counts:', error);
                    }
                });
            });
        </script>
    @endsection
@endsection
