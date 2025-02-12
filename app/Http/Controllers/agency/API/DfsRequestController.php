<?php

namespace App\Http\Controllers\agency\API;

use App\Http\Controllers\Controller;
use App\Jobs\BankRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Validator;

class DfsRequestController extends Controller
{

    public function dfsRequest(Request $request)
    {
        $time = 5;
        $validator = Validator::make($request->all(), [
            'details' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => $validator->errors(),
            ]);
        }

        try {
            $jsonData = $request->getContent();
            $data = json_decode($jsonData, true);

            $recipient = $data['details']['recipient'];
            $body = $data['details']['body'];

            Queue::later($time, new BankRequests($body, $recipient));

            return response()->json([
                'code' => 200,
                'message' => 'Request is sent successfully',
            ]);

        } catch (\Exception $e) {
            Log::info('AGENCY-DFS-REQUEST: ', ['message' => $e]);
            return response()->json([
                'code' => 500,
                'message' => 'Failed to send request',
            ]);
        }
    }
}
