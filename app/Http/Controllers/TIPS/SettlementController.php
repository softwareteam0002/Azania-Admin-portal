<?php

namespace App\Http\Controllers\TIPS;

use App\Http\Controllers\Controller;
use App\Models\Tips\Settlement;
use App\Traits\Responsemessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as Input;

class SettlementController extends Controller
{
    use Responsemessage;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Input::get('search'))
        {
            $value =  Input::get('search');
            $settlements = Settlement::search($value)->orderBy('id', 'DESC')->paginate(7);
        }
        else
        {
            $settlements = Settlement::orderBy('id', 'DESC')->paginate(7);
        }

        return view('tips.settlements.index', compact('settlements'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     public function store(Request $request)
    {
      try{
        $transaction_id = mt_rand(1234567890,9999999999);
        $duplicate_settlement =  Settlement::where("settlementWindow_id", $request->settlementWindow['id'])->where('settlementWindow_date', $request->settlementWindow['closingDate'])->first();

        if($duplicate_settlement)
        {
            $this->responseCode=4006;
            $this->responseMessage="Settlement request with duplicate settlementWindow ID";
        }
        else
        {
            $settlement = new Settlement();
            $settlement->settlementWindow_id = $request->settlementWindow['id'];
            $settlement->settlementWindow_date = $request->settlementWindow['closingDate'];
            $settlement->settlementWindow_description = $request->settlementWindow['description'];
            $settlement->outgoingTransactions_currency = $request->outgoingTransactions[0]['currency'];
            $settlement->outgoingTransactions_volume = intval($request->outgoingTransactions[0]['volume']);
            $settlement->outgoingTransactions_value = intval($request->outgoingTransactions[0]['value']);
            $settlement->incomingTransactions_currency = $request->incomingTransactions[0]['currency'];
            $settlement->incomingTransactions_volume = intval($request->incomingTransactions[0]['volume']);
            $settlement->incomingTransactions_value = intval($request->incomingTransactions[0]['value']);
            $settlement->position_type = $request->accountsPosition[0]['accountType'];
            $settlement->position_currency = $request->accountsPosition[0]['currency'];
            $settlement->position_amount = $request->accountsPosition[0]['amount'];
            $settlement->position_ledger_name = $request->accountsPosition[0]['ledgerType'][0]['name'];
            $settlement->position_ledger_amount = $request->accountsPosition[0]['ledgerType'][0]['amount'];
            $settlement->type_fee = $request->accountsPosition[1]['accountType'];
            $settlement->fee_currency = $request->accountsPosition[1]['currency'];
            $settlement->fee_amount = $request->accountsPosition[1]['amount'];
            $settlement->fee_ledger_name_interchange = $request->accountsPosition[1]['ledgerType'][1]['name'];
            $settlement->fee_ledger_amount_interchange = intval($request->accountsPosition[1]['ledgerType'][1]['amount']);
            $settlement->fee_ledger_name_processing = $request->accountsPosition[1]['ledgerType'][0]['name'];
            $settlement->fee_ledger_amount_processing = intval($request->accountsPosition[1]['ledgerType'][0]['amount']);
            $saved =   $settlement->save();
            if($saved)
            {
                $this->responseCode=200;
                $this->responseMessage="Success";
            }
            else
            {
                $this->responseCode=201;
                $this->responseMessage="Failed to save settlement";
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
        return $this->responseMessage($this->responseCode,$this->responseMessage);
    }
    catch(\Exception $e)
    {
        $this->responseCode=400;
        $customMessage="Bad transaction request";
        return $this->responseMessage($this->responseCode,$e->getMessage(),$customMessage);


    }
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
