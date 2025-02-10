<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait Responsemessage {

    public function responseMessage($responseCode, $responseMessage=null, $customMessage=null) {
        header('Content-Type: application/json');
        try {
            if(empty($responseMessage))
            {
                $responseMessage = $customMessage;
            }
            $response = array("responseCode"=>$responseCode, "responseMessage"=>$responseMessage);
            return json_encode($response);
        } catch (Exception $e) {
            $response = array("responseCode"=>400, "responseMessage"=>"Bad request");
            return json_encode($response);
        }
    }
}
