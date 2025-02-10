<?php

namespace App\Http\Controllers;

//use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SMSController extends Controller{

    private $AuthKey = "SMSAPIKey";//used to authenticate users
    private $selcom_username = "MKOMBOZIBANK";
    private $selcom_password = "mkcb123";

    public function send($phone, $message){
        $channel = "ESB";
        $message = urlencode($message);
        //send the request to SMS Gateway
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://paypoint.selcommobile.com/bulksms/dispatch.php?msisdn=$phone&user=$this->selcom_username&password=$this->selcom_password&message=$message",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);
        curl_close($curl);
	
	return $response;
       
    }

    public function sendSMS(){
        $ret = array();
        /*
        $channel = $r->channel;
        $phone = $r->phone; 
        $message = $r->message;
        //validate the request
        $key = 'SMSAPIKey';
        if($this->AuthKey != $key){
            $ret['code'] = 400;
            $ret['msg'] = "Invalid SMS Key!";
        }else{
            $ret['code'] = 200;
            $ret['msg'] = "SMS Key Valid!";
            //validate the payload
            if(!isset($r->channel) || !isset($r->phone) || !isset($r->message)){
                $ret['code'] = 400;
                $ret['msg'] = "Invalid request format!";
            }else{
                //create a request
                $channel = $r->channel;
                //validate to see if the channel exist

                $phone = $r->phone; 
                //make sure you parse the phone number
                $message = $r->message;
                $message = urlencode($message);

                //send the request to SMS Gateway
                $curl = curl_init();
                curl_setopt_array($curl, array(
                CURLOPT_URL => "https://paypoint.selcommobile.com/bulksms/dispatch.php?msisdn=$phone&user=$this->selcom_username&password=$this->selcom_password&message=$message",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                ));

                $response = curl_exec($curl);
		
                //parse the result to see if the request passed
                $ret['code'] = 200;
                $ret['msg'] = "Request sent!";
                $ret['response'] = curl_error($curl);
                curl_close($curl);
                //echo $response;
		

            }
        }
        */

        return $ret;
    }
}