<?php

namespace App\Http\Controllers\IB;
use App\IBExchangeRate;
use App\Imports\ExchangeRatesImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExchangeRateController extends Controller
{
    //

    public function index( ) {
         $currency_rates = IBExchangeRate::latest()->paginate(10);
    	return view('ib.exchange_rate.index', compact('currency_rates'));
    }


    public function store (Request $request) {

      $validator = $this->validate($request, [
           'mean_rate'         => 'max:6',
           'buying_price'      => 'max:6',
           'selling_price'     => 'max:6'
      ]);

      $request->mean_rate              = $request->mean_rate;
      $request->selling_price          = $request->selling_price;
      $request->buying_price           = $request->buying_price;
    	$exchangeRate = new IBExchangeRate(); 
    	$exchangeRate->foreign_currency   = $request->foreign_currency;
      $exchangeRate->currency_code      = $request->currency_code;
    	$exchangeRate->mean_rate          = $request->mean_rate;
    	$exchangeRate->buying_price       = $request->buying_price;
    	$exchangeRate->selling_price      = $request->selling_price;
        $exchangeRate->date             = date('Y-m-d');
    	$exchangeRate->save();	
    	return redirect()->back();
    }

    public function edit($id) {

           $exchangeRate = IBExchangeRate::findOrFail($id);
          return view('ib.exchange_rate.edit', compact('exchangeRate'));

    }

    public function update(Request $request, $id) {

        $validator = $this->validate($request, [
           'mean_rate'         => 'max:6',
           'buying_price'      => 'max:6',
           'selling_price'     => 'max:6'
        ]);
          $exchangeRate                    = IBExchangeRate::findOrFail($id);
          $request->mean_rate              = $request->mean_rate;
          $request->selling_price          = $request->selling_price;
          $request->buying_price           = $request->buying_price;
          $exchangeRate->foreign_currency  = $request->foreign_currency;
          $exchangeRate->currency_code     = $request->currency_code;
          $exchangeRate->mean_rate         = $request->mean_rate;
          $exchangeRate->buying_price      = $request->buying_price;
          $exchangeRate->selling_price     = $request->selling_price;
          $update = $exchangeRate->save();
          if ($update) {
              
               session()->flash('exchange_rate_message', 'Exchange rate updated successfully');      
          }
          return redirect()->back();
          
    }

    public function import () {
    	Excel::import(new ExchangeRatesImport, request()->file('file'));
    	return redirect()->back();
    }
}
