<!DOCTYPE html>
<html>

<head>
    <title>MB Transactions Report Report</title>
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
                            <h3><b>MB Transaction Report</b></h3>
                            <p class="small">As of {{ $from_date }} to {{ $to_date }}.<br />{{ $s_title }}</p>
                        </td>
                    </tr>
                </table>



    <hr />
    <table style="width:100%">
        <tr>
             <th><b># </b></th>
             <th><b>Date </b></th>
             <th><b>Transaction ID </b></th>
             <th><b>Channel </b></th>
             <th><b>Type </b></th>
             <th><b>Serial ID </b></th>
             <th><b>Batch ID </b></th>
             <th><b>Status </b></th>
            <th class='tright'><b>Amount </b></th>
            <th class='tright'><b>Charges </b></th>
        </tr>
        <?php
        $amount_total = 0;
        $charges_total = 0;
        $k = 1;
        foreach ($transactions as $transcation) {
            /**
             * 
             * "txn_i  d": "1312",
                {
                    "id": 1341,
                    "transaction_channel": "IB",
                    "transaction_name": "UTILITY",
                    "transaction_amount": "90",
                    "transaction_date": "Fri May 22 12:05:19 EAT 2020",
                    "transaction_id": "IB.1590138355.19668400",
                    "transaction_status": "200",
                    "transaction_response": "SUCCESS",
                    "transaction_thirdpart_status": "200",
                    "transaction_thirdpart_response": "successfull processed",
                    "transaction_thirdpart_data": "1010101010111",
                    "transaction_thirdpart_ref": null,
                    "transaction_is_reversed": "0",
                    "transaction_serialID": "6426",
                    "transaction_batchID": "1786382",
                    "transaction_charge": "0"
                }
             */
                    $transaction_id = $transcation->transaction_id;
                    $transaction_channel = $transcation->transaction_channel;
                    $transaction_name = $transcation->transaction_name;
                    $amount = intval($transcation->transaction_amount);
                    $date = $transcation->transaction_date;
                    //convert the date to Y-m-d
                    $date = date("Y-m-d H:i:s", strtotime($date));

                    $charges = intval($transcation->transaction_charge);
                    $transaction_response = $transcation->transaction_response;

                    $transaction_status = $transcation->transaction_status;
                    //parse the status to define a text

                    if($transaction_status == "200"){
                        $transaction_response  = "Succces";
                    }elseif($transaction_status == null){
                        $transaction_response  = "On Progress";
                    }else{
                        $transaction_response  = "Failed";
                     }

                    $transaction_serialID = $transcation->transaction_serialID ?? "--";
                    $transaction_batchID = $transcation->transaction_batchID ?? "--";
                    $amount_total = ($amount_total + $amount);
                    $charges_total = ($charges_total + $charges);
                    echo "
        
        <tr>
            <td>$k</td>
            <td>$date</td>
            <td>$transaction_id</td>
            <td>$transaction_channel</td>
            <td>$transaction_name</td>
            <td>$transaction_serialID</td>
            <td>$transaction_batchID</td>
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
        </tr>
        <tr>
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