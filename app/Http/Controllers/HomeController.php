<?php

namespace App\Http\Controllers;

use App\Devices;
use App\TblTransaction;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function getTransactions(): \Illuminate\Http\JsonResponse
    {
        $transactions = TblTransaction::all();
        return response()->json($transactions);
    }

    public function getUserCount(): \Illuminate\Http\JsonResponse
    {
        try {
            $devices = Devices::count();
            $agents = DB::connection('sqlsrv4')->table('tbl_agency_banking_agents')->count();
            $users = User::count();

            return response()->json([
                'users' => $users,
                'agents' => $agents,
                'devices' => $devices,
            ]);
        } catch (\Exception $e) {
            Log::error("Error fetching user count: ", ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            return response()->json([
                'error' => 'An error occurred while fetching user count.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
