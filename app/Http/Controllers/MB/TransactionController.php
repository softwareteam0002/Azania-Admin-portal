<?php

namespace App\Http\Controllers\MB;

use App\EsbTransactions;
use App\Http\Controllers\Controller;
use App\MbTransactions;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(){
        abort_unless(\Gate::allows('mb_view_transactions'), 403);
        $trnxs = MbTransactions::where('transaction_channel','MB')->orderBy('id', 'DESC')->get();
        return view("mobile.transaction", compact('trnxs'));
    }


    public function viewTransactions(Request $r){
        abort_unless(\Gate::allows('mb_view_transactions_details'), 403);
        $id = $r->transaction_id;
        $transaction = MbTransactions::where('id',$id)->get()[0];
        return view("mobile.view_transactions",compact('transaction'));
    }

    public function reverseTransaction(Request $request) {

        $url = "http://172.20.1.37:8984/mkombozi/request/process/ib";

        $client = new Client;

        $infoRequest = [
            "serviceType"=> "REVERSAL",
            "serviceAccountId"=> "00129900153101",
            "mobile"=>"255654896656",
            "charge"=> "0",
            "transactionId"=> "1234567890",
            "channelType"=> "IB",
            "accountID"=> "00129900153101",
            "destinationAccountId"=>"00129900153101",
            "trxAmount"=> "100",
            "trxnDescription"=>"Description of transaction"
        ];

        $res = $client->request('POST', $url, [
            'json' => $infoRequest
        ]);


        $accountInfo            = $res->getBody();
        $accountDetail          =  json_decode($accountInfo);
        $responseCode           =  $accountDetail->responseCode;
        $responseMessage        =  $accountDetail->responseMessage;
        $transactionTimestamp   =  $accountDetail->transactionTimestamp;
        $transactionId          =  $accountDetail->transactionId;

        if ($responseCode == 100) {

            $notification =  $responseMessage;
            $color = "danger";
            return redirect('ib/accounts/index')->with('notification',$notification)->with('color',$color);
        }

        if($responseCode == 200) {
            //TODO: Update transaction table after received response IB/AB/ESB


        }


    }
}
