@extends('layouts.admin')
@section('content')
<div class="content">

    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-header">ESB Users</div>
                <div class="card-body">{{ $admin }}</div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    {{-- <a class="small text-white stretched-link" href="{{ url('admin/users') }}">View Details</a>--}}
                    <div class="small text-white"><svg class="svg-inline--fa fa-angle-right fa-w-8" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg="">
                            <path fill="currentColor" d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path>
                        </svg><!-- <i class="fas fa-angle-right"></i> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-header">Agency Banking Users</div>
                <div class="card-body">{{$agency}}</div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    {{-- <a class="small text-white stretched-link" href="">View Details</a>--}}
                    <div class="small text-white"><svg class="svg-inline--fa fa-angle-right fa-w-8" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg="">
                            <path fill="currentColor" d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path>
                        </svg><!-- <i class="fas fa-angle-right"></i> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-header">Internet Banking Users</div>
                <div class="card-body">{{ $ib }}</div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    {{-- <a class="small text-white stretched-link" href="#">View Details</a>--}}
                    <div class="small text-white"><svg class="svg-inline--fa fa-angle-right fa-w-8" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg="">
                            <path fill="currentColor" d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path>
                        </svg><!-- <i class="fas fa-angle-right"></i> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-header">Total Users</div>
                <div class="card-body">{{$total}}</div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    {{-- <a class="small text-white stretched-link" href="#">View Details</a>--}}
                    <div class="small text-white"><svg class="svg-inline--fa fa-angle-right fa-w-8" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg="">
                            <path fill="currentColor" d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path>
                        </svg><!-- <i class="fas fa-angle-right"></i> -->
                    </div>
                </div>
            </div>
        </div>


    </div>

    <div class="row">

        <div class="col-md-12">
            <h5>Transactions - <span id="transactions_total">0</span> Total</h5>
            <hr />
        </div>


        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div id="cards_chart" class="mt-10">
                        <canvas id="transactions_by_type_chart"></canvas>
                        <h6 class="text-center mt-2">Transactions by Type</h6>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-4">
            <div class="row">

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div id="cards_chart" class="mt-10">
                                <canvas id="transactions_by_channel_chart"></canvas>
                                <h6 class="text-center mt-2">Transactions by Channel</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div id="cards_chart" class="mt-10">
                                <canvas id="transactions_by_status"></canvas>
                                <h6 class="text-center mt-2">Transactions by Status</h6>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>


    </div>


</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>


@section('scripts')
@parent
<script>
    //start creating the chart
    window.chartColors = {
        red: 'rgb(255, 99, 132)',
        orange: 'rgb(255, 159, 64)',
        yellow: 'rgb(255, 205, 86)',
        green: 'rgb(75, 192, 192)',
        blue: 'rgb(54, 162, 235)',
        purple: 'rgb(153, 102, 255)',
        grey: 'rgb(201, 203, 207)',
        c0: 'rgb(250, 250, 210)',
        c1: 'rgb(255, 20, 417)',
        c2: 'rgb(39,0,139)',
        c3: 'rgb(75,0,130)',
        c4: 'rgb(30,144,255)',
        c5: 'rgb(95,158,160)',
        c6: 'rgb(46,139,87)',
        c7: 'rgb(50,205,50)',
        c8: 'rgb(255,165,0)',
        c9: 'rgb(203, 212, 194)'
    };



    var trxns = @json($trxns);
    console.log(trxns);
    $("#transactions_total").html(trxns.length);
    //variables used to create the pie charts
    var trxns_count_IB = 0,
        trxns_count_MB = 0,
        trxns_count_AB = 0,
        trxns_total_IB = 0,
        trxns_total_MB = 0,
        trxns_total_AB = 0,
        trxns_count_UTILITY = 0,
        trxns_count_MINISTATEMENT = 0,
        trxns_count_FULLSTATEMENT = 0,
        trxns_count_BANKWALLET = 0,
        trxns_count_BALANCE = 0,
        trxns_count_CHARGE = 0,
        trxns_count_BANKTRANSFER = 0,
        trxns_count_INFO = 0,
        trxns_count_PAYINFO = 0,
        trxns_count_PAYSOLN = 0,
        trxns_count_SUCCESS = 0,
        trxns_count_FAILED = 0,
        trxns_count_ONPROGRESS = 0;



    //loop and get the data
    for (var j = 0; j < trxns.length; j++) {
        //parse the trxns by channel
        switch (trxns[j].transaction_channel) {
            case "IB":
                trxns_count_IB++;
                trxns_total_IB = trxns_total_IB + Number(trxns[j].transaction_amount);
                break;
            case "AB":
                trxns_count_AB++;
                trxns_total_AB = trxns_total_AB + Number(trxns[j].transaction_amount);
                break;
            case "MB":
                trxns_count_MB++;
                trxns_total_MB = trxns_total_MB + Number(trxns[j].transaction_amount);
                break;
        }

        //parse transactions by type
        switch (trxns[j].transaction_name) {
            case "UTILITY":
                trxns_count_UTILITY++;
                break;
            case "MINISTATEMENT":
                trxns_count_MINISTATEMENT++;
                break;
            case "FULLSTATEMENT":
                trxns_count_FULLSTATEMENT++;
                break;
            case "BANKWALLET":
                trxns_count_BANKWALLET++;
                break;
            case "BALANCE":
                trxns_count_BALANCE++;
                break;
            case "CHARGE":
                trxns_count_CHARGE++;
                break;
            case "BANKTRANSFER":
                trxns_count_BANKTRANSFER++;
                break;
            case "INFO":
                trxns_count_INFO++;
                break;
            case "PAYINFO":
                trxns_count_PAYINFO++;
                break;
            case "PAYSOLN":
                trxns_count_PAYSOLN++;
                break;
        }

        //parse transactions by status
        switch (trxns[j].transaction_status) {
            case "200":
                trxns_count_SUCCESS++;
                break;
            case "100":
                trxns_count_ONPROGRESS++;
                break;
            case "9999":
                trxns_count_ONPROGRESS++;
                break;
            default:
                trxns_count_FAILED++;
                break;
        }
    }




    //create the chart
    var config = {
        type: 'pie',
        data: {
            datasets: [{
                data: [
                    trxns_count_IB,
                    trxns_count_MB,
                    trxns_count_AB,
                ],
                backgroundColor: [
                    window.chartColors.green,
                    window.chartColors.blue,
                    window.chartColors.red,
                ],
                label: 'IB Transactions Success Rate'
            }],
            labels: [
                'Internet Banking',
                'Mobile Banking',
                'Agency Banking',
            ]
        },
        options: {
            responsive: true
        }
    };

    var ctx = document.getElementById('transactions_by_channel_chart').getContext('2d');
    window.myPie = new Chart(ctx, config);

    //create the chart
    var config = {
        type: 'pie',
        data: {
            datasets: [{
                data: [
                    trxns_count_UTILITY,
                    trxns_count_MINISTATEMENT,
                    trxns_count_FULLSTATEMENT,
                    trxns_count_BANKWALLET,
                    trxns_count_BALANCE,
                    trxns_count_CHARGE,
                    trxns_count_BANKTRANSFER,
                    trxns_count_INFO,
                    trxns_count_PAYINFO,
                    trxns_count_PAYSOLN,
                ],
                backgroundColor: [
                    window.chartColors.c0,
                    window.chartColors.c1,
                    window.chartColors.c2,
                    window.chartColors.c3,
                    window.chartColors.c4,
                    window.chartColors.c5,
                    window.chartColors.c6,
                    window.chartColors.c7,
                    window.chartColors.c8,
                    window.chartColors.c9,
                ],
                label: 'Transactions by Type'
            }],
            labels: [
                'UTILITY',
                'MINISTATEMENT',
                'FULLSTATEMENT',
                'BANKWALLET',
                'BALANCE',
                'CHARGE',
                'BANKTRANSFER',
                'INFO',
                'PAYINFO',
                'PAYSOLN',
            ]
        },
        options: {
            responsive: true
        }
    };

    var ctx = document.getElementById('transactions_by_type_chart').getContext('2d');
    window.myPie = new Chart(ctx, config);


    //create the chart
    var config = {
        type: 'pie',
        data: {
            datasets: [{
                data: [
                    trxns_count_SUCCESS,
                    trxns_count_FAILED,
                    trxns_count_ONPROGRESS,
                ],
                backgroundColor: [
                    window.chartColors.green,
                    window.chartColors.red,
                    window.chartColors.blue,
                ],
                label: 'Transactions by Type'
            }],
            labels: [
                'SUCCESS',
                'FAILED',
                'ON PROGRESS',
            ]
        },
        options: {
            responsive: true
        }
    };

    var ctx = document.getElementById('transactions_by_status').getContext('2d');
    window.myPie = new Chart(ctx, config);
</script>
@endsection
@endsection