<h3>Agency Banking Report</h3>
<p class="small">As of {{ $from_date }} to {{ $to_date }}.<br />{{ $s_title }}</p>
 <table style="width:100%">
    <tr>
        <th>Serial Number</th>
        <th >Agent ID</th>
        <th>Branch Name</th>
        <th>Terminal ID</th>
        <th>Agent Name</th>
        <th>Transaction Date</th>
        <th>Transaction Type</th>
        <!--<th>Transactor's Name</th>
        <th>Transactor's Phone</th>-->
        <th>Transaction ID from ESB </th>
        <!--<th>Transaction Ref from CBS </th>-->
        <th>Agent Account</th>
        <th>Customer Account</th>
        <th>Transaction Amount</th>
        <th>Transaction Charge</th>
        <th>Agent Commission</th>
        <!--<th>Trading Acc Opening Balance</th>
        <th>Trading Acc Closing Balance</th>-->
        <th>Status</th>
        <th>Failure Reason</th>
    </tr>
    <?php
    $amount_total = 0;
    $charges_total = 0;
    $k = 1;

    foreach ($transactions as $transcation) {

        $trans_agent_terminal_id = $transcation->trans_agent_terminal_id;
        //$agentID = $agent->agent_id;
        $serialID = $transcation->serialID;
        $transactionID = $transcation->transactionID;
        $trxn_type = $transcation->trxn_name;
        $amount = intval($transcation->amount);
        $charges = intval($transcation->charges);
        $date = $transcation->trans_datetime;
        $trxn_status = $transcation->trxn_status;
        $customer_account = $transcation->customer_account;
        $amount_total = ($amount_total + $amount);
        $charges_total = ($charges_total + $charges);
        $agent = $transcation->operator->agent;
        $name = $agent->agent_full_name ?? '';
        $branchName = $agent->branchName ?? '';
        $agent_id = $agent->agent_id ?? '';
        $trade = $agent->accounts()->where('account_type_id', 1)->first();
        $trade_account = $trade->bank_account ?? '';
		$trade_acc = $transcation->operator->device->tradingac->bank_account ?? 'N/A';
        $commission = \App\TblABServiceCommision::where('transactionID', $transactionID)->first();
        if($commission)
        {
            $agent_commission = $commission->agent_amount;
        }
        else
        {
            $agent_commission = 0;
        }

        if($transcation->response_code == 200)
        {
            $status = 'Successful';
            $message = '';
        }
        else
        {
            $status = 'Failed';
            $message = $transcation->responseMessage;
        }
        echo "
        
        <tr>
            <td>$k</td>
            <td>$agent_id</td>
            <td>$branchName</td>
            <td>$trans_agent_terminal_id</td>
            <td>$name</td>
            <td>$date</td>
            <td>$trxn_type</td>
            <td>$transactionID</td>
            <td>$trade_acc</td>
            <td>$customer_account</td>
            <td>$amount</td>
            <td>$charges</td>
            <td>$agent_commission</td>
            <td>$status</td>
            <td>$message</td>
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
            <td style='text-align: right;'><u><b>" . number_format($amount_total, 2) . "</b></u></td>
            <td style='text-align: right;'><u><b>" . number_format($charges_total, 2) . "</b></u></td>
            <td></td>
            <td></td>
            <td></td>
            
        </tr>
    ";

    ?>
</table>