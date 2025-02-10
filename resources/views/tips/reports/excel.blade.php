<table style="width:100%">
    <tr>
        <th><b>S/N</b></th>
        <th><b>Date</b></th>
        <th><b>Payer Reference No</b></th>
        <th><b>Payer Identifier Type</b></th>
        <th><b>Payer Identifier No</b></th>
        <th><b>Payer FSP Name</b></th>
        <th><b>Payer Full Name</b></th>
        <th><b>Payer Account Category</b></th>
		<th><b>Payer Account Type</b></th>
        <th><b>Payer ID Type</b></th>
		<th><b>Payer ID Value</b></th>
		<th><b>Payee Reference No</b></th>
		<th><b>Payee Identifier Type</b></th>
        <th><b>Payee Identifier</b></th>
        <th><b>Payee FSP Name</b></th>
        <th><b>Payee Full Name</b></th>
        <th><b>Payee Account Category</b></th>
		<th><b>Payee Account Type</b></th>
        <th><b>Payee ID Type</b></th>
		<th><b>Amount</b></th>
        <th><b>Currency</b></th>
        <th><b>Enduser Fee Amount</b></th>
        <th><b>Enduser Fee Currency</b></th>
        <th><b>Transaction Type</b></th>
        <th><b>Transaction Initiator</b></th>
		<th><b>Transaction Initiator Type</b></th>
		<th><b>Description</b></th>
        <th><b>Transaction ID</b></th>
        <th><b>Status</b></th>
        <th><b>Switch Reference No</b></th>
        <th><b>Transfer State</b></th>
        <th><b>Reversal State</b></th>
		<th><b>Reversal Reason</b></th>
        <th><b>Reversal Reference No</b></th>
		<th><b>Payer Reversal Reference </b></th>
        <th><b>Switch reversal Reference</b></th>
        <th><b>Payee Reversal Reference</b></th>
        <th><b>Hold Status</b></th>
    </tr>
    <?php
    $amount_total = 0;
    $charges_total = 0;
    $k = 1;
    foreach ($transactions as $transaction) {
		
        $hold_status = $transaction->holdStatus;
        $payeeRevRef = $transaction->payeeReversalRef;
        $switchRevRef = $transaction->switchReversalRef;
        $payerRevRef = $transaction->payerReversalRef;
        $RevRef_no = $transaction->reversalRef;
		$rev_reason = $transaction->reversalReason;
        $rev_state = $transaction->reversalState;
        $transfer_state = $transaction->transferState;
        $switch_ref = $transaction->switchRef;
        $status = $transaction->status;
		$transaction_id = $transaction->transaction_id;
        $transaction_initiatorType = $transaction->transactionType_initiatorType;
        $descriptions = $transaction->description;
		$transactionType_initiator = $transaction->transactionType_initiator;
        $transactionType = $transaction->transactionType_scenario;
        $endUserFee_amount = $transaction->endUserFee_amount;
		$endUserFee_currency = $transaction->endUserFee_currency;
        $currency = $transaction->currency;
        $amount = $transaction->amount;
        $payee_id_value = $transaction->payee_identity_value;
        $payee_id_type = $transaction->payee_identity_type;
		$payee_accountType = $transaction->payee_accountType;
        $payee_accountCategory = $transaction->payee_accountCategory;
        $payee_fullName = $transaction->payee_fullName;
		$payee_fspId = $transaction->payee_fspId;
        $payeeRef = $transaction->payeeRef;
        $payee_identifier = $transaction->payee_identifier;
		$payee_identifierType = $transaction->payee_identifierType;
		$payee_identity_value = $transaction->payee_identity_value;
        $payer_identitytype = $transaction->payer_identity_type;
        $payer_accountType = $transaction->payer_accountType;
		$payer_id_value = $transaction->payer_identity_value;
        $payer_accountCategory = $transaction->payee_accountCategory;
        $payer_fullName = $transaction->payer_fullName;
		$payer_fspId = $transaction->payer_fspId;
        $payer_identifier = $transaction->payer_identifier;
        $payer_identifierType = $transaction->payer_identifierType;
		$payerRef = $transaction->payerRef;
		$date = $transaction->transaction_date;
		
		
        //convert the date to Y-m-d
        $date = date("d-m-Y", strtotime($date));

       
        //parse the status to define a text

        /*if ($transaction_status == "200") {
            $transaction_response  = "Succces";
        } elseif ($transaction_status == null) {
            $transaction_response  = "On Progress";
        } else {
            $transaction_response  = "Failed";
        }
        $transaction_serialID = $transaction->transaction_serialID;
        $transaction_batchID = $transaction->transaction_batchID;
        $amount_total = ($amount_total + $amount);
        $charges_total = ($charges_total + $charges);*/
        echo "
        
        <tr>
            <td>$k</td>
            <td>$date</td>
            <td>$payerRef</td>
            <td>$payer_identifierType</td>
            <td>$payer_identifier</td>
            <td>$payer_fspId</td>
            <td>$payer_fullName</td>
            <td>$payer_accountCategory</td>
			<td>$payer_accountType</td>
            <td>$payer_identitytype</td>
			<td>$payer_id_value</td>
            <td>$payeeRef</td>
            <td>$payee_identifierType</td>
            <td>$payee_identifier</td>
            <td>$payee_fspId</td>
            <td>$payee_fullName</td>
            <td>$payee_accountCategory</td>
			<td>$payee_accountType</td>
            <td>$payee_id_type</td>
            <td>$amount</td>
            <td>$currency</td>
            <td>$endUserFee_amount</td>
            <td>$endUserFee_currency</td>
            <td>$transactionType</td>
			<td>$transaction_initiatorType</td>
			<td>$transactionType_initiator</td>
			<td>$descriptions</td>
			<td>$status</td>
			<td>$transaction_id</td>
            <td>$switch_ref</td>
            <td>$transfer_state</td>
			<td>$rev_state</td>
            <td>$rev_reason</td>
            <td>$RevRef_no</td>
            <td>$payerRevRef</td>
            <td>$switchRevRef</td>
			<td>$payeeRevRef</td>
            <td>$hold_status</td>
            
        </tr>
        
        ";
        $k++;
    }

    echo "
        
    ";

    ?>
</table>