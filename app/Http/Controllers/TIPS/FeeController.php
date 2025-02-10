<?php

namespace App\Http\Controllers\TIPS;

use App\Http\Controllers\Controller;
use App\Models\Tips\Fee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DB;

class FeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fees = Fee::where('status', 0)->orderBy('id', 'DESC')->get();
        return view('tips.fees.index', compact('fees'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tips.fees.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $check_Fee = Fee::where('transfer_type', $request->transfer_type)->where('fee_type', $request->fee_type)->where('minimum', $request->minimum)->where('maximum', $request->maximum)->first();

        if($check_Fee)
        {
            return back()->with('error', 'Fee Already Exists');
        }
        else
        {
            $fee = new Fee();
            $fee->transfer_type = $request->transfer_type;
            $fee->fee_type = $request->fee_type;
            $fee->minimum = $request->minimum;
            $fee->maximum = $request->maximum;
            $fee->fee = $request->fee;
            $fee->status = 0;
            $fee->save();
            return back()->with('success', 'Fee added successfully!');
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
    public function edit(Fee $fee)
    {
        return view('tips.fees.edit', compact('fee'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Fee $fee)
    {
        if($fee)
        {
            $fee->fee_type = $request->fee_type;
            $fee->minimum = $request->minimum;
            $fee->maximum = $request->maximum;
            $fee->fee = $request->fee;
            $fee->save();
            return back()->with('success', 'Fee updated successfully!');
        }
        else
        {
            return back()->with('error', 'Fee update failed!');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Fee $fee)
    {
        if($fee)
        {
            $fee->delete();
            return back()->with('success', 'Fee Deleted Successfully');
        }
        else
        {
            return back()->with('error', 'Fee Delete Failed');
        }

    }
    public function getFees(Request $request)
    {
        $fees = Fee::where('status', 0)->where('transfer_type', $request->transfer_type)->where('minimum', '<=', $request->amount)->where('maximum', '>=', $request->amount)->get(['fee_type','minimum','maximum', 'fee']);
        if(!empty($fees))
        {
            $responseCode = "200";
            $responseMessage = "Success";
        }
        else
        {
            $responseCode = "100";
            $responseMessage = "No data";
        }
        return json_encode(array('responseCode'=>$responseCode,'responseMessage'=>$responseMessage,'fees'=>$fees));
    }
	public function saveAgencyTransaction(Request $request)
	{
		Log::info("Request: ".json_encode($request->all()));
		$results = DB::connection('sqlsrv4')->select('CALL sp_agency_save_transactions(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', [$request->trans_agent_terminal_id,$request->operator_id,$request->trxn_name,$request->trxn_type,$request->amount,$request->charges,$request->trans_datetime,$request->date,$request->response_code,$request->responseMessage,$request->trxn_status,$request->customer_account,$request->trxn_start_time,$request->trxn_end_time,$request->transaction_reversed,$request->transactionID]);
		Log::info("Response: ".json_encode($results));
		if($results)
		{
			return response()->json(
                                        [
                                          'error' => false,
                                           'results' => $results
                                             ]
                                        );
		}
		else
		{
			return response()->json(
                                        [
                                          'error' => true,
                                           'results' => $results
                                             ]
                                        );
		}
	}
	public function updateAgencyTransaction(Request $request)
	{
		Log::info("Request: ".json_encode($request->all()));
		$results = DB::connection('sqlsrv4')->select('CALL sp_agency_save_transactions(?,?,?,?,?,?,?,?,?,?,?,?)', [$request->serialID,$request->stan,$request->rowIdCharge,$request->serialIDCharge,$request->batchID,$request->batchIDCharge,$request->rowId,$request->response_code,$request->responseMessage,$request->trxn_status,$request->trxn_end_time,$request->transactionID]);
		Log::info("Response: ".json_encode($results));
		if($results)
			{
				return response()->json(
											[
											  'error' => false,
											   'results' => $results
												 ]
											);
			}
			else
			{
				return response()->json(
											[
											  'error' => true,
											   'results' => $results
												 ]
											);
			}
	}
}
