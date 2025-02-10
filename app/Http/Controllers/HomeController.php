<?php

namespace App\Http\Controllers;

use App\Devices;
use App\TblTransaction;
use App\User;
use Illuminate\Support\Facades\DB;

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
        $devices = Devices::count();
        $agents = DB::connection('sqlsrv4')->table('tbl_agency_banking_agents')->count();
        $users = User::count();

        return response()->json([
            'users' => $users,
            'agents' => $agents,
            'devices' => $devices,
        ]);
    }

}
