<?php

namespace App\Http\Controllers\IB;

use App\Http\Controllers\Controller;
use App\IBTransaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // Get the transaction ID from the request
        $transactionId = $request->input('transaction_id');

        // Get start and end dates from the request, defaulting to null if not provided
        $startDate = $request->input('start_date') ? $request->input('start_date') . " 00:00:00" : null;
        $endDate = $request->input('end_date') ? $request->input('end_date') . " 23:59:59" : null;

        $query = IBTransaction::query();
        // Check if transaction ID is present
        if ($transactionId) {
            // Filter by the transaction ID
            $trxns = $query->where('transactionId', trim($transactionId))->orderByDesc('id')->get();
        } elseif ($startDate && $endDate) {
            // Filter by the date range
            $trxns = $query->whereBetween('created_at', [$startDate, $endDate])->orderByDesc('id')
                ->limit(300)->get();
        } else {
            // If no filters are applied, return the latest 20 transactions
            $trxns = $query->orderByDesc('id')->limit(20)->get();
        }

        return view("ib.transaction.index", compact('trxns'));
    }

    public function editTrans($id)
    {
        $transaction = IBTransaction::where('id', $id)->get()[0];

        return view("ib.transaction.view", compact('transaction'));
    }


}
