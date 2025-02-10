<table width="100%">
				<tr>
								<th><b>S/N</b></th>
								<th><b>Date</b></th>
								<th><b>Institute Name</b></th>
								<th><b>Payer Account</b></th>
								<th><b>Batch Number</b></th>
								<th><b>Batch ID</b></th>
								<th><b>Description</b></th>
								<th><b>Payee Name</b></th>
								<th><b>Payee Account</b></th>
								<th><b>Payee Bank Name</b></th>
								<th><b>Payee Amount</b></th>
								<th><b>Serial ID</b></th>
								<th><b>Status</b></th>
								<th class='tright'><b>Batch Total Amount</b></th>
				</tr>

				<?php
        $amount_total = 0;
        $charges_total = 0;
        $k = 1;
        foreach ($transactions as $transaction) {
            $date = $transaction->created_at;
            $date = date("d-m-Y", strtotime($date));
            $institute_name = $transaction->institute_name;
            $batch_no = $transaction->batch_number;
            $batch_id = $transaction->batchID;
            $descriptions = $transaction->description;
            $account_no = $transaction->account_number;
            $payee_name = $transaction->payee_name;
            $payee_account = $transaction->payee_account;
            $serial_id = $transaction->serialID;
            $status = $transaction->responseMessage;
            $payee_amount = $transaction->payee_amount;
            $payee_bank_name = $transaction->payee_bank_name;
            $batch_total_amount = $transaction->batch_total_amount;
            $transaction_channel = "IB";
            $response_code = $transaction->responseCode;

            //parse the status to define a text
            switch ($service_id) {
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

            if ($response_code == "200") {
                $status  = "Succces";
            } elseif ($response_code == null) {
                $status  = "On Progress";
            } else {
                $status  = "Failed";
            }

            echo "
    ?>
				<tr>
								<td>$k</td>
								<td style='width:130px;'>$date</td>
								<td>$institute_name</td>
								<td>$account_no</td>
								<td>$batch_no</td>
								<td>$batch_id</td>
								<td>$descriptions</td>
								<td>$payee_name</td>
								<td>$payee_account</td>
								<td>$payee_bank_name</td>
								<td>$payee_amount</td>
								<td>$serial_id</td>
								<td>$status</td>
								<td class='tright'>" . number_format($batch_total_amount, 2) . "</td>
				</tr>

				";
				$k++;
				}

				?>
</table>
