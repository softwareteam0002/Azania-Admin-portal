<?php

namespace App\Http\Controllers\TIPS;

use App\Http\Controllers\Controller;
use App\Models\Tips\Fraud;
use App\Models\Tips\FSP;
use App\Traits\Responsemessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request as Input;

class FraudController extends Controller
{
    use Responsemessage;

    private $responseCode;
    private $responseMessage;
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
            $frauds = Fraud::search($value)->orderBy('id', 'DESC')->get();
        }
        else
        {
            $frauds = Fraud::orderBy('id', 'DESC')->get();
        }

        return view('tips.frauds.index', compact('frauds'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $fsps = FSP::where('status', 0)->orderBy('name', 'ASC')->pluck('name','fspId');
        return view('tips.frauds.create', compact('fsps'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info("Fraud Request :".json_encode($request->all()));
        $fraud = new Fraud();
        $fraud->fspId = $request->fspId;
        $fraud->identifierType = $request->identifierType;
        $fraud->identifier = $request->identifier;
        $fraud->fullName = $request->fullName;
        $fraud->identityType = $request->identityType;
        $fraud->identityValue = $request->identityValue;
        $fraud->reasons = $request->reasons;
        $fraud->status = $request->status;
        $saved = $fraud->save();

        if($saved)
        {
            $this->responseCode="200";
            $this->responseMessage="Fraud reported successfully";

            $curl = curl_init();
            $url = "https://172.29.1.93:8003/api/tips/transfers/esbMockup";
            $post = [
                'responseCode' => $this->responseCode,
                'fspId' => $request->fspId,
                'identifier' => $request->identifier,
                'identifierType' => $request->identifierType,
                'identityType' => $request->identityType,
                'identityValue' => $request->identityValue,
                'reasons' => $request->reasons,
                'fullName' => $request->fullName,
                'fraudRegisterId' => 3,
                'participantId' => 3,
                'createdDate' => date('Y-m-d H:i:s')
            ];
            $json_string = json_encode($post);

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );

            $result = curl_exec($curl);

            curl_close($curl);
            $response = json_decode($result);;
            Log::info("Fraud Response :".$result);
            if($response->responseCode == 200)
            {
                $fraud->fraudRegisterId = $response->fraudRegisterId;
                $fraud->participantId = $response->participantId;
                $fraud->createdDate = $response->createdDate;
                $fraud->save();
                return back()->with('success',$this->responseMessage);
            }
            else
            {
                $fraud->status = 'FAILED';
                $fraud->errorCode = $response->errorCode;
                $fraud->errorDescription = $response->errorDescription;
                $fraud->save();
                return back()->with('error',$this->errorDescription);
            }

        }
        else
        {
            $this->responseCode="201";
            $this->responseMessage="Failed to save fraud";
            $post = [
                "errorCode" => $this->responseCode,
                "errorDescription" => $this->responseMessage
            ];

            $post =  array("errorInformation"=>$post);
            return back()->with('error',$this->responseMessage);
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
    public function edit(Fraud $fraud)
    {
        $fsps = FSP::where('status', 0)->orderBy('name', 'ASC')->pluck('name','fspId');
        return view('tips.frauds.edit', compact('fraud','fsps'));
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

        Log::info("Fraud Update Request :".json_encode(array('user'=>Auth::user()->id.' '.Auth::user()->name.' '.Auth::user()->email,$request->all())));
        $fraud = Fraud::where('id',$id)->where('fspId', $request->fspId)->first();

        if($fraud)
        {
            $this->responseCode="200";
            $curl = curl_init();
            $url = "https://172.29.1.93:8003/api/tips/transfers/esbMockup";
            $post = [
                'fspId' => $request->fspId,
                'status' => $request->status,
                'reasons' => $request->reasons,
                'fraudRegisterId' => $fraud->fraudRegisterId,
                'participantId' => $fraud->participantId,
                'responseCode' => $this->responseCode
            ];
            $json_string = json_encode($post);

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );

            $result = curl_exec($curl);

            curl_close($curl);
            $response = json_decode($result);
            Log::info("Fraud Update Response :".$result);
            if($response->responseCode == 200)
            {
                $this->responseMessage = 'Fraud updated successfully';
                $fraud->identifierType = $request->identifierType;
                $fraud->identityType = $request->identityType;
                $fraud->identityValue = $request->identityValue;
                $fraud->status = $response->status;
                $fraud->reasons = $response->reasons;
                $fraud->save();
                return back()->with('success',$this->responseMessage);
            }
            else
            {
                $fraud->errorCode = $response->errorCode;
                $fraud->errorDescription = $response->errorDescription;
                $fraud->save();
                return back()->with('error',$this->errorDescription);
            }
            return $result;
            //return json_encode($post);
        }
        else
        {
            $this->responseCode="201";
            $this->responseMessage="Fraud not found";
            $post = [
                "errorCode" => $this->responseCode,
                "errorDescription" => $this->responseMessage
            ];
            $errorInformation =  array("errorInformation"=>$post);
            //return json_encode($errorInformation);
            return back()->with('error',$this->responseMessage);
        }
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

    //fraud register/report request
    public function postFraud(Request $request)
    {
        Log::info("Fraud Request :".json_encode($request->all()));
        $fraud = new Fraud();
        $fraud->fspId = $request->fspId;
        $fraud->identifierType = $request->identifierType;
        $fraud->identifier = $request->identifier;
        $fraud->fullName = $request->fullName;
        $fraud->identityType = $request->identityType;
        $fraud->identityValue = $request->identityValue;
        $fraud->reasons = $request->reasons;
        $fraud->status = $request->status;
        $saved = $fraud->save();

        if($saved)
        {
            $this->responseCode="200";
            $this->responseMessage="Success";

            $curl = curl_init();
            $url = "https://172.29.1.93:8003/api/tips/transfers/esbMockup";
            $post = [
                'responseCode' => $this->responseCode,
                'fspId' => $request->fspId,
                'identifier' => $request->identifier,
                'identifierType' => $request->identifierType,
                'identityType' => $request->identityType,
                'identityValue' => $request->identityValue,
                'reasons' => $request->reasons,
                'fullName' => $request->fullName,
                'fraudRegisterId' => 3,
                'participantId' => 3,
                'createdDate' => date('Y-m-d H:i:s')
            ];
            $json_string = json_encode($post);

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );

            $result = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($result);;
            Log::info("Fraud Response :".$result);
            if($response->responseCode == 200)
            {
                $fraud->fraudRegisterId = $response->fraudRegisterId;
                $fraud->participantId = $response->participantId;
                $fraud->createdDate = $response->createdDate;
                $fraud->save();
            }
            else
            {
                $fraud->errorCode = $response->errorCode;
                $fraud->errorDescription = $response->errorDescription;
                $fraud->save();
            }
            return $result;
            //return json_encode($post);
        }
        else
        {
            $this->responseCode="201";
            $this->responseMessage="Failed to save fraud";
            $post = [
                "errorCode" => $this->responseCode,
                "errorDescription" => $this->responseMessage
            ];
            $errorInformation =  array("errorInformation"=>$post);
            return json_encode($errorInformation);
        }


    }

    //fraud callback
    public function updateFraud(Request $request)
    {
        Log::info("Fraud Update Request :".json_encode($request->all()));
        $fraud = Fraud::where('fraudRegisterId',$request->fraudRegisterId)->where('fspId', $request->fspId)->first();

        if($fraud)
        {
            $this->responseCode="200";
            $curl = curl_init();
            $url = "https://172.29.1.93:8003/api/tips/transfers/esbMockup";
            $post = [
                'fspId' => $request->fspId,
                'status' => $request->status,
                'reasons' => $request->reasons,
                'fraudRegisterId' => $request->fraudRegisterId,
                'responseCode' => $this->responseCode
            ];
            $json_string = json_encode($post);

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );

            $result = curl_exec($curl);

            curl_close($curl);
            $response = json_decode($result);
            Log::info("Fraud Update Response :".$result);
            if($response->responseCode == 200)
            {
                $fraud->status = $response->status;
                $fraud->reasons = $response->reasons;
                $fraud->save();
            }
            else
            {
                $fraud->errorCode = $response->errorCode;
                $fraud->errorDescription = $response->errorDescription;
                $fraud->save();
            }
            return $result;
            //return json_encode($post);
        }
        else
        {
            $this->responseCode="201";
            $this->responseMessage="Fraud not found";
            $post = [
                "errorCode" => $this->responseCode,
                "errorDescription" => $this->responseMessage
            ];
            $errorInformation =  array("errorInformation"=>$post);
            return json_encode($errorInformation);
        }


    }
    //fraud list/get
    public function listFrauds(Request $request)
    {
        Log::info("Fraud List Request :".json_encode($request->all()));
        $curl = curl_init();
        $url = "https://172.29.1.93:8003/api/tips/transfers/esbMockup";
        $this->responseCode="200";
        $post = [
            'fspId' => $request->fspId,
            'frauds' => $request->frauds,
            'responseCode' => $this->responseCode
        ];
        $json_string = json_encode($post);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );

        $result = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($result);
        Log::info("Fraud List Response :".$result);
        if($response->responseCode == 200)
        {

        }
        else
        {

        }
        return $result;

    }
}
