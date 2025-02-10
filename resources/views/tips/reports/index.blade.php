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
    </div>

    <div class="col-md-4 text-right">
        <!-- Date and Time-->
        <p id="todayDate" class="small"><b></b></p>
        <h5 id="todayTime" class=""><b></b></h5>
    </div>
    <hr />
</div>

<h5>TIPS Transactions Report </h5>
<hr />
<div class="card mb-5">
    <div class="card-body">
        <h6>Export report</h6>
        <p class="small text-danger">All fields with an asterisk(*) are mandatory.</p>
        <form action="{{ url('tips/reports/export') }}" method="POST">
            @csrf
            @method('POST')
            <div class="row">

                <div class="col-md-2">
                    <label for="name">Report Duration:</label>

                    <div class="form-group">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text">From:</span>
                            </div>
                            <input type="date" name="from_date" id='from_date' class="form-control form-control-sm" required>
                        </div>
                    </div>

                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text">To &nbsp;&nbsp;&nbsp;&nbsp;:</span>
                        </div>
                        <input type="date" name="to_date" id='to_date' class="form-control form-control-sm" required>
                    </div>

                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="name">Format:<span class="text-danger">*</span></label>
                        <div>
                            <!--<div class="form-check">
                                <input class="form-check-input" type="radio" name="format" value="pdf" checked>
                                <label class="form-check-label">PDF File</label>
                            </div>-->
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" value="xls">
                                <label class="form-check-label">Excel File</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!--<div class="col-md-2">
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
                                <input class="form-check-input" type="radio" name="status" value="failed">
                                <label class="form-check-label">Pending / Failed </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label for="name">Channel:</label>

                        <div class="options">

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="channels[]" value="IB">
                                <label class="form-check-label">Internet Banking</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="channels[]" value="MB">
                                <label class="form-check-label">Mobile Banking</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="channels[]" value="AB">
                                <label class="form-check-label">Agency Banking</label>
                            </div>


                        </div>
                        <p class="small pt-1">If not selected, will display all.</p>

                    </div>

                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label for="name">Service:</label>

                        <div class="options">

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="service[]" value="BALANCE">
                                <label class="form-check-label">Balance</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="service[]" value="BANKTOWALLET">
                                <label class="form-check-label">Bank to Wallet</label>
                            </div>


                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="service[]" value="UTILITY">
                                <label class="form-check-label">Utility</label>
                            </div>


                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="service[]" value="PAYSOLN">
                                <label class="form-check-label">Payment Solution</label>
                            </div>



                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="service[]" value="MINISTATEMENT">
                                <label class="form-check-label">Mini Statement</label>
                            </div>


                        </div>
                        <p class="small pt-1">If not selected, will display all.</p>

                    </div>

                </div>-->

                <div class="col-md-3">
                    <h6>Quick Dates:</h6>
                    <hr />
                    <a href="#" onclick="quickDate('today')" class="btn btn-sm btn-link">Today</a>
                    <a href="#" onclick="quickDate('yesterday')" class="btn btn-sm btn-link">Yesterday</a>
                    <a href="#" onclick="quickDate('this_week')" class="btn btn-sm btn-link">This Week</a>
                    <a href="#" onclick="quickDate('this_month')" class="btn btn-sm btn-link">This Month</a>
                    <a href="#" onclick="quickDate('last_month')" class="btn btn-sm btn-link">Last Month</a>
                    <script async src="https://momentjs.com/downloads/moment.min.js"></script>
                    <script>
                        function quickDate(a) {
                            var d_from = d;
                            var d_to = d;
                            var today = "{{$today}}";

                            switch (a) {
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
                                    d_from = moment(today).format('YYYY-MM-') + "01";
                                    d_to = d;
                                    break;
                                case "last_month":
                                    //create dates for today
                                    var d = moment(today).format('YYYY-MM-DD');
                                    d_from = moment(today).startOf('month').format('YYYY-MM-DD');
                                    d_to = moment(today).endOf('month').format('YYYY-MM-DD');
                                    d_from = moment(d_from).subtract(1, 'month').format('YYYY-MM-DD');
                                    d_to = moment(d_to).subtract(1, 'month').format('YYYY-MM-DD');
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

@section('scripts')
@parent
<script>
    $(function() {
        $('.datatable').DataTable()
    });
</script>
@endsection
@endsection