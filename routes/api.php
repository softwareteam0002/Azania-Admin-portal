<?php

use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\agency\InteroperabilityController;
use Illuminate\Support\Facades\Route;

Route::post('operator/login', 'Admin\HomeController@operatorLogin');
Route::middleware('auth:operator')->group(function () {
    Route::post('agent/pin/change', [HomeController::class, 'agentPinChange']);
    Route::post('operator/pin/change', [HomeController::class, 'operatorPinChange']);
    Route::get('agent/pin', [HomeController::class, 'agentPin']);
    Route::get('operator/pin', [HomeController::class, 'operatorPin']);
});
Route::post('splash', 'agency\API\SplashController@splash');
Route::post('encrypt', 'Admin\HomeController@encryptAESTest');
Route::post('decrypt', 'Admin\HomeController@decryptAESTest');
Route::post('decode', 'Admin\HomeController@decodeToken');

Route::get('agent/enc', 'Admin\HomeController@agentEnc');
Route::post('agent/dfs/request', 'agency\API\DfsRequestController@dfsRequest');


Route::group(['prefix' => 'tips', 'namespace' => 'TIPS'], function () {

    Route::post('fee', 'FeeController@getFees')->name('getFees');
    Route::post('agency/save', 'FeeController@saveAgencyTransaction')->name('saveAgencyTransaction');
    Route::post('agency/update', 'FeeController@updateAgencyTransaction')->name('updateAgencyTransaction');
    Route::post('messages/receive', 'MessageController@receiveMessage')->name('receiveMessage');
    Route::post('messages', 'MessageController@postMessage')->name('postMessage');
    Route::post('frauds/list', 'FraudController@listFrauds')->name('listFrauds');
    Route::post('frauds/update', 'FraudController@updateFraud')->name('updateFraud');
    Route::post('frauds', 'FraudController@postFraud')->name('postFraud');
    Route::post('settlements', 'SettlementController@store')->name('settlements');
    Route::post('transfers/sendReversal', 'TransactionController@receiveReversal')->name('sendReversal');
    Route::post('transfers/reversal/inquiry', 'TransactionController@reversalInquiry')->name('reversalInquiry');
    Route::post('transfers/reversal', 'TransactionController@reversal')->name('reversal');
    Route::post('transfers/esbMockup', 'TransactionController@esbMockup')->name('esbMockup');
    Route::post('transfers/inquiry', 'TransactionController@inquiry')->name('inquiry');
    Route::post('transfers/inquiry/incoming', 'TransactionController@inquiryIncoming')->name('inquiryIncoming');
    Route::post('transfers/notification', 'TransactionController@notification')->name('notification');
    Route::post('transaction/confirmation', 'TransactionController@tipsTransactionConfirmation')->name('transactionConfirmation');
    Route::put('transfers/{id}', 'TransactionController@update')->name('update');
    Route::put('transfers/{id}/confirm', 'TransactionController@tipsConfirmation')->name('tipsConfirmation');
    Route::post('transfers', 'TransactionController@store')->name('transfers');
    Route::post('fsps', 'FSPsController@getFSP')->name('fsps');

});

Route::get('agency/districts/{id}', 'agency\API\RegionDistrictController@getDistricts');
Route::post('agency/transactions', 'agency\API\TransactionController@processTransactions')->middleware('auth:operator');

//test encrypt
Route::post('encrypt', 'Admin\HomeController@encryptTest');


//agency interoperability
Route::prefix('agency/interoperability')->group(function () {
    Route::post('get_entries', [InteroperabilityController::class, 'getBatchEntries']);

    Route::get('transactions', [InteroperabilityController::class, 'getTransactions']);

    Route::middleware('check.authorized')->group(function () {
        Route::post('incomingToUmojaAgents', [InteroperabilityController::class, 'incomingTransaction']);
        Route::post('outgoingToAcbAgents', [InteroperabilityController::class, 'outgoingTransaction']);
        Route::post('balanceInquiry', [InteroperabilityController::class, 'balanceInquiry']);
        Route::post('reverseInquiry', [InteroperabilityController::class, 'reversalInquiry']);
        Route::post('updateTransaction', [InteroperabilityController::class, 'updateTransaction']);
    });
});
