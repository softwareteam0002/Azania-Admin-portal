{{--TODO: Cancel/Reverse Transactions--}}
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
        <hr/>
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small" style="background-color:#f9f9f9; padding: .55rem 1rem;">
            <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
            <li class="breadcrumb-item active">Transactions</li>
        </ol>
    </nav>
    <h5>Transaction Filter</h5>
    <div class="card">
        <form action="{{url('agency/transactions')}}" method="GET">
            <div class="card-body">
                <div class="row">
                    <div class="col-4 form-group">
                        <label for="account">Transaction ID:</label>
                        <input type="text" name="transaction_id" class="form-control">
                    </div>
                    <div class="col-4 form-group">
                        <label for="account">From Date: </label>
                        <input type="date" name="start_date" class="form-control">
                    </div>
                    <div class="col-4 form-group">
                        <label for="account">To Date: </label>
                        <input type="date" name="end_date" class="form-control">
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12 form-group mg-t-8">
                        <button type="submit" class="btn btn-primary">Filter <i class="fa fa-filter"></i></button>
                        <a href="{{url('agency/transactions')}}" class="btn btn-danger">Clear Filter <i class="fa
                        fa-times-circle-o"></i></a>
                    </div>
                </div>
        </form>
    </div>

    </div>
    <h5>Transactions</h5>
    <hr/>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-responsive-sm table-striped table-sm table-hover datatable">
                    <thead>
                    <tr>
                        <th width="10">
                        </th>
                        <!-- <th>Operator</th> -->
                        <th>Transaction ID</th>
                        <th>Agent terminal</th>
                        <th>Transaction name</th>
                        <th>Amount</th>
                        <th>Charges</th>
                        <th>Date</th>
                        <th>Customer account</th>
                        <th>Status</th>
                        <th>
                            Action
                        </th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($transactions as $transaction)
                        <tr data-entry-id="1" data-toggle="modal" data-target="#largeModal{{$transaction->id}}"
                            id="loadingModel" data="{{$transaction->id}}">
                            <td>
                            </td>
                            <td>{{$transaction->transactionID}}</td>
                            <td>{{$transaction->trans_agent_terminal_id}}</td>
                            <td>{{$transaction->trxn_name}}</td>
                            <td>{{number_format( $transaction->amount/100,2)}}</td>
                            <td>{{ $transaction->charges }}</td>
                            <td>{{$transaction->date}}</td>
                            <td>{{$transaction->customer_account ?? 'N/A'}}
                                @if(strlen($transaction->customer_account)==0)
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($transaction->response_code == '00' || $transaction->response_code == '200')
                                    <span class="badge badge-success"> SUCCESS</span>
                                @elseif($transaction->response_code == null)
                                    <span class="badge badge-warning"> IN-PROGRESS </span>
                                @elseif($transaction->response_code != '00' || $transaction->response_code != '200')
                                    <span class="badge badge-warning"> IN-PROGRESS</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ url('agency/transaction',[$transaction->txn_id]) }}"
                                   class="btn btn-sm btn-primary" title="View Transaction">
                                    <span><i class="fas fa-eye text-dark-pastel-green"></i></span>
                                </a>
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


            jQuery(document).ready(function () {
                jQuery('#ajaxSubmit').click(function (e) {
                    var dialogy = " <div class=\"d-flex justify-content-center\">\n" +
                        "                                            <div class=\"spinner-border\" role=\"status\">\n" +
                        "                                              <span class=\"sr-only\">Loading...</span>\n" +
                        "                                            </div>\n" +
                        "                                        </div>";
                    document.getElementById('subscriberDetails').innerHTML = dialogy;
                    e.preventDefault();
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    jQuery.ajax({
                        {{--url: "{{ url('/ib/subscriber/'.Crypt::encrypt(1)) }}",--}}
                        url: "{{ url('/ib/subscriber/'.Crypt::encrypt(1)) }}",
                        method: 'get',
                        data: {},
                        success: function (result) {
                            var cleanData = JSON.parse(result.data);
                            console.log("Here is the data received ", cleanData);
                            var tempData = "<tr>\n" +
                                "                                        <td>Name</td>\n" +
                                "                                        <td>" + cleanData[0]['name'] + "</td>\n" +
                                "                                    </tr>\n" +
                                "                                    <tr>\n" +
                                "                                        <td>Email</td>\n" +
                                "                                        <td>" + cleanData[0]['email'] + "</td>\n" +
                                "                                    </tr>\n" +
                                "                                    <tr>\n" +
                                "                                        <td>Language</td>\n" +
                                "                                        <td>\n" +
                                "                                            en\n" +
                                "                                        </td>\n" +
                                "                                    </tr>\n" +
                                "                                    <tr>\n" +
                                "\n" +
                                "\n" +
                                "                                        <td>Status</td>\n" +
                                "                                        <td><span class=\"badge badge-danger\">Suspende</span></td>\n" +
                                "                                    </tr>";
                            document.getElementById('subscriberDetails').innerHTML = "";
                            var table = "<table class=\"table table-responsive-sm table-bordered table-striped table-sm\">\n" +
                                "                                    <thead>\n" +
                                "                                    <tr class=\"bottom-border-color-green primary\" data-toggle=\"modal\" data-target=\"#largeModalTesting\">\n" +
                                "                                        <th colspan=\"2\">SUBSCRIBER DETAILS</th>\n" +
                                "                                    </tr>\n" +
                                "                                    </thead>\n" +
                                "                                    <tbody \n" +
                                tempData +
                                "                                    </tbody>\n" +
                                "                                </table>";
                            document.getElementById('subscriberDetails').innerHTML = table;

                        }
                    });
                });
            });

            jQuery(document).ready(function () {
                jQuery('#loadingModel').click(function (e) {
                    var dialogy = " <div class=\"d-flex justify-content-center\">\n" +
                        "                                            <div class=\"spinner-border\" role=\"status\">\n" +
                        "                                              <span class=\"sr-only\">Loading...</span>\n" +
                        "                                            </div>\n" +
                        "                                        </div>";
                    document.getElementById('subscriberDetails').innerHTML = dialogy
                    console.log(document.getElementById("loadingModel").getAttribute("data"));
                    e.preventDefault();
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    jQuery.ajax({
                        url: "{{ url('/ib/subscriber/'.Crypt::encrypt(1)) }}",
                        method: 'get',
                        data: {},
                        success: function (result) {
                            var cleanData = JSON.parse(result.data);
                            var tempData = "<tr>\n" +
                                "                                        <td>Name</td>\n" +
                                "                                        <td>" + cleanData[0]['name'] + "</td>\n" +
                                "                                    </tr>\n" +
                                "                                    <tr>\n" +
                                "                                        <td>Email</td>\n" +
                                "                                        <td>" + cleanData[0]['email'] + "</td>\n" +
                                "                                    </tr>\n" +
                                "                                    <tr>\n" +
                                "                                        <td>Language</td>\n" +
                                "                                        <td>\n" +
                                "                                            en\n" +
                                "                                        </td>\n" +
                                "                                    </tr>\n" +
                                "                                    <tr>\n" +
                                "\n" +
                                "\n" +
                                "                                        <td>Status</td>\n" +
                                "                                        <td><span class=\"badge badge-danger\">Suspende</span></td>\n" +
                                "                                    </tr>";
                            document.getElementById('subscriberDetails').innerHTML = "";
                            var table = "<table class=\"table table-responsive-sm table-bordered table-striped table-sm\">\n" +
                                "                                    <thead>\n" +
                                "                                    <tr class=\"bottom-border-color-green primary\" data-toggle=\"modal\" data-target=\"#largeModalTesting\">\n" +
                                "                                        <th colspan=\"2\">SUBSCRIBER DETAILS</th>\n" +
                                "                                    </tr>\n" +
                                "                                    </thead>\n" +
                                "                                    <tbody \n" +
                                tempData +
                                "                                    </tbody>\n" +
                                "                                </table>";
                            document.getElementById('subscriberDetails').innerHTML = table;

                        }
                    });
                });
            });


            $(function () {
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.users.massDestroy') }}",
                    className: 'btn-danger',
                    action: function (e, dt, node, config) {
                        var ids = $.map(dt.rows({selected: true}).nodes(), function (entry) {
                            return $(entry).data('entry-id')
                        });

                        if (ids.length === 0) {
                            alert('{{ trans('global.datatables.zero_selected') }}')

                            return
                        }

                        if (confirm('{{ trans('global.areYouSure') }}')) {
                            $.ajax({
                                headers: {'x-csrf-token': _token},
                                method: 'POST',
                                url: config.url,
                                data: {ids: ids, _method: 'DELETE'}
                            })
                                .done(function () {
                                    location.reload()
                                })
                        }
                    }
                }
                let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
                @can('user_delete')
                dtButtons.push(deleteButton)
                @endcan

                $('.datatable:not(.ajaxTable)').DataTable({buttons: dtButtons})
            })

        </script>
    @endsection

@endsection
