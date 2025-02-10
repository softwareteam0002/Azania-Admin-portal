<table width="100%">
    <tr>
        <th><b>Date</b></th>
        <th ><b>Time</b></th>
        <th ><b>Transaction Type</b></th>
        <th ><b>Card Number</b></th>
        <th ><b>Agent ID</b></th>
        <th><b>Terminal ID</b></th>
        <th><b>Acquiring Bank</b></th>
        <th><b>From Account</b></th>
        <th><b>To Account</b></th>
        <th><b>Acquirer Reference</b></th>
        <th><b>Amount</b></th>
        <th><b>Charges </b></th>
        <th><b>Transaction Status</b></th>
    </tr>
    <?php
    foreach ($transactions as $transaction) {
        $date = \Carbon\Carbon::parse($transaction->created_at)->format('d-m-Y');
        $time = \Carbon\Carbon::parse($transaction->created_at)->format('H:i:s');
        $transactionType = $transaction->transaction_type;
        if ($transaction->card_number) {
            $cardNumber = \Illuminate\Support\Facades\Crypt::decryptString(trim($transaction->card_number));
        } else {
            $cardNumber = 'N/A';
        }
        $agentId = $transaction->agent_id;
        $terminalId = $transaction->terminal_id;
        $bin = $transaction->acquire_bank;
        $fromAccount = $transaction->from_account;
        $toAccount = $transaction->to_account;
        $reference = $transaction->rrn;
        $amount = $transaction->amount;
        $charge = $transaction->transaction_charge;

        if ($transaction->response_code == 200) {
            $status = 'SUCCESS';
        } elseif ($transaction->response_code == null) {
            $status = 'IN-PROGRESS';
        } else {
            $status = 'FAILED';
        }

        echo "

        <tr>
            <td>$date</td>
            <td>$time</td>
             <td>$transactionType</td>
            <td>$cardNumber</td>
            <td>$agentId</td>
            <td>$terminalId</td>
            <td>$bin</td>
            <td>$fromAccount</td>
            <td>$toAccount</td>
            <td>$reference</td>
            <td>$amount</td>
            <td>$charge</td>
            <td>$status</td>
        </tr>

        ";
    }

    ?>
</table>
