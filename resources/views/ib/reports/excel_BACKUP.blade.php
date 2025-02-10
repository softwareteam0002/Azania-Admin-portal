<table width="100%">
    <tr>
        <th><b>#</b></th>
        <th><b>Date</b></th>
        <th><b>Transaction ID</b></th>
        <th><b>Channel</b></th>
        <th><b>Transaction Type</b></th>
        <th><b>Service</b></th>
        <th><b>Account Number</b></th>
        <th><b>Dest. Account</b></th>
        <th><b>Ref. Number</b></th>
        <th><b>Status</b></th>
        <th class='tright'><b>Amount</b></th>
        <th class='tright'><b>Charges</b></th>
        <th><b>Sender's Name</b></th>
        <th><b>Receiver's Name</b></th>
        <th><b>Receiver's Bank</b></th>
        <th><b>Failure Reason</b></th>
        <th><b>Sender Mobile Number</b></th>
        <th><b>Receiver Mobile/Utility Number</b></th>
        <th><b>Auth Code</b></th>
    </tr>
    <?php
    $amount_total = 0;
    $charges_total = 0;
    $k = 1;
    foreach ($transactions as $transcation) {
        $transaction_response_message = "";
        // Sanitize transaction fields
        $transaction_id = preg_replace('/[^A-Za-z0-9\s\-]/', '', $transcation->transactionId);
        $transaction_channel = "IB";
        $transaction_name = preg_replace('/[^A-Za-z0-9\s\-]/', '', $transcation->serviceType);
        $amount = intval($transcation->trxAmount);
        $date = !empty($transcation->transactionTimestamp) ? date("Y-m-d H:i:s", strtotime($transcation->transactionTimestamp)) : date("Y-m-d H:i:s", strtotime($transcation->created_at));
        $charges = intval($transcation->charge);
        $transaction_status = preg_replace('/[^A-Za-z0-9\s\-]/', '', $transcation->responseCode);
        $sender_number = preg_replace('/[^A-Za-z0-9\s\-]/', '', $transcation->mobile_number);
        $receiver_number = preg_replace('/[^A-Za-z0-9\s\-]/', '', $transcation->utilityMsisdn);

        // Sanitize service type
        switch ($transcation->transfer_type_id) {
            case 1: $service = "GOVERNMENT"; break;
            case 2: $service = "LUKU PAYMENT"; break;
            case 3: $service = "TV SUBSCRIPTION"; break;
            case 4: $service = "INTERNET SUBSCRIPTION"; break;
            case 5: $service = "AIRTIME TOP UP"; break;
            case 6: $service = "WATER BILLS"; break;
            case 7: $service = "INSTITUTION PAYMENT"; break;
            case 8: $service = "INTERNAL ACCOUNT TRANSFER"; break;
            case 9: $service = "BANK TO WALLET"; break;
            case 11: $service = "WALLETS"; break;
            case 16: $service = "BULK IFT"; break;
            case 18: $service = "BULK EFT"; break;
            case 19: $service = "STANDING ORDER"; break;
            case 20: $service = "STOP CHEQUE"; break;
            case 22: $service = "TISS"; break;
            case 23: $service = "TT"; break;
            case 24: $service = "EFT"; break;
            case 26: $service = "LOAN REQUEST"; break;
            case 27: $service = "SALARY ADVANCE"; break;
            default: $service = ""; break;
        }

        if ($service == "" && $transcation->isStatement == 1) {
            $transaction_name = 'FULLSTATEMENT';
            $service = "FULLSTATEMENT";
        }

        if ($transaction_name == 'BANKTRANSFER') {
            if ($transcation->transfer_type_id == 22) {
                $transaction_name = 'TISSTransfer';
                $service = "TISS";
            } elseif ($transcation->transfer_type_id == 24) {
                $transaction_name = 'EFTTransfer';
                $service = "EFT";
            }
        }

        if ($transaction_status == "200" || $transaction_status == 200) {
            $transaction_response = "Success";
        } elseif ($transcation->status_id == 1 && is_null($transaction_status)) {
            $transaction_response = "On Progress";
        } else {
            $transaction_response = "Failed";
            $transaction_response_message = $transcation->responseMessage;
        }

        // Sanitize receiver bank
        if ($transcation->transfer_type_id == 8) {
            $receiver_bank = 'AKIBA COMMERCIAL BANK PLC';
        } else {
            $bank = \App\IbBank::where('bank_code', $transcation->bank_code)->first();
            $receiver_bank = $bank ? preg_replace('/[^A-Za-z0-9\s\-]/', '', $bank->name) : '';
        }

        // Sanitize sender and receiver names
        $receiver_name = !empty($transcation->beneficiary_name) ? preg_replace('/[^A-Za-z0-9\s\-]/', '', $transcation->beneficiary_name) : "--";
        $sender_name = preg_replace('/[^A-Za-z0-9\s\-]/', '', \App\IbAccount::where('accountID', $transcation->fromAccountID)->first()->accountName ?? $transcation->sender_name);

        // Sanitize account and reference fields
        $account_number = preg_replace('/[^A-Za-z0-9\s\-]/', '', $transcation->fromAccountID);
        $dest_account = preg_replace('/[^A-Za-z0-9\s\-]/', '', $transcation->toAccountID ?? "--");
        $reference_number = preg_replace('/[^A-Za-z0-9\s\-]/', '', $transcation->reference_number ?? "--");
        $authCode = preg_replace('/[^A-Za-z0-9\s\-]/', '', $transcation->authCode);

        // Output sanitized data
        echo "
        <tr>
            <td>$k</td>
            <td>$date</td>
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
            <td>$sender_name</td>
            <td>$receiver_name</td>
            <td>$receiver_bank</td>
            <td>$transaction_response_message</td>
            <td>$sender_number</td>
            <td>$receiver_number</td>
            <td>$authCode</td>
        </tr>
        ";
        $k++;
    }

    // Output total amounts
    echo "
    <tr>
        <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
    </tr>
    <tr>
        <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td><u><b>TOTAL:</b></u></td>
        <td class='tright'><u><b>" . number_format($amount_total, 2) . "</b></u></td>
        <td class='tright'><u><b>" . number_format($charges_total, 2) . "</b></u></td>
    </tr>
    ";
    ?>
</table>
