@extends('layouts.admin')
@section('content')

<div class="row mb-3">
    <div class="col-md-8">
        <!-- Notifications-->
        @if(isset($notification) && isset($color))
        <div class="alert alert-{{ $color }} alert-dismissible fade show" role="alert">
            {{ $notification }}
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
    <hr />
</div>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb small" style="background-color:#f9f9f9; padding: .55rem 1rem;">
        <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>

        <li class="breadcrumb-item active">Internet Banking Reports</li>
    </ol>
</nav>

<h5>Internet Banking Reports Tanzania</h5>
<hr />

<div class="card mb-5">
    <div class="card-body">
        <h6>Export report</h6>
        <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
        <form action="{{ url('ib/reports/export') }}" method="POST">
            @csrf
            @method('POST')
            <div class="row">
               
                <div class="col-md-3">
                    <div class="form-group">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">From:</span>
                            </div>
                            <input type="date" name="from_date" id='from_date' class="form-control form-control-sm" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">To &nbsp;&nbsp;&nbsp;&nbsp;:</span>
                            </div>
                            <input type="date" name="to_date" id='to_date' class="form-control form-control-sm" required>
                        </div>
                    </div>

                </div>
                <div class="col-md-3">
                            <div class="form-group">
                               
                                <select name="customer_type" class="form-control  form-control-sm">
                                  <option value="" >Select Customer Type</option>
                                  <option value="Retail" >Retail Customers</option>
                                  <option value="Corporate" >Corporate Customers</option>  
                                </select>
                            </div>
                        </div>
            </div>
            <div class="row">
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="name">Format:<span class="text-danger">*</span></label>
                        <div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="format" value="pdf" >
                            <label class="form-check-label">PDF File</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="format" value="xls" checked>
                            <label class="form-check-label">Excel File</label>
                        </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label for="name">Status:<span class="text-danger">*</span></label>
                        <div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" value="all" checked>
                            <label class="form-check-label">All transactions</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" value="success">
                            <label class="form-check-label">Successful</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" value="onprogress">
                            <label class="form-check-label">On Progress </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" value="failed">
                            <label class="form-check-label">Failed </label>
                        </div>

                       

                        </div>
                    </div>
                </div>

               <!-- <div class="col-md-4">
                    <div class="form-group">
                        <label for="name">Services:</label>
                       
                        <div class="options">
                            
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="service[]"
                                        value="BANKTOWALLET" >
                                <label class="form-check-label">Bank to Wallet</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="service[]"
                                        value="BALANCE" >
                                <label class="form-check-label">Balance Inquiry</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="service[]"
                                        value="DEPOSIT" >
                                <label class="form-check-label">Deposit Cash</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="service[]"
                                        value="WITHDRAWAL" >
                                <label class="form-check-label">Withdraw Cash</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="service[]"
                                        value="BANKTRANSFER" >
                                <label class="form-check-label">Fund Transfer</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="service[]"
                                        value="MINISTATEMENT" >
                                <label class="form-check-label">Mini Statement</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="service[]"
                                        value="UTILITY" >
                                <label class="form-check-label">Utility Payments</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="service[]"
                                        value="STOPCHEQUES" >
                                <label class="form-check-label">Stop Cheques</label>
                            </div>

                        </div>
                        <p class="small pt-1">If not selected, will display all.</p>
                        
                    </div>

                </div>-->
                <div class="col-md-4">
                            <div class="form-group">
                               <label for="name">Services:</label>
                                <select name="service" class="form-control  form-control-sm">
                                  <option value="all" >All Services</option>
                                  @foreach($transferTypes as $transferType)
                                  <option value="{{$transferType->id}}">{{$transferType->name}}</option>
                                  @endforeach
  
                                </select>
                            </div>
                        </div>

                <div class="col-md-3">
                    <h6>Quick Dates:</h6>
                    <hr/>
                    <a href="#" onclick="quickDate('today')" class="btn btn-sm btn-link">Today</a>
                    <a href="#" onclick="quickDate('yesterday')"class="btn btn-sm btn-link">Yesterday</a>
                    <a href="#" onclick="quickDate('this_week')"class="btn btn-sm btn-link">This Week</a>
                    <a href="#" onclick="quickDate('this_month')"class="btn btn-sm btn-link">This Month</a>
                    <a href="#" onclick="quickDate('last_month')"class="btn btn-sm btn-link">Last Month</a>
                    <script async src="https://momentjs.com/downloads/moment.min.js"></script>
                    <script>
                        function quickDate(a){
                            var d_from = d;
                            var d_to = d;
                            var today = "{{$today}}";  

                            switch(a){
                                case "today":
                                    //create dates for today
                                    var d = moment(today).format('YYYY-MM-DD');
                                    d_from = d;
                                    d_to = d;
                                break;
                                case "yesterday":
                                    //create dates for today
                                    var d = moment(today).format('YYYY-MM-DD');
                                    d = moment(d).subtract(1, 'day').format('YYYY-MM-DD');
                                    d_from = d;
                                    d_to = d;
                                break;
                                case "this_week":
                                    //create dates for today
                                    var d = moment(today).format('YYYY-MM-DD');
                                    d_from = moment(today).startOf('week').format('YYYY-MM-DD');
                                    d_to = d;
                                break;
                                case "this_month":
                                    //create dates for today
                                    var d = moment(today).format('YYYY-MM-DD');
                                    d_from = moment(today).format('YYYY-MM-')+"01";
                                    d_to = d;
                                break;
                                case "last_month":
                                    //create dates for today
                                    var d = moment(today).format('YYYY-MM-DD');
                                    d_from = moment(today).startOf('month').format('YYYY-MM-DD');
                                    d_to = moment(today).endOf('month').format('YYYY-MM-DD');
                                    d_from =  moment(d_from).subtract(1, 'month').format('YYYY-MM-DD');
                                    d_to =  moment(d_to).subtract(1, 'month').format('YYYY-MM-DD');
                                break;

                            }
                            //populate the dates
                            $("#from_date").val(d_from);
                            $("#to_date").val(d_to);
                        }
                    </script>
                </div>

                <div class="col-md-12">
                    <hr />
                    <button type="submit" class="btn btn-success btn-sm">Export</button>
                    <button type="reset" class="btn btn-link btn-sm">Clear form</button>
                </div>

            </div>


        </form>
    </div>
</div>


<div class="card d-none">
    <div class="card-body">
        <h5>Transactions Report Summary</h5>
        <hr />
        {{ $transactions }}
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5>Users Report Summary</h5>
        <hr />
        <div class="row">
            <div class="col-md-4">
                <div id="users_pie_chart">
                    <canvas id="ib_users_chart"></canvas>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="mt-2 mb-3">Latest cooperate users</h6>
                        <table class="table table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Date</th>
                                </tr>
                            </thead>
                            <tbody id="latest_cooperate_users">
                                <tr colspan='5'>
                                    <td class='text-center'>There is no data!</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-12">
                        <h6 class="mt-2 mb-3">Latest customer users</h6>
                        <table class="table table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Date</th>
                                </tr>
                            </thead>
                            <tbody id="latest_customer_users">
                                <tr colspan='5'>
                                    <td class='text-center'>There is no data!</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

<div class="card d-none">
    <div class="card-body">
        <h5>Usage Report Summary</h5>
    </div>
</div>


<div class="card">
    <div class="card-body">
        <h5>Card Request Report Summary</h5>
        <hr />
        <div class="row">
            <div class="col-md-4">
                <div id="cards_chart" class="mt-10">
                    <canvas id="ib_cards_chart"></canvas>
                </div>
            </div>
            <div class="col-md-8">

                <h6 class="mt-2 mb-3">Latest requested cards requests</h6>
                <table class="table table-sm mb-5">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Transaction ID</th>
                            <th scope="col">Account ID</th>
                            <th scope="col">Name on Card</th>
                            <th scope="col">Date</th>
                        </tr>
                    </thead>
                    <tbody id="latest_requested_cards">
                        <tr colspan='5' class="text-center">
                            <td>There is no data!</td>
                        </tr>
                    </tbody>
                </table>

                <h6 class="mt-2 mb-3">Latest accepted cards requests</h6>
                <table class="table table-sm mb-5">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Transaction ID</th>
                            <th scope="col">Account ID</th>
                            <th scope="col">Name on Card</th>
                            <th scope="col">Date</th>
                        </tr>
                    </thead>
                    <tbody id="latest_accepted_cards">
                        <tr colspan='5' class="text-center">
                            <td>There is no data!</td>
                        </tr>
                    </tbody>
                </table>

                <h6 class="mt-2 mb-3">Latest processed cards requests</h6>
                <table class="table table-sm mb-5">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Transaction ID</th>
                            <th scope="col">Account ID</th>
                            <th scope="col">Name on Card</th>
                            <th scope="col">Date</th>
                        </tr>
                    </thead>
                    <tbody id="latest_processed_cards">
                        <tr colspan='5' class="text-center">
                            <td>There is no data!</td>
                        </tr>
                    </tbody>
                </table>

                <h6 class="mt-2 mb-3">Latest collected cards requests</h6>
                <table class="table table-sm mb-5">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Transaction ID</th>
                            <th scope="col">Account ID</th>
                            <th scope="col">Name on Card</th>
                            <th scope="col">Date</th>
                        </tr>
                    </thead>
                    <tbody id="latest_collected_cards">
                        <tr colspan='5' class="text-center">
                            <td>There is no data!</td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
@section('scripts')
@parent
<script>
    $(document).ready(function() {
        $('.datatable').DataTable();
    });


    //init variables
    var c_cards = 5; //this is the number of cards to be displayed on each request
    //render the users chart
    var cooperate_users = @json($cooperate_users);
    var customer_users = @json($customer_users);
    var users = @json($users);

    var transactions = @json($transactions);

    //populate the tables
    var latest_customer_users = "";
    var latest_customer_users = "";
    var cooperate_count = cooperate_users.length;
    var costumer_count = customer_users.length;

    var flag = false;
    var v = 0;
    
    for (var i = 0; i < costumer_count; i++) {
        latest_customer_users += "<tr>" +
            "<td>" + (i + 1) + "</td>" +
            "<td>" + customer_users[i].name + "</td>" +
            "<td>" + customer_users[i].email + "</td>" +
            "<td>" + customer_users[i].mobile_phone + "</td>" +
            "<td>" + customer_users[i].created_at + "</td>" +
            "</tr>";
        if (i >= (c_cards - 1)) {
            break;
        }
    }


    for (var i = 0; i < cooperate_count; i++) {
        latest_cooperate_users += "<tr>" +
            "<td>" + (i + 1) + "</td>" +
            "<td>" + cooperate_users[i].name + "</td>" +
            "<td>" + cooperate_users[i].email + "</td>" +
            "<td>" + cooperate_users[i].mobile_phone + "</td>" +
            "<td>" + cooperate_users[i].created_at + "</td>" +
            "</tr>";
        if (i >= (c_cards - 1)) {
            break;
        }
    }

    //render the UI
    $("#latest_customer_users").html(latest_customer_users);
    $("#latest_cooperate_users").html(latest_cooperate_users);



    window.chartColors = {
        red: 'rgb(255, 99, 132)',
        orange: 'rgb(255, 159, 64)',
        yellow: 'rgb(255, 205, 86)',
        green: 'rgb(75, 192, 192)',
        blue: 'rgb(54, 162, 235)',
        purple: 'rgb(153, 102, 255)',
        grey: 'rgb(201, 203, 207)'
    };

    var config = {
        type: 'pie',
        data: {
            datasets: [{
                data: [
                    cooperate_count,
                    costumer_count,
                ],
                backgroundColor: [
                    window.chartColors.green,
                    window.chartColors.blue,
                ],
                label: 'Internet Banking Users'
            }],
            labels: [
                'Cooperate',
                'Customers',
            ]
        },
        options: {
            responsive: true
        }
    };

    var ctx = document.getElementById('ib_users_chart').getContext('2d');
    window.myPie = new Chart(ctx, config);

    //render the atm card report
    var requested_cards = @json($requested_cards);
    var accepted_cards = @json($accepted_cards);
    var processed_cards = @json($processed_cards);
    var collected_cards = @json($collected_cards);
    var cards = @json($cards);

    var requested_cards_count = requested_cards.length;
    var accepted_cards_count = accepted_cards.length;
    var processed_cards_count = processed_cards.length;
    var collected_cards_count = collected_cards.length;
    var cards = cards.length;

    var latest_requested_cards = "";
    var latest_accepted_cards = "";
    var latest_processed_cards = "";
    var latest_collected_cards = "";

    //render the tables
    for (var i = 0; i < requested_cards_count; i++) {
        latest_requested_cards += "<tr>" +
            "<td>" + (i + 1) + "</td>" +
            "<td>" + requested_cards[i].transactionId + "</td>" +
            "<td>" + requested_cards[i].accountID + "</td>" +
            "<td>" + requested_cards[i].name_on_card + "</td>" +
            "<td>" + requested_cards[i].created_at + "</td>" +
            "</tr>";
        if (i >= (c_cards - 1)) {
            break;
        }
    }

    for (var i = 0; i < accepted_cards_count; i++) {
        latest_accepted_cards += "<tr>" +
            "<td>" + (i + 1) + "</td>" +
            "<td>" + accepted_cards[i].transactionId + "</td>" +
            "<td>" + accepted_cards[i].accountID + "</td>" +
            "<td>" + accepted_cards[i].name_on_card + "</td>" +
            "<td>" + accepted_cards[i].created_at + "</td>" +
            "</tr>";
        if (i >= (c_cards - 1)) {
            break;
        }
    }

    for (var i = 0; i < processed_cards_count; i++) {
        latest_processed_cards += "<tr>" +
            "<td>" + (i + 1) + "</td>" +
            "<td>" + processed_cards[i].transactionId + "</td>" +
            "<td>" + processed_cards[i].accountID + "</td>" +
            "<td>" + processed_cards[i].name_on_card + "</td>" +
            "<td>" + processed_cards[i].created_at + "</td>" +
            "</tr>";
        if (i >= (c_cards - 1)) {
            break;
        }
    }


    for (var i = 0; i < processed_cards_count; i++) {
        latest_collected_cards += "<tr>" +
            "<td>" + (i + 1) + "</td>" +
            "<td>" + collected_cards[i].transactionId + "</td>" +
            "<td>" + collected_cards[i].accountID + "</td>" +
            "<td>" + collected_cards[i].name_on_card + "</td>" +
            "<td>" + collected_cards[i].created_at + "</td>" +
            "</tr>";
        if (i >= (c_cards - 1)) {
            break;
        }
    }


    if (latest_requested_cards != "") {
        $("#latest_requested_cards").html(latest_requested_cards);
    }


    if (latest_accepted_cards != "") {
        $("#latest_accepted_cards").html(latest_accepted_cards);
    }

    if (latest_processed_cards != "") {
        $("#latest_processed_cards").html(latest_processed_cards);
    }

    if (latest_collected_cards != "") {
        $("#latest_collected_cards").html(latest_collected_cards);
    }



    var config = {
        type: 'pie',
        data: {
            datasets: [{
                data: [
                    requested_cards_count,
                    accepted_cards_count,
                    processed_cards_count,
                    collected_cards_count,
                ],
                backgroundColor: [
                    window.chartColors.red,
                    window.chartColors.orange,
                    window.chartColors.yellow,
                    window.chartColors.green,
                ],
                label: 'Cards Request'
            }],
            labels: [
                'Requested',
                'Accepted',
                'Processed',
                'Collected',
            ]
        },
        options: {
            responsive: true
        }
    };

    var ctx = document.getElementById('ib_cards_chart').getContext('2d');
    window.myPie = new Chart(ctx, config);



    console.log("This is to be printed");
</script>
@endsection

@endsection
