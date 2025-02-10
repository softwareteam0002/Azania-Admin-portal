<?php

namespace App\Http\Controllers\ESB;

use App\EsbTransactions;
use App\Http\Controllers\Controller;
use App\IBServiceAccount;
use App\MbTransactions;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
//        $sql = "SELECT `transaction`.*, `currencies`.`currency_name` AS currency, `transaction_type`.`name` AS trxn_type FROM `transaction`, `transaction_type`, `currencies` WHERE transaction.transaction_type_id = transaction_type.id AND transaction.currency_id = currencies.id ORDER BY transaction_date DESC";
        $sql = "SELECT * FROM tbl_esb_transaction ORDER BY transaction_date DESC";
        $trxns = DB::connection('sqlsrv3')->select($sql);
        $success = $this->pendings();
        $fails = $this->fails();
        $pendings = $this->pendings();
        return view("esb.transaction.index", compact('trxns', 'fails', 'pendings', 'success'));
    }

    public function pendings()
    {
        $sql = "SELECT `transaction`.*, `currencies`.`currency_name` AS currency, `transaction_type`.`name` AS trxn_type FROM `transaction`, `transaction_type`, `currencies` WHERE transaction.transaction_type_id = transaction_type.id AND transaction.currency_id = currencies.id AND transaction.transaction_status=" . "9999";
        $trxns = DB::connection('sqlsrv3')->select($sql);
        return $trxns;
    }

    public function success()
    {
        $sql = "SELECT `transaction`.*, `currencies`.`currency_name` AS currency, `transaction_type`.`name` AS trxn_type FROM `transaction`, `transaction_type`, `currencies` WHERE transaction.transaction_type_id = transaction_type.id AND transaction.currency_id = currencies.id AND transaction.transaction_status=" . "200";
        $trxns = DB::connection('sqlsrv3')->select($sql);
        return $trxns;
    }

    public function fails()
    {
        $sql = "SELECT `transaction`.*, `currencies`.`currency_name` AS currency, `transaction_type`.`name` AS trxn_type FROM `transaction`, `transaction_type`, `currencies` WHERE transaction.transaction_type_id = transaction_type.id AND transaction.currency_id = currencies.id AND transaction.transaction_status=" . "090";
        $trxns = DB::connection('sqlsrv3')->select($sql);
        return $trxns;
    }

    public function connection_status()
    {
        $sql = "SELECT [name]
      ,[entity_name]
      ,[type]
      ,[IP_Address]
      ,[PORT]
      ,[status]
      ,[timeConnected]
      ,[timeDisconnected]
      ,[state]
  FROM [dbo].[Client_Connections]
";
        $trnxs = DB::connection('sqlsrv3')->select($sql);

        return view("esb.connection.connection_status", compact('trnxs'));

    }

//    public function transactions_list()
//    {
//        $service_ac = IBServiceAccount::first();
//        $sql = "SELECT [id]
//      ,[transaction_channel]
//      ,[transaction_name]
//      ,[transaction_amount]
//      ,[transaction_date]
//      ,[transaction_id]
//      ,[transaction_status]
//      ,[transaction_response]
//      ,[transaction_thirdpart_status]
//      ,[transaction_thirdpart_response]
//      ,[transaction_thirdpart_data]
//      ,[transaction_thirdpart_ref]
//      ,[transaction_is_reversed]
//  FROM [dbo].[tbl_esb_transaction] ORDER BY transaction_date DESC";
//
//        $trnxs = DB::connection('sqlsrv3')->select($sql);
//
//        return view("esb.transaction.index", compact('trnxs', 'service_ac'));
//
//    }

    public function transactions_list(Request $request)
    {
        $service_ac = IBServiceAccount::first();


        // Get the transaction ID from the request
        $transactionId = $request->input('transaction_id');

        // Get start and end dates from the request, defaulting to null if not provided
        $startDate = $request->input('start_date') ? $request->input('start_date') . " 00:00:00" : null;
        $endDate = $request->input('end_date') ? $request->input('end_date') . " 23:59:59" : null;
        $query = MbTransactions::select([
            'id',
            'transaction_channel',
            'transaction_name',
            'transaction_amount',
            'transaction_date',
            'transaction_id',
            'transaction_status',
            'transaction_response',
            'transaction_thirdpart_status',
            'transaction_thirdpart_response',
            'transaction_thirdpart_data',
            'transaction_thirdpart_ref',
            'transaction_is_reversed'
        ]);

        // Check if transaction ID is present
        if ($transactionId) {
            // Filter by the transaction ID
            $trnxs = $query->where('transaction_id', trim($transactionId))->orderBy('transaction_date', 'DESC')->get();
        } elseif ($startDate && $endDate) {
            // Filter by the date range
            $trnxs = $query->whereBetween('transaction_date', [$startDate, $endDate])->orderBy('transaction_date', 'DESC')->limit(300)->get();
        } else {
            // If no filters are applied, return the latest 20 transactions
            $trnxs = $query->orderBy('transaction_date', 'DESC')->limit(20)->get();
        }

        // Return the transactions and service account to the view
        return view('esb.transaction.index', compact('trnxs', 'service_ac'));
    }


    public function viewTransactions($id)
    {

        $transaction = EsbTransactions::where('id', $id)->get()[0];
        $service_ac = IBServiceAccount::first();
        return view("esb.transaction.view", compact('transaction', 'service_ac'));
    }

    public function reverseTransaction(Request $request)
    {

        $url = "http://172.20.1.37:8984/mkombozi/request/process/ib";

        $client = new Client;

        $infoRequest = [
            "serviceType" => "REVERSAL",
            "serviceAccountId" => "00129900153101",
            "mobile" => "255654896656",
            "charge" => "0",
            "transactionId" => "1234567890",
            "channelType" => "IB",
            "accountID" => "00129900153101",
            "destinationAccountId" => "00129900153101",
            "trxAmount" => "100",
            "trxnDescription" => "Description of transaction"
        ];

        $res = $client->request('POST', $url, [
            'json' => $infoRequest
        ]);


        $accountInfo = $res->getBody();
        $accountDetail = json_decode($accountInfo);
        $responseCode = $accountDetail->responseCode;
        $responseMessage = $accountDetail->responseMessage;
        $transactionTimestamp = $accountDetail->transactionTimestamp;
        $transactionId = $accountDetail->transactionId;

        if ($responseCode == 100) {

            $notification = $responseMessage;
            $color = "danger";
            return redirect('ib/accounts/index')->with('notification', $notification)->with('color', $color);
        }

        if ($responseCode == 200) {
            //TODO: Update transaction table after received response IB/AB/ESB


        }


    }
}
