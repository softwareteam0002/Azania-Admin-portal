<?php

namespace App\Http\Controllers\TIPS;

use App\Http\Controllers\Controller;
use App\Models\Tips\FSP;
use App\Models\Tips\Message;
use App\Traits\Responsemessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request as Input;

class MessageController extends Controller
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
            $messages = Message::search($value)->orderBy('id', 'DESC')->get();
        }
        else
        {
            $messages = Message::orderBy('id', 'DESC')->get();
        }

        return view('tips.messages.index', compact('messages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $fsps = FSP::where('status', 0)->orderBy('name', 'ASC')->pluck('name','fspId');
        return view('tips.messages.create', compact('fsps'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        Log::info("Message Request :".json_encode($request->all()));
        $message = new Message();
        $sender_reference = mt_rand(1234567,9999999);
        $message->sender_reference = Auth::user()->fspId.'_'.$sender_reference;
        $message->sender_fspId = Auth::user()->fspId;
        $message->sender_user = Auth::user()->name;
        $message->recipients_fspId = $request->recipients_fspId;
        $message->recipients_user = $request->recipients_user;
        $message->subject = $request->subject;
        $message->body = $request->body;
        $message->notificationType = $request->notificationType;
        $message->status = 0;
        $message->flag = 'OUTGOING';
        $message->date = date('Y-m-d H:i:s');
        $saved = $message->save();

        if($saved)
        {
            $this->responseCode="200";
            $this->responseMessage="Message sent successfully";
            $alert="success";

            $curl = curl_init();
            $url = "https://172.29.1.93:8003/api/tips/transfers/esbMockup";
            $post = [
                'responseCode' => $this->responseCode,
                'responseMessage' => $this->responseMessage,
                'sender_reference' => $sender_reference,
                'sender_fspId' => Auth::user()->sender_fspId,
                'sender_user' => Auth::user()->name,
                'recipients_fspId' => $request->recipients_fspId,
                'recipients_user' => $request->recipients_user,
                'subject' => $request->subject,
                'body' => $request->body,
                'notificationType' => $request->notificationType,
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
            Log::info("Message Response :".$result);
            if($response->responseCode == 200)
            {
                $message->responseCode = $response->responseCode;
                $message->status = 1;
                $message->save();
            }
            else
            {
                $this->responseCode="100";
                $alert="error";
                $this->responseMessage="Message sending failed";
                $message->responseCode = $response->responseCode;
                $message->status = 2;
                $message->save();
            }
        }
        else
        {
            $this->responseCode="201";
            $alert="error";
            $this->responseMessage="Failed to save message";
            $post = [
                "errorCode" => $this->responseCode,
                "errorDescription" => $this->responseMessage
            ];
            $post =  array("errorInformation"=>$post);
        }
        return back()->with($alert,$this->responseMessage);
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

    public function postMessage(Request $request)
    {
        Log::info("Message Request :".json_encode($request->all()));
        $message = new Message();
        $message->sender_reference = $request->sender_reference;
        $message->sender_fspId = $request->sender_fspId;
        $message->sender_user = $request->sender_user;
        $message->recipients_fspId = $request->recipients_fspId;
        $message->recipients_user = $request->recipients_user;
        $message->subject = $request->subject;
        $message->body = $request->body;
        $message->notificationType = $request->notificationType;
        $message->status = 0;
        $message->flag = 'OUTGOING';
        $message->date = date('Y-m-d H:i:s');
        $saved = $message->save();

        if($saved)
        {
            $this->responseCode="200";
            $this->responseMessage="Success";


            $curl = curl_init();
            $url = "https://172.29.1.93:8003/api/tips/transfers/esbMockup";
            $post = [
                'responseCode' => $this->responseCode,
                'responseMessage' => $this->responseMessage,
                'sender_reference' => $request->sender_reference,
                'sender_fspId' => $request->sender_fspId,
                'sender_user' => $request->sender_user,
                'recipients_fspId' => $request->recipients_fspId,
                'recipients_user' => $request->recipients_user,
                'subject' => $request->subject,
                'body' => $request->body,
                'notificationType' => $request->notificationType,
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
            Log::info("Message Response :".$result);
            if($response->responseCode == 200)
            {
                $message->responseCode = $response->responseCode;
                $message->status = 1;
                $message->save();
            }
            else
            {
                $message->responseCode = $response->responseCode;
                $message->status = 2;
                $message->save();
            }
            return $result;
            //return json_encode($post);
        }
        else
        {
            $this->responseCode="201";
            $this->responseMessage="Failed to save message";
            $post = [
                "errorCode" => $this->responseCode,
                "errorDescription" => $this->responseMessage
            ];
            $errorInformation =  array("errorInformation"=>$post);
            return json_encode($errorInformation);
        }


    }

    public function receiveMessage(Request $request)
    {
        Log::info("Message Receiving Request :".json_encode($request->all()));
        $message = new Message();
        $message->sender_reference = $request->sender_reference;
        $message->sender_fspId = $request->sender_fspId;
        $message->sender_user = $request->sender_user;
        $message->recipients_fspId = $request->recipients_fspId;
        $message->recipients_user = $request->recipients_user;
        $message->subject = $request->subject;
        $message->body = $request->body;
        $message->notificationType = $request->notificationType;
        $message->status = 3;
        $message->flag = 'INCOMING';
        $message->date = date('Y-m-d H:i:s');
        $saved = $message->save();

        if($saved)
        {
            $this->responseCode="200";
            $this->responseMessage="Success";
        }
        else
        {
            $this->responseCode="201";
            $this->responseMessage="Failed to save message";
        }
        return $this->responseMessage($this->responseCode,$this->responseMessage);
    }

    public function upload(Request $request)
    {
        if($request->hasFile('upload')) {
            //get filename with extension
            $filenamewithextension = $request->file('upload')->getClientOriginalName();

            //get filename without extension
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);

            //get file extension
            $extension = $request->file('upload')->getClientOriginalExtension();

            //filename to store
            $filenametostore = $filename.'_'.time().'.'.$extension;

            //Upload File
            $request->file('upload')->storeAs('public/uploads', $filenametostore);

            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $url = asset('storage/app/public/uploads/'.$filenametostore);
            $msg = 'Image successfully uploaded';
            $re = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";

            // Render HTML output
            @header('Content-type: text/html; charset=utf-8');
            echo $re;
        }
    }
}
