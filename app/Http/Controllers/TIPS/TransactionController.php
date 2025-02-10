<?php
//uat
namespace App\Http\Controllers\TIPS;

use App\Http\Controllers\Controller;
use App\Models\Tips\Transaction;
use App\Traits\Responsemessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request as Input;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    use Responsemessage;

    private $responseCode;
    private $responseMessage;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\https\Response
     */
    public function index()
    {

        if(Input::get('search'))
        {
            $value =  Input::get('search');
            $transactions = Transaction::search($value)->orderBy('id', 'DESC')->paginate(7);
        }
        else
        {
            $transactions = Transaction::orderBy('id', 'DESC')->paginate(7);
        }



        return view('tips.transactions.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\https\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\https\Request  $request
     * @return \Illuminate\https\Response
     */

    //transaction request post for ESB
    public function store(Request $request)
    {

        try{
            Log::info("Transaction Request :".json_encode($request->all()));

           
            $duplicate_transaction = Transaction::where("payerRef",$request->payerRef)->first();
            if($duplicate_transaction)
            {
                $this->responseCode="4006";
                $this->responseMessage="Transfer request from payer with duplicate reference";
            }
            else
            {
            	$transaction_id = mt_rand(1234567890,9999999999);
            	$saved = Transaction::create([
                'lookup_id' => $request->lookup_id,
                "payerRef" => $request->payerRef,
                "payer_identifierType" => $request->payer_identifierType,
                "payer_identifier" => $request->payer_identifier,
                "payer_fspId" => $request->payer_fspId,
                "payer_fullName" => $request->payer_fullName,
                "payer_accountCategory" => $request->payer_accountCategory,
                "payer_accountType" => $request->payer_accountType,
                "payer_identity_type" => $request->payer_identity_type,
                "payer_identity_value" => $request->payer_identity_value,
                "payeeRef" => $request->payeeRef,
                "payee_identifierType" => $request->payee_identifierType,
                "payee_identifier" => $request->payee_identifier,
                "payee_fspId" => $request->payee_fspId,
                "payee_fullName" => $request->payee_fullName,
                "payee_accountCategory" => $request->payee_accountCategory,
                "payee_accountType" => $request->payee_accountType,
                "payee_identity_type" => $request->payee_identity_type,
                "payee_identity_value" => $request->payee_identity_value,
                "amount" => $request->amount,
                "currency" => $request->currency,
                "endUserFee_amount" => $request->endUserFee_amount,
                "endUserFee_currency" => $request->endUserFee_currency,
                "transactionType_scenario" => $request->transactionType_scenario,
                "transactionType_initiator "=> $request->transactionType_initiator,
                "transactionType_initiatorType"  => $request->transactionType_initiatorType,
                "description" => $request->description,
                "transaction_id" => $transaction_id,
                "status" => 1,
                "transaction_date"   => $request->transaction_date,
                ]);
                if($saved)
                {
                    $this->responseCode="200";
                    $this->responseMessage="Success";
                }
                else
                {
                    $this->responseCode="201";
                    $this->responseMessage="Failed to save transaction";
                }
            }
            if($this->responseCode==200){
                $request->status = 'RECEIVED';
                $reasonCode = 60;
            }
            else
            {
                $request->status = 'ABORTED';
            }

            Log::info("Transaction Response :".$this->responseMessage($this->responseCode,$this->responseMessage));
            return $this->responseMessage($this->responseCode,$this->responseMessage);

        }
        catch(\Exception $e)
        {
            $this->responseCode="400";
            $customMessage="Bad transaction request";
            return $this->responseMessage($this->responseCode,$e->getMessage(),$customMessage);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\https\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\https\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\https\Request  $request
     * @param  int  $id
     * @return \Illuminate\https\Response
     */


    //transaction reversal outgoing form request/submission
    public function update(Request $request, $id)
    {
        $reverse_transaction = Transaction::where("payerRef",$request->payerRef)->first();
        if($reverse_transaction)
        {
            $payerReversalRef = $request->payer_fspId.'-'.mt_rand(1234567890,9999999999);
            $reverse_transaction->reversalReason = $request->reversalReason;
            $reverse_transaction->reversalRef = $payerReversalRef;
            $reverse_transaction->reversalState = 'INPROGRESS';
            $reverse_transaction->save();

            $curl = curl_init();
            $url = "http://172.29.1.133:8984/esb/api/messageTransfersReversal";
            $amount = ["amount"=>$request->amount, "currency"=>$request->currency];
            $post = [
                'payerReversalRef' => $payerReversalRef,
                'payerRef' => $request->payerRef,
                'payeeRef' => $request->payeeRef,
                'switchRef' => $request->switchRef,
                'amount' => $amount,
                'fspSource'=>$request->payer_fspId,
                'fspDestination'=>$request->payee_fspId,
                'reversalReason' => $request->reversalReason,
                //'reversalRef' => $request->reversalRef,
            ];

            $json_string = json_encode($post);

            Log::info("Reversal Request :".$json_string);

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );

            $result = curl_exec($curl);

            curl_close($curl);
            $response = json_decode($result);
            Log::info("Reversal Response: ".$result);
	            if($response->responseCode)
            {
                if($response->responseCode == 200)
                {
                    $this->responseMessage = $response->responseMessage;
                    return back()->with('success',$this->responseMessage);
                }
                else
                {
                    $this->responseCode="100";
                    $this->responseMessage="Failed";
                    return back()->with('error',$this->responseMessage);
                }
            }
            else
            {
                $this->responseCode= $this->status;
                $this->responseMessage=$error;
                return back()->with('error',$this->responseMessage);
            }
        }
        else
        {
            $this->responseCode="101";
            $this->responseMessage="No transaction found";
            return back()->with('error',$this->responseMessage);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\https\Response
     */
    public function destroy($id)
    {
        //
    }

    //transaction confirmation callback
    public function tipsConfirmation(Request $request,$id)
    {
        $post = [
            'payeeRef' => $request->payeeRef,
            'transferState' => $request->transferState,
            'reasonCode' => $request->reasonCode
        ];

        // API URL
        $url = 'https://172.29.1.93:8003/api/tips/transfers/'.$id;
        // return json_encode($url);
        $payload = json_encode($post);
        Log::info("Tips Confirmation Request: ".$payload);
        $ch1 = curl_init($url);
        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch1, CURLOPT_POSTFIELDS, http_build_query($post));
        $result = curl_exec($ch1);
        curl_close($ch1);
        $responsex = json_decode($result);
        Log::info("Tips Confirmation Response: ".$result);
        return $result;

    }
	
	public function tipsTransactionConfirmation(Request $request)
    {
		Log::info("TIPS TRANSACTION CONFIRMATION REQUEST: ".json_encode($request->all()));
		$mbUrl = 'https://172.29.1.93:8000/api/esb/';
		$ibUrl = 'https://172.29.1.93:8001/api/esb/tips_callback';
		$esbUrl = 'http://172.29.50.133:8984/esb/api/submit/transferStatus/request';
		$abUrl = 'http://172.29.1.93:8005/AgencyBanking/services/Transactions/receiveTIPSCallbackFromESB';
		
		$transaction_details = [
			"payerRef"=>$request->payerRef,
			"fspSource"=>$request->payerRef,
			"fspDestination"=>$request->payerRef
		];
      
		
		try{
			$response = Http::post($esbUrl, $transaction_details);
			Log::info("TIPS CONFIRMATION RESPONSE: ",['message'=>$response]);
		
			if($response){
			$callback_payload = [				
					"payerRef"=> $response->payerRef,
					"payeeRef"=> $response->payeeRef,
					"switchRef"=> $response->switchRef,
					"completedTimestamp"=> $response->completedDate,
					"transferState"=> $response->transferState,
				];
			}
	
			$parsedAsRequest = $this->parseResponseToRequest($callback_payload);
			this->notification($parsedAsRequest);
			$channel = $response->payerRef;
			$substring = substr($channel, 4, 3);
			
			if($substring === '-AB'){
				$uri = $abUrl;
				
			}
			
			if ($substring === '-IB'){
				$uri = $ibUrl;
				
			}
			
			if ($substring === '-MB'){
				$uri = $mbUrl;
				$callback_payload = http_build_query($callback_payload);
			}
			
			Http::post($uri, $callback_payload);
			
		}catch (\Exception $e){
			Log::info("TIPS TRANSACTION CONFIRMATION ERROR: ",['message'=>$e]);
		}
	
    }
	
	private function parseResponseToRequest($response)
	{
		$content = $response->json(); 

		$request = Request::create(
			'',
			'POST',
			$content,
			[],
			[],
			[],
			null
		);

		return $request;
	}

    //transaction notification/response callback
    public function notification(Request $request)
    {
        Log::info("Transaction Callback Request :".json_encode($request->all()));
        $transaction = Transaction::where("payerRef",$request->payerRef)->first();
        if($transaction)
        {
            if($request->transferState == 'COMMITTED' && !empty($request->input('switchRef')))
            {
                $responseCode="200";
                $responseMessage=$request->input('transferState');
            }else if($request->transferState == 'COMMITTED')
            {
                $responseCode="200";
                $responseMessage=$request->input('transferState');
            }
            else if($request->input('transferState') == 'ABORTED')
            {
                $responseCode="100";
                $responseMessage=$request->input('transferState');
            }
            else
            {
                $errorDescription = $request->errorInformation ? $request->errorInformation['errorDescription'] : null;
                $errorCode = $request->errorInformation ? $request->errorInformation['errorCode'] : null;

                $responseCode=$errorCode ?? '100';
                $responseMessage='ABORTED';
            }
            $transaction->payeeRef = $request->payeeRef;
			   $transaction->response_message = $errorDescription;
            $transaction->response_code = $responseCode;


	    if(!empty($request->switchRef))
            {
                 $transaction->switchRef = $request->switchRef;           
	  }

	  if(!empty($request->completedTimestamp))
            {
                 $transaction->completedTimestamp = $request->completedTimestamp;           
	  }
			
            $transaction->transferState = $request->transferState ?? $responseMessage;
            $transaction->save();
            $status = "RECEIVED";
            $post = [
                "responseCode"=>"200",
                "payerRef"=> $request->payerRef,
                "status"=> $status,
                "datetime"=> date('Y-m-d H:i:s')
            ];

        }
        else
        {

            $post = [
                "errorCode" => "100",
                "errorDescription" => "Transaction not found"
            ];
            $errorInformation =  array("errorInformation"=>$post);

            //return json_encode($errorInformation);
        }
        Log::info("Transaction Callback Response :".json_encode($post));
        return json_encode($post);
    }

//transaction details inquiry
    public function inquiry(Request $request)
    {
        $curl = curl_init();
        $url = "https://172.29.1.93:8003/api/tips/transfers/esbMockup";
        $post = [
            'payerRef' => $request->payerRef
        ];

        $json_string = json_encode($post);
        Log::info("Transaction Inquiry Request :".$json_string);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );

        $result = curl_exec($curl);
        Log::info("Transaction Inquiry Response :".$result);
        curl_close($curl);
        return $result;
    }

    //transaction details inquiry incoming
    public function inquiryIncoming(Request $request)
    {

        Log::info("Transaction Inquiry Incoming Request :".json_encode($request->all()));
        $transaction = Transaction::where('payerRef',$request->payerRef)->first(['lookup_id','payerRef','payer_identifierType','payer_identifier','payer_fspId','payer_fullName','payer_accountCategory','payer_accountType','payer_identity_type','payer_identity_value','payee_identifierType','payee_identifier','payeeRef','payee_fspId','payee_fullName','payee_accountCategory','payee_accountType','payee_identity_type','payee_identity_value','amount','currency','endUserFee_amount','endUserFee_currency','transactionType_scenario','transactionType_initiator','transactionType_initiatorType','description','transaction_id','response_code','response_message','status','switchRef','completedTimestamp','transferState','reversalState','reversalReason','reversalRef','reversal_completedTimestamp','transaction_date']);

        if($transaction)
        {
            $responseCode="200";
            $responseMessage="Success";
            Log::info("Transaction Inquiry Incoming Response :".json_encode($transaction));
            return json_encode(array("responseCode"=>$responseCode, "responseMessage"=>$responseMessage,"transaction"=>$transaction));
        }
        else
        {
            $responseCode="400";
            $responseMessage="No transaction found";
            Log::info("Transaction Inquiry Incoming Response :".json_encode(array("responseCode"=>$responseCode, "responseMessage"=>$responseMessage)));
            return json_encode(array("responseCode"=>$responseCode, "responseMessage"=>$responseMessage));
        }

    }

    //transaction reversal callback
    public function reversal(Request $request)
    {
        try{
			$transaction = Transaction::where("payerRef",$request->payerRef)->first();
			$serviceAccountId = '24600124';
			$channel = "TP";
			$transactionId = $this->getTransactionID($channel);
			Log::info("Transaction Reversal Callback Request :".json_encode($request->all()));

        if($transaction)
        {
            $transaction->reversalState = $request->reversalState;
            $transaction->reversalRef = $request->reversalRef;
            $transaction->reversal_completedTimestamp = $request->completedTimestamp;
            $transaction->settlementWindowId = $request->settlementWindowId;
            $transaction->switchReversalRef = $request->switchReversalRef;
            $transaction->payerReversalRef = $request->payerReversalRef;
            $transaction->payeeReversalRef = $request->payeeReversalRef;
            $saved = $transaction->save();
            $status = "RECEIVED";
			
			$post = [
                "responseCode" => "200",
                "message" => "reversal callback updated successfully"
			];
			
			if($request->reversalState == 'REVERSED'){
				$serviceType = 'REVERSAL';
              
                $transaction_id = substr($request->payerRef,4);
		
				$first_two_letters = substr($transaction_id,0,2);
				
				if(str_contains($first_two_letters,'IB')){
					$ib_transaction = DB::connection('sqlsrv2')
					->table('tbl_transactions')
					->select('authCode')
					->where('transactionId',$transaction_id)
					->first();
					
					$auth_id = $ib_transaction->authCode;
				}
				
				if(str_contains($first_two_letters,'MB')){
					$mb_transaction = DB::connection('sqlsrv6')
					->table('mx_transaction')
					->select('authID')
					->where('int_transaction_number',$transaction_id)
					->first();
			
				
					$auth_id = $mb_transaction->authID;
				}
				
                $post =	[
                    "serviceType" => $serviceType,
                    "authId"	  => $auth_id,

                ];

                Log::info("Outgoing Reversal Credit Request: ".json_encode($post));
                $curl = curl_init();
                $url = "http://172.29.1.133:8984/esb/request/process/mb";
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post));
                curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                $result = curl_exec($curl);

                curl_close($curl);
                $reversalResponse = json_decode($result);
				Log::info("Outgoing Reversal Credit Response: ".json_encode($reversalResponse));
				
				
			}
			
        }
        else
        {

            $post = [
                "errorCode" => "100",
                "errorDescription" => "Transaction not found"
            ];
            $post =  array("errorInformation"=>$post);
        }
        Log::info("Transaction Reversal Callback Response :".json_encode($post));
		}catch(\Exception $e){
			 Log::info("Reversal-Error:".json_encode($e->getMessage()));
		}
    }

    //transaction reversal inquiry
    public function reversalInquiry(Request $request)
    {
        $curl = curl_init();
        $url = "https://172.29.1.93:8003/api/tips/transfers/esbMockup";
        $post = [
            'reversalRef' => $request->reversalRef,
            'payerRef' => $request->payerRef,
            'payeeRef' => $request->payeeRef
        ];
        $json_string = json_encode($post);
        Log::info("Transaction Inquiry Request :".$json_string);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );

        $result = curl_exec($curl);

        curl_close($curl);
        Log::info("Transaction Inquiry Response :".$result);
        return $result;
    }

    //transaction reversal incoming form request/submission
    public function receiveReversal(Request $request)
    {
		Log::info("Incoming Reversal Request: ".json_encode($request->all()));
		
        $reverse_transaction = Transaction::where("payerRef",$request->payerRef)->first();
        if($reverse_transaction)
        {
            $reverse_transaction->reversalReason = $request->reversalReason;
            $reverse_transaction->payerReversalRef = $request->payerReversalRef;
            if( $request->reversalState == "CONFIRMED_WITHDRAW" ||  $request->reversalState == "CONFIRMED_HOLD" ){
                $reverse_transaction->reversalState  = $request->reversalState;
            }else{
                $reverse_transaction->reversalState = 'INPROGRESS';
            }
			$reverse_transaction->freezingId = $request->freezingId;
			$reverse_transaction->holdStatus = $request->holdStatus;
			
            $reverse_transaction->save();
            if($reverse_transaction)
            {
                $this->responseCode="200";
                $this->responseMessage="INPROGRESS";
                $post = [
                    'responseCode' => $this->responseCode,
                    'responseMessage' => $this->responseMessage,
                    'payerReversalRef' => $request->payerReversalRef,
                    'payerRef' => $request->payerRef,
                    'payeeRef' => $request->payeeRef,
                    'switchRef' => $request->switchRef,
                    'reversalReason' => $request->reversalReason,

                ];
            }
            else
            {
                $this->responseCode="100";
                $this->responseMessage="ABORTED";
                $post = [
                    'responseCode' => $this->responseCode,
                    'responseMessage' => $this->responseMessage,
                    'payerReversalRef' => $request->payerReversalRef,
                    'payerRef' => $request->payerRef,
                    'payeeRef' => $request->payeeRef,
                    'switchRef' => $request->switchRef,
                    'reversalReason' => $request->reversalReason
                ];
            }

        }
        else
        {
            $this->responseCode="100";
            $this->responseMessage="No transaction found";
            $post = [
                'responseCode' => $this->responseCode,
                'responseMessage' => $this->responseMessage,
                'payerReversalRef' => $request->payerReversalRef,
                'payerRef' => $request->payerRef,
                'payeeRef' => $request->payeeRef,
                'switchRef' => $request->switchRef,
                'reversalReason' => $request->reversalReason
            ];
        }
		Log::info("Incoming Reversal Response: ".json_encode($post));
        return json_encode($post);

    }


    //transaction reversal form/view
    public function reverse($id)
    {
        $transaction = Transaction::where("id",$id)->first();
        return view('tips.transactions.reverse', compact('transaction'));
    }
    //transaction reversal incoming form/view
    public function reverseincoming($id)
    {
        $transaction = Transaction::where("id",$id)->first();
        return view('tips.transactions.reverseincoming', compact('transaction'));
    }

    //transaction reversal commit/reject request/submission
     public function reversal_commit(Request $request, $id)
    {
        $channel = "TP";
        $transactionId = $this->getTransactionID($channel);
        $serviceAccountId = '24600124';
        $reverse_transaction = Transaction::where("payerRef", $request->payerRef)->first();
        if ($request->reject == 'reject') {
            if ($reverse_transaction) {
                //unfreezing transaction process
                $serviceType = 'UNFREEZE';
                $post =	[
                    "serviceType" => $serviceType,
                    "frozenId"	  => $reverse_transaction->freezingId,

                ];

                Log::info("Unfreezing transaction Request: ".json_encode($post));
                $curl = curl_init();
                $url = "http://172.29.1.133:8984/esb/request/process/mb";
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post));
                curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                $result = curl_exec($curl);

                curl_close($curl);
                $unfreezeResponse = json_decode($result);
                Log::info("Unfreezing transaction Response: ".$result);

                if ($unfreezeResponse->responseCode == 200) {
                     $post =    [
                        "fspDestination"=>$request->payer_fspId,
                        "fsSource"=>$request->payee_fspId,
                        "newReversalState"=>'CANCELLED',
                        "reversalReason"=>$request->reversalReason ?? 'Reversal cancelled by payee',
                        "payerRef"=>$request->payerRef,
                        "mkcbRefIdVariable" => $reverse_transaction->payeeRef . "99"
                    ];

                    $reverse_transaction->reversalState = 'CANCELLED';
                    $reverse_transaction->save();
                   

                    Log::info("Cancel Reversal Request: ".json_encode($post));
                    $curl = curl_init();
                    $url = "http://172.29.1.133:8984/esb/api/cancellingReversalRequest";
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post));
                    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                    $result = curl_exec($curl);

                    curl_close($curl);
                    $transferAmtReversalResponse = json_decode($result);
                    Log::info("Cancel Reversal Response: ".$result);
                    unset($post);
                    if ($transferAmtReversalResponse->responseCode == 200) {
                        return back()->with('success', 'Reversal cancelled successfully, TIPS updated');
                    } else {
                        return back()->with('error', 'Reversal cancelling failed: '.$transferAmtReversalResponse->responseMessage);
                    }
                
                } else {
                    return back()->with('error', 'Unfreezing process failed: '.$unfreezeResponse->responseMessage);
                }
            } else {
                return back()->with('error', 'Requested transaction not found');
            }
        } elseif ($request->commit == 'commit') {
            //unfreeze process begins
            $serviceType = 'UNFREEZE';
            $post =	[
                "serviceType" => $serviceType,
                "frozenId"	  => $reverse_transaction->freezingId,

            ];

            Log::info("Unfreezing transaction Request: ".json_encode($post));
            $curl = curl_init();
            $url = "http://172.29.1.133:8984/esb/request/process/mb";
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post));
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

           $result = curl_exec($curl);

            curl_close($curl);
            $unfreezeResponse = json_decode($result);
            Log::info("Unfreezing transaction Response: ".$result);

           // $auth_id = substr($reverse_transaction->payeeRef,4);

            if ($unfreezeResponse->responseCode == 200) {
                //execute reversal
                $serviceType = 'REVERSAL';
              
                $auth_id = substr($reverse_transaction->payeeRef,4);
                $post =	[
                    "serviceType" => $serviceType,
                    "authId"	  => $auth_id,

                ];

                Log::info("Reversal transaction Request: ".json_encode($post));
                $curl = curl_init();
                $url = "http://172.29.1.133:8984/esb/request/process/mb";
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post));
                curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                $result = curl_exec($curl);

                curl_close($curl);
                $reversalResponse = json_decode($result);
                Log::info("Reversal transaction Response: ".$result);

                if ($reversalResponse->responseCode == 200) {
                    Log::info("PayeeRef" . $reverse_transaction->payeeRef);
                     $post =    [
                        "fspDestination"=>$request->payer_fspId,
                        "fsSource"=>$request->payee_fspId,
                        "newReversalState"=>'REVERSED',
                        "reversalReason"=>$request->reversalReason ?? 'Reversal reversed by payee',
                        "payerRef"=>$request->payerRef,
                        "mkcbRefIdVariable" => $reverse_transaction->payeeRef . "99"
                    ];

                    $reverse_transaction->reversalState = 'REVERSED';
                    $reverse_transaction->save();
                   

    

                    Log::info("Commit Reversal Request: ".json_encode($post));
                    $curl = curl_init();
                    $url = "http://172.29.1.133:8984/esb/api/commitingReversalRequest";
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post));
                    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                    $result = curl_exec($curl);

                    curl_close($curl);
                    $transferAmtReversalResponse = json_decode($result);
                    Log::info("Commiting Reversal Response: ".$result);

                    if ($transferAmtReversalResponse->responseCode == 200) {
                        return back()->with('success', 'Reversal commited successfully, TIPS updated');
                    }
                } else {
                    return back()->with('error', 'Reverse funds failed');
                }
            } else {
                return back()->with('error', 'Failed to unfreeze funds: '.$unfreezeResponse->responseMessage);
            }
        } else {
            return back()->with('error', 'Failed to commit, Something went wrong');
        }
    }
    function getTransactionID($channel = 'TP'){
        $channel = strtoupper($channel);
        $mt = microtime();
        $mt = explode(" ",$mt);
        $t = ".".$mt[1];
        $m = explode(".", $mt[0]);
        return $channel.$t;

    }


    //esb mockup
    public function esbMockup(Request $request)
    {
        return json_encode($request->all());
    }
	
	public function view_tips_transactions($id)
    {
        $transaction = Transaction::where('id',$id)->get()[0];

        return view("tips.transactions.view",compact('transaction'));
    }
}
