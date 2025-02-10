
!DOCTYPE html>
<html>

<head>
    <title>IB Transactions Report Report</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<style>
    body {
        font-size: 12px;
    }

    .tright {
        text-align: right;
    }

    .tleft {
        text-align: left;
    }

    .b {
        font-weight: bold;
    }

    th {
        text-align: left;
        font-weight: bold;
    }

    .rheading {
        text-transform: capitalize;
    }
</style>

<body>
    <div class="contaier-fluid">
        <div class="row">
            <div class="col-lg-12">

                <table>
                    <tr>
                        <td>
                            <img src="{{ public_path('images/logo.png') }}" width="100px" height="auto" alt="">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h3><b>IB Transaction Report</b></h3>
                            <p class="small">As of {{ $from_date }} to {{ $to_date }}.<br />{{ $s_title }}</p>
                        </td>
                    </tr>
                </table>

                <hr />
                <table width="100%">
                    <tr>
                        <th><b>#</b></th>
                        <th style="width:auto"><b>Date</b></th>
                        <th><b>Transaction ID</b></th>
                        <th><b>Channel</b></th>
                        <th><b>Type</b></th>
			<th><b>Service</b></th>
                        <th><b>Account Number</b></th>
                        <th><b>Dest. Account</b></th>
                        <th><b>Ref. Number</b></th>
                        <th><b>Status</b></th>
                        <th class='tright'><b>Amount</b></th>
                        <th class='tright'><b>Charges</b></th>
                    </tr>
                    <?php
                    $amount_total = 0;
                    $charges_total = 0;
                    $k = 1;
                    foreach ($transactions as $transcation) {
                        $transaction_id = $transcation->transactionId;
                        $transaction_channel = "IB";
                        $transaction_name = $transcation->serviceType;
                        $amount = intval($transcation->trxAmount);
                        $date = $transcation->transactionTimestamp;
                        //convert the date to Y-m-d
                        $date = date("Y-m-d H:i:s", strtotime($date));


                        $charges = intval($transcation->charge);
                        $transaction_response = $transcation->responseMessage;

                        $transaction_status = $transcation->transaction_status;
                        //parse the status to define a text
			switch ($transcation->transfer_type_id) {
                                case 1:
                                $service = "GOVERNMENT";
                                break;
                                case 2:
                                $service = "LUKU PAYNMENT";
                                break;
                                case 3:
                                $service = "TV SUBSCRIPTION";
                                break;
                                case 4:
                                $service = "INTERNET SUBSCRIPTION";
                                break;
                                case 5:
                                $service = "AIRTIME TOP UP";
                                break;
                                case 6:
                                $service = "WATER BILLS";
                                break;
                                case 7:
                                $service = "INSTITUTION PAYMENT";
                                break;
                                case 8:
                                $service = "INTERNAL ACCOUNT TRANSFER";
                                break;
                                case 9:
                                $service = "BANK TO WALLET";
                                break;
                                case 11:
                                $service = "WALLETS";
                                break;
                                case 16:
                                $service = "BULK IFT";
                                break;
                                case 18:
                                $service = "BULK EFT";
                                break;
                                case 19:
                                $service = "STANDING ORDER";
                                break;
                                case 20:
                                $service = "STOP CHEQUE";
                                break;
                                case 22:
                                $service = "TISS";
                                break;
                                case 23:
                                $service = "TT";
                                break;
                                case 26:
                                $service = "LOAN REQUEST";
                                break;
                                case 27:
                                $service = "SALARY ADVANCE";
                                break;
                            default:
                                $service = "";
                                break;
                        }
                        if ($transaction_status == "200") {
                            $transaction_response  = "Succces";
                        } elseif ($transaction_status == null) {
                            $transaction_response  = "On Progress";
                        } else {
                            $transaction_response  = "Failed";
                        }

                        $account_number = $transcation->fromAccountID;
                        $dest_account = $transcation->toAccountID ?? "--";
                        $reference_number = $transcation->reference_number ?? "--";
                        $amount_total = ($amount_total + $amount);
                        $charges_total = ($charges_total + $charges);
                        echo "
        
        <tr>
            <td>$k</td>
            <td style='width:130px;'>$date</td>
            <td>$transaction_id</td>
            <td>$transaction_channel</td>
            <td>$transaction_name</td>
	    <td>$service</td>
            <td>$account_number</td>
            <td>$dest_account</td>
            <td>$reference_number</td>
            <td>$transaction_response</td>
            <td class='tright'>" . number_format($amount, 2) . "</td>
            <td class='tright'>" . number_format($charges, 2) . "</td>
        </tr>
        
        ";
                        $k++;
                    }

                    echo "
        <tr>
            <td></td>
	    <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><u><b>TOTAL:</b></u></td>
            <td class='tright'><u><b>" . number_format($amount_total, 2) . "</b></u></td>
            <td class='tright'><u><b>" . number_format($charges_total, 2) . "</b></u></td>
        </tr>
    ";

                    ?>
                </table>


            </div>
        </div>
    </div>
</body>

</html>