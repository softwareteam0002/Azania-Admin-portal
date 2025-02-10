<?php

namespace App\Http\Controllers\agency;

use App\AbInteroperabilityCharge;
use App\AbInteroperabilityChargeBatch;
use App\AbInteroperabilityTransaction;
use App\Exports\AbInteroperabilityExport;
use App\Http\Controllers\Controller;
use App\Imports\InteroperabilityChargeImport;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\HTTPSecurity;

class InteroperabilityController extends Controller
{
    public const WAITING_APPROVAL = 1;
    public const INACTIVE = 0;
    public const ACTIVE = 1;
    public const WITHDRAW = 1;
    public const APPROVE = 1;
    public const REJECT = 2;
    public const DEPOSIT = 2;
    public const LEVY = 3;
    public const BALANCE_INQUIRY = 4;
    public const AGENCY_WITHDRAW = 'WI';
    public const AGENCY_DEPOSIT = 'DE';
    public const AGENCY_BALANCE_INQUIRY = 'BI';
    public const MINIMUM_AMOUNT = 1000;
    public const PRINCIPAL_ACCOUNT = 100;
    public const FEE_COMMISSION_ACCOUNT = 200;
    public const FEE_ACQUIRER_ACCOUNT = 300;
    public const FEE_PROCESSOR_ACCOUNT = 400;
    public const VAT_ACCOUNT = 500;
    public const LEVY_ACCOUNT = 600;
    public const EXPENSE_ACCOUNT = 700;
    public const ALL = 1;
    public const SUCCESS = 2;
    public const FAILED = 3;
    public const DEPOSIT_SERVICE_ACCOUNT = 200;
    public const WITHDRAW_SERVICE_ACCOUNT = 100;


    public function index()
    {
        $agencyWithdrawCharges = AbInteroperabilityCharge::where('charge_type', self::WITHDRAW)->get();
        $agencyDepositCharges = AbInteroperabilityCharge::where('charge_type', self::DEPOSIT)->get();
        $agencyChargeBatches = AbInteroperabilityChargeBatch::where('deleted_at', null)->orderBy('id', 'desc')->get();
        $agencyLevyRates = AbInteroperabilityCharge::where('charge_type', self::LEVY)->get();
        $serviceAccounts = DB::connection('sqlsrv4')->table('tbl_agency_banking_interoperability_service_accounts')->get();

        return view('agency.interoperability.charges', compact('agencyDepositCharges', 'agencyWithdrawCharges', 'agencyChargeBatches', 'agencyLevyRates', 'serviceAccounts'));
    }

    public function downloadTemplate()
    {
        $file_name = 'agency_interoperability_charges.xlsx';
        $file_path = 'template/' . $file_name;

        if (Storage::exists($file_path)) {
            return response()->download(Storage::path($file_path), $file_name);
        } else {
            return redirect()->back()->with(['notification' => "Template doesn't exist", 'color' => "danger"]);
        }
    }

    public function uploadCharges(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'charge_type' => 'required',
            'file' => 'required|mimes:xls,xlsx',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['notification' => $validator->errors(), 'color' => "danger"]);
        }


        try {
            DB::beginTransaction();
            $charge_type = $request->charge_type;
            $batch_number = $this->generateBatchNumber($charge_type);


            DB::connection('sqlsrv4')->table('tbl_agency_banking_interoperability_charge_batches')->insert([
                'charge_type' => $request->charge_type,
                'batch_number' => $batch_number,
                'is_waitingApproval' => self::WAITING_APPROVAL,
                'created_by' => Auth::user()->id,
                'uuid' => Str::uuid(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Excel::import(new InteroperabilityChargeImport($batch_number, $charge_type), request()->file('file'));

            DB::commit();
            return redirect()->back()->with(['notification' => "Batch imported successfully, waiting approval", 'color' => "success"]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
            DB::rollBack();
            return redirect()->back()->with(['notification' => "The value of " . $failure->attribute() . " on row " . $failure->row() . " is not valid", 'color' => "danger"]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()->with(['notification' => "Please fill all necessary fields in the excel file", 'color' => "danger"]);
        }

    }

    public function generateBatchNumber($chargeType)
    {
        $last_batch_number = DB::connection('sqlsrv4')->table('tbl_agency_banking_interoperability_charge_batches')
            ->latest()->pluck('batch_number')->first();


        $prefix = 'CHARGES-';


        if (!isset($last_batch_number)) {

            $batch_number = $prefix . "0000001";
            return $batch_number;
        }

        if (isset($last_batch_number)) {
            $number = intval(substr($last_batch_number, 8));

            $number++;
            $number = sprintf("%07d", $number);
            $batch_number = $prefix . strval($number);
            return $batch_number;
        }
    }

    public function serviceAccountIndex()
    {
        $serviceAccounts = DB::connection('sqlsrv4')->table('tbl_agency_banking_interoperability_service_accounts')->get();
        return view('agency.interoperability.service_accounts', compact('serviceAccounts'));
    }

    public function transactionIndex()
    {
        return view('agency.interoperability.transactions');
    }

    public function getTransactions()
    {
        $interoperabilityTransactions = AbInteroperabilityTransaction::query()->orderBy('id', 'DESC')->get();
        $encryptedData = $this->encrypt(json_encode(['code' => 200, 'transactions' => $interoperabilityTransactions]), config("security.encryption_password"));
        return response()->json(['data' => $encryptedData]);
    }

    public function statusServiceAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_account_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['notification' => $validator->errors(), 'color' => "danger"]);
        }

        try {
            $serviceAccount = DB::connection('sqlsrv4')->table('tbl_agency_banking_interoperability_service_accounts')->
            where('id', $request->service_account_id)->first();

            if ($serviceAccount) {
                DB::connection('sqlsrv4')
                    ->table('tbl_agency_banking_interoperability_service_accounts')
                    ->where('id', $request->service_account_id)
                    ->update([
                        'is_active' => ($serviceAccount->is_active == self::ACTIVE) ? self::INACTIVE : self::ACTIVE
                    ]);
                return redirect()->back()->with(['notification' => ($serviceAccount->is_active == self::INACTIVE) ? "Account activated successfully" : "Account deactivated successfully", 'color' => "success"]);
            } else {
                return redirect()->back()->with(['notification' => 'Account not found', 'color' => "danger"]);
            }
        } catch (Exception $e) {
            return redirect()->back()->with(['notification' => ($serviceAccount->is_active == self::INACTIVE) ? "Failed to activate account" : "Failed to deactivate account", 'color' => "danger"]);
        }

    }

    public function updateServiceAccount(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'account_name' => 'required',
            'account_number' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['notification' => $validator->errors(), 'color' => "danger"]);
        }
        try {
            $serviceAccount = DB::connection('sqlsrv4')->table('tbl_agency_banking_interoperability_service_accounts')->
            where('id', $request->id)->first();

            if ($serviceAccount) {
                DB::connection('sqlsrv4')
                    ->table('tbl_agency_banking_interoperability_service_accounts')
                    ->where('id', $request->id)
                    ->update([
                        'account_name' => $request->account_name,
                        'account_number' => $request->account_number,
                        'is_active' => self::INACTIVE
                    ]);
                return redirect()->back()->with(['notification' => "Account updated successfully", 'color' => "success"]);
            } else {
                return redirect()->back()->with(['notification' => 'Account not found', 'color' => "danger"]);
            }
        } catch (Exception $e) {
            return redirect()->back()->with(['notification' => "Failed to update account", 'color' => "danger"]);
        }

    }

    public function approveAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'approval' => 'required',
            'action' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['notification' => $validator->errors(), 'color' => "danger"]);
        }

        try {
            $serviceAccount = AbInteroperabilityCharge::where('uuid', $request->approval)->first();

            if ($serviceAccount) {
                if ($request->action == self::APPROVE) {
                    $waitingApproval = 0;
                } else {
                    $waitingApproval = 2;
                }

                $serviceAccount->update([
                    'is_waitingApproval' => $waitingApproval,
                    'approved_by' => Auth::user()->id,
                ]);

                return redirect()->back()->with(['notification' => ($waitingApproval == self::REJECT) ? "Rejected successfully" : "Approved successfully", 'color' => "success"]);
            } else {
                return redirect()->back()->with(['notification' => 'Account not found', 'color' => "danger"]);
            }
        } catch (Exception $e) {
            return redirect()->back()->with(['notification' => "Failed to approve/reject", 'color' => "danger"]);
        }

    }

    public function getBatchEntries(Request $request)
    {
        $decryptedRequest = $this->decrypt($request->data, config("security.encryption_password"));
        $request = json_decode($decryptedRequest, true);
        $validator = Validator::make($request, [
            'batchNo' => 'required',
        ], [
            'batchNo.required' => 'Batch number is required'
        ]);


        if ($validator->fails()) {
            $encryptedData = $this->encrypt(json_encode(['code' => 400, 'message' => $validator->errors()->first()]), config("security.encryption_password"));
            return response()->json(['data' => $encryptedData]);
        }

        $data = [];
        $entries = AbInteroperabilityCharge::query()->select('from_amount', 'to_amount', 'charge', 'is_active')->where('batch_number',
            $request->batchNo)->get();

        foreach ($entries as $entry) {
            $entries = [
                'from_amount' => $entry->from_amount,
                'to_amount' => $entry->to_amount,
                'charge' => $entry->charge,
                'status' => ($entry->is_active == self::ACTIVE) ? 'Active' : 'Inactive',
            ];
            $data[] = $entries;
        }

        if ($entries) {
            $encryptedData = $this->encrypt(json_encode(['code' => 200, 'charges' => $data]), config("security.encryption_password"));
            return response()->json(['data' => $encryptedData]);
        }
        $encryptedData = $this->encrypt(json_encode(['code' => 100, 'message' => 'No results found']), config("security.encryption_password"));
        return response()->json(['data' => $encryptedData]);
    }

    public function approveChargeBatch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'approval' => 'required',
            'action' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['notification' => $validator->errors(), 'color' => "danger"]);
        }

        DB::beginTransaction();
        try {
            $chargeBatch = AbInteroperabilityChargeBatch::where('uuid', $request->approval)->first();
            $chargeEntries = AbInteroperabilityCharge::where('batch_number', $chargeBatch->batch_number)->get();

            if ($chargeBatch) {
                if ($request->action == self::APPROVE) {
                    $waitingApproval = 0;
                } elseif ($request->action == self::REJECT) {
                    $waitingApproval = 2;
                } else {
                    $status = ($chargeBatch->is_active == self::ACTIVE) ? self::INACTIVE : self::ACTIVE;
                    $chargeBatch->update([
                        'is_active' => $status,
                    ]);
                    AbInteroperabilityCharge::where('batch_number', $chargeBatch->batch_number)->update([
                        'is_active' => $status,
                    ]);

                    DB::commit();
                    return redirect()->back()->with(['notification' => ($chargeBatch->is_active == self::ACTIVE) ? "Activated successfully"
                        : "Deactivated successfully", 'color' => "success"]);
                }

                AbInteroperabilityCharge::where('batch_number', $chargeBatch->batch_number)->update([
                    'is_waitingApproval' => $waitingApproval,
                    'approved_by' => Auth::user()->id,

                ]);

                $chargeBatch->update([
                    'is_waitingApproval' => $waitingApproval,
                    'approved_by' => Auth::user()->id,
                ]);
                DB::commit();
                return redirect()->back()->with(['notification' => ($waitingApproval == self::REJECT) ? "Rejected successfully" : "Approved successfully", 'color' => "success"]);
            } else {
                DB::rollBack();
                return redirect()->back()->with(['notification' => 'Batch not found', 'color' => "danger"]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::info(json_encode($e));
            return redirect()->back()->with(['notification' => "Exception occured,try again later!", 'color' => "danger"]);
        }

    }

	public function incomingTransaction(Request $request)
    {
		$decryptedRequest = $this->decryptRequest($request->data, env('ENCRYPTION_PASSWORD'));
        //$decryptedRequest = $this->decryptRequest($request->data, env('ENCRYPTION_PASSWORD')); dd($decryptedRequest);
        $request = json_decode($decryptedRequest, true);
        Log::channel('interoperability')->info('Incoming-Transaction-Request:' . json_encode($request));
        $validator = Validator::make($request, [
            'transactionType' => 'required|in:' . self::AGENCY_WITHDRAW . ',' . self::AGENCY_DEPOSIT,
            'amount' => 'required|numeric|min:' . self::MINIMUM_AMOUNT,
            'transactionId' => 'required|min:12',
            'rrn' => 'required|min:12',
            'terminalId' => 'required|regex:/^[a-zA-Z0-9]{8}$/',
            'cardNumber' => 'required|min:12',
            'acquireBank' => 'required|digits:6',
            'fromAccount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
			Log::channel('interoperability')->info('Incoming-Transaction-Response: ' . json_encode($validator->errors()->first()));
            $encryptedData = $this->encryptResponse(json_encode(['responseCode' => 400, 'error' => $validator->errors()->first()]), env('ENCRYPTION_PASSWORD'));
            return response()->json(['data' => $encryptedData]);
        }

        $request = (object)$request;

        if ($this->checkTransactionId($request->transactionId)) {
			Log::channel('interoperability')->info('Incoming-Transaction-Response: Transaction ID already exist');
            $encryptedData = $this->encryptResponse(json_encode(['responseCode' => 400, 'error' => 'Transaction ID already exist']), env('ENCRYPTION_PASSWORD'));
            return response()->json(['data' => $encryptedData]);
        }

        DB::beginTransaction();
        try {
            $serviceAccounts = DB::connection('sqlsrv4')
                ->table('tbl_agency_banking_interoperability_service_accounts')
                ->select('account_code', 'account_number')
                ->where('is_active', self::ACTIVE)
                ->get()
                ->keyBy('account_code');

            $govtLevy = AbInteroperabilityCharge::query()->select('charge')
                ->where('from_amount', '<=', $request->amount)
                ->where('to_amount', '>=', $request->amount)
                ->where('charge_type', self::LEVY)
                ->where('is_active', self::ACTIVE)
                ->where('is_waitingApproval', 0)
                ->first();

            // Process based on transaction type
            if ($request->transactionType == self::AGENCY_WITHDRAW) {
                $cashWithdrawCharge = AbInteroperabilityCharge::query()->select('charge')
                    ->where('from_amount', '<=', $request->amount)
                    ->where('to_amount', '>=', $request->amount)
                    ->where('charge_type', self::WITHDRAW)
                    ->where('is_active', self::ACTIVE)
                    ->where('is_waitingApproval', 0)
                    ->first();

                $withdrawalCharge = $cashWithdrawCharge ? $cashWithdrawCharge->charge : 0;

				$serviceAccount = trim(strval($serviceAccounts->get(self::DEPOSIT_SERVICE_ACCOUNT)->account_number));
				
                $charges = $withdrawalCharge;

            } else {
                $cashDeposit = AbInteroperabilityCharge::select('charge')
                    ->where('from_amount', '<=', $request->amount)
                    ->where('to_amount', '>=', $request->amount)
                    ->where('charge_type', self::DEPOSIT)
                    ->where('is_active', self::ACTIVE)
                    ->where('is_waitingApproval', 0)
                    ->first();

                $depositCharge = $cashDeposit->charge;
				$depositCharge = $cashDeposit ? $cashDeposit->charge : 0;
				$serviceAccount = trim(strval($serviceAccounts->get(self::WITHDRAW_SERVICE_ACCOUNT)->account_number));
				
                $charges = $depositCharge;
            }
            //store transaction
            AbInteroperabilityTransaction::create([
                'from_account' => $request->fromAccount,
                'rrn' => $request->rrn,
                'terminal_id' => $request->terminalId,
                'amount' => $request->amount,
                'transaction_code' => $request->transactionType,
                'charge_response' => json_encode($charges),
                'transaction_id' => $request->transactionId,
                'transaction_type' => ($request->transactionType == self::AGENCY_WITHDRAW) ? 'IN-AGENCY-WITHDRAW' : 'IN-AGENCY-DEPOSIT',
                'card_number' => $this->encryptCardNumber($request->cardNumber),
                'agent_id' => $request->agentId ?? NULL,
                'acquire_bank' => $request->acquireBank,
                'transaction_charge' => $withdrawalCharge ?? $depositCharge,
                'uuid' => Str::uuid()
            ]);
            DB::commit();
			Log::channel('interoperability')->info('Incoming-Transaction-Response: Charges'. json_encode($charges));
			Log::channel('interoperability')->info('Incoming-Transaction-Response: serviceAccount'. json_encode($serviceAccount));
            $encryptedData = $this->encryptResponse(json_encode(['responseCode' => 200, 'charges' => $charges, 'serviceAccount' =>$serviceAccount]), env('ENCRYPTION_PASSWORD'));
			return response()->json(['data' => $encryptedData]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('interoperability')->info('Incoming-Transaction-Exception:' . json_encode($e->getMessage()));
            $encryptedData = $this->encryptResponse(json_encode(['responseCode' => 100, 'error' => 'Exception occured,try again later!']), env('ENCRYPTION_PASSWORD'));
            return response()->json(['data' => $encryptedData]);
        }

    }

    private function checkTransactionId($transactionId): bool
    {
        return AbInteroperabilityTransaction::where('transaction_id', $transactionId)->exists();
    }

    public function updateTransaction(Request $request)
    {
		Log::channel('interoperability')->info('Update-Transaction-Request:' . json_encode($request->data));
        $decryptedRequest = $this->decryptRequest(json_encode($request->data), env('ENCRYPTION_PASSWORD'));
        $request = json_decode($decryptedRequest, true);
        Log::channel('interoperability')->info('Update-Transaction-Request:' . json_encode($request));
        $validator = Validator::make($request, [
            'transactionId' => 'required',
            'responseCode' => 'required',
            'responseMessage' => 'required',
            'authCode' => 'required',
            'reversalState' => 'nullable|sometimes|in:True,False'
        ]);

        if ($validator->fails()) {
            $encryptedData = $this->encryptResponse(json_encode(['responseCode' => 400, 'error' => $validator->errors()->first()]), env('ENCRYPTION_PASSWORD'));
            return response()->json(['data' => $encryptedData]);
        }

        $request = (object)$request;
        $updateTransaction = AbInteroperabilityTransaction::query()->where('transaction_id', $request->transactionId)->first();
        if (!$updateTransaction) {
            $encryptedData = $this->encryptResponse(json_encode(['responseCode' => 100, 'message' => 'Transaction not found']), env('ENCRYPTION_PASSWORD'));
            return response()->json(['data' => $encryptedData]);
        }

        try {
            $updateTransaction->update([
                'response_code' => $request->responseCode,
                'response_message' => $request->responseMessage,
                'auth_code' => $request->authCode,
                //'to_account' => $request->toAccount,
                'reversal_state' => $request->reversalState != null ? ($request->reversalState == 'True' ? 1 : 0) : null,
            ]);
			Log::channel('interoperability')->info('Update-Transaction-Response: Successfully');
            $encryptedData = $this->encryptResponse(json_encode(['responseCode' => 200, 'message' => 'Transaction updated successfully']), env('ENCRYPTION_PASSWORD'));
            return response()->json(['data' => $encryptedData]);
        } catch (Exception $e) {
            Log::channel('interoperability')->info('Update-Transaction-Exception:' . json_encode($e->getMessage()));
            $encryptedData = $this->encryptResponse(json_encode(['responseCode' => 100, 'message' => 'Exception occured,try again later!']), env('ENCRYPTION_PASSWORD'));
            return response()->json(['data' => $encryptedData]);
        }
    }

    public function outgoingTransaction(Request $request)
    {
        $decryptedRequest = $this->decrypt($request->data, config("security.encryption_password"));
        $request = json_decode($decryptedRequest, true);
        Log::channel('interoperability')->info('Outgoing-Transaction-Request:' . json_encode($request));
        $validator = Validator::make($request, [
            'transactionType' => 'required|in:' . self::AGENCY_WITHDRAW . ',' . self::AGENCY_DEPOSIT,
            'amount' => 'required|numeric|min:' . self::MINIMUM_AMOUNT,
            'transactionId' => 'required|min:12',
            'rrn' => 'required',
            'terminalId' => 'required|regex:/^[a-zA-Z0-9]{8}$/',
            'cardNumber' => 'required|min:12',
            'agentId' => 'required|digits:6',
            'acquireBank' => 'required|digits:6',
            'fromAccount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $encryptedRequest = $this->encrypt(json_encode(['responseCode' => 400, 'error' => $validator->errors()->first()]), config("security.encryption_password"));
            return response()->json(['data' => $encryptedRequest]);
        }

        $request = (object)$request;
        if ($this->checkTransactionId($request->transactionId)) {
            $encryptedRequest = $this->encrypt(json_encode(['responseCode' => 400, 'error' => 'Transaction ID already exist']), config("security.encryption_password"));
            return response()->json(['data' => $encryptedRequest]);
        }

        DB::beginTransaction();
        try {
            $serviceAccounts = DB::connection('sqlsrv4')
                ->table('tbl_agency_banking_interoperability_service_accounts')
                ->select('account_code', 'account_number')
                ->where('is_active', self::ACTIVE)
                ->get()
                ->keyBy('account_code');

            // Process based on transaction type
            if ($request->transactionType == self::AGENCY_WITHDRAW) {
                $cashWithdrawCharge = AbInteroperabilityCharge::select('charge')
                    ->where('from_amount', '<=', $request->amount)
                    ->where('to_amount', '>=', $request->amount)
                    ->where('charge_type', self::WITHDRAW)
                    ->where('is_active', self::ACTIVE)
                    ->where('is_waitingApproval', 0)
                    ->first();

                $withdrawalCharge = $cashWithdrawCharge->charge;

                $charges = [
                    [
                        'chargeName' => 'PRINCIPAL',
                        'amount' => trim(strval($request->amount)),
                        'account' => trim(strval($serviceAccounts->get(self::PRINCIPAL_ACCOUNT)->account_number)),
                    ],
                    [
                        'chargeName' => 'EXPENSE',
                        'amount' => trim(strval($withdrawalCharge)),
                        'account' => trim(strval($serviceAccounts->get(self::EXPENSE_ACCOUNT)->account_number)),
                    ],
                ];

            } else {
                $cashDeposit = AbInteroperabilityCharge::select('charge')
                    ->where('from_amount', '<=', $request->amount)
                    ->where('to_amount', '>=', $request->amount)
                    ->where('charge_type', self::DEPOSIT)
                    ->where('is_active', self::ACTIVE)
                    ->where('is_waitingApproval', 0)
                    ->first();

                $depositCharge = $cashDeposit->charge;

                $charges = [
                    [
                        'chargeName' => 'PRINCIPAL',
                        'amount' => trim(strval($request->amount)),
                        'account' => trim(strval($serviceAccounts->get(self::PRINCIPAL_ACCOUNT)->account_number)),
                    ],
                    [
                        'chargeName' => 'EXPENSE',
                        'amount' => trim(strval($depositCharge)),
                        'account' => trim(strval($serviceAccounts->get(self::EXPENSE_ACCOUNT)->account_number)),
                    ],
                ];
            }

            //store transaction
            AbInteroperabilityTransaction::create([
                'from_account' => $request->fromAccount,
                'rrn' => $request->rrn,
                'terminal_id' => $request->terminalId,
                'amount' => $request->amount,
                'transaction_code' => $request->transactionType,
                'charge_response' => json_encode($charges),
                'transaction_id' => $request->transactionId,
                'transaction_type' => ($request->transactionType == self::AGENCY_WITHDRAW) ? 'OUT-AGENCY-WITHDRAW' : 'OUT-AGENCY-DEPOSIT',
                'card_number' => $this->encryptCardNumber($request->cardNumber),
                'agent_id' => $request->agentId,
                'acquire_bank' => $request->acquireBank,
                'transaction_charge' => $withdrawalCharge ?? $depositCharge,
                'uuid' => Str::uuid()
            ]);
            DB::commit();
            $encryptedRequest = $this->encrypt(json_encode(['responseCode' => 200, 'charges' => $charges]), config("security.encryption_password"));
            return response()->json(['data' => $encryptedRequest]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('interoperability')->info('Outgoing-Transaction-Exception:' . json_encode($e->getMessage()));
            $encryptedRequest = $this->encrypt(json_encode(['responseCode' => 100, 'error' => 'Exception occured,try again later!']), config("security.encryption_password"));
            return response()->json(['data' => $encryptedRequest]);
        }

    }

    public function balanceInquiry(Request $request)
    {
        $decryptedRequest = $this->decrypt($request->data, config("security.encryption_password"));
        $request = json_decode($decryptedRequest, true);
        Log::channel('interoperability')->info('Balance-Inquiry-Request:' . json_encode($request));
        $validator = Validator::make($request, [
            'transactionType' => 'required|in:' . self::AGENCY_BALANCE_INQUIRY,
            'accountNumber' => 'required|numeric',
            'transactionId' => 'required|min:12',
            'terminalId' => 'required|regex:/^[a-zA-Z0-9]{8}$/',
            'acquireBank' => 'required|digits:6',
            'agentId' => 'required|digits:6',
            'cardNumber' => 'required|min:12',
        ]);

        if ($validator->fails()) {
            $encryptedRequest = $this->encrypt(json_encode(['responseCode' => 400, 'error' => $validator->errors()->first()]), config("security.encryption_password"));
            return response()->json(['data' => $encryptedRequest]);
        }

        if ($this->checkTransactionId($request->transactionId)) {
            $encryptedRequest = $this->encrypt(json_encode(['responseCode' => 400, 'error' => 'Transaction ID already exist']), config("security.encryption_password"));
            return response()->json(['data' => $encryptedRequest]);
        }

        DB::beginTransaction();
        try {
            $balanceInquiry = AbInteroperabilityCharge::select('charge')
                ->where('charge_type', self::BALANCE_INQUIRY)
                ->where('is_active', self::ACTIVE)
                ->where('is_waitingApproval', 0)
                ->first();

            $balanceInquiryCharge = $balanceInquiry->charge;

            $charges = [
                [
                    'chargeName' => 'BALANCE-INQUIRY',
                    'amount' => strval($balanceInquiryCharge),
                ],
            ];

            AbInteroperabilityTransaction::create([
                'transaction_id' => $request->transactionId,
                'transaction_type' => 'BALANCE-INQUIRY',
                'transaction_code' => $request->transactionType,
                'from_account' => $request->accountNumber,
                'amount' => $balanceInquiryCharge,
                'terminal_id' => $request->terminalId,
                'transaction_charge' => $balanceInquiryCharge,
                'acquire_bank' => $request->acquireBank,
                'charge_response' => json_encode($charges),
                'card_number' => $this->encryptCardNumber($request->cardNumber),
                'agent_id' => $request->agentId,
                'uuid' => Str::uuid()
            ]);
            DB::commit();
            $encryptedRequest = $this->encrypt(json_encode(['responseCode' => 200, 'charges' => $charges]), config("security.encryption_password"));
            return response()->json(['data' => $encryptedRequest]);
        } catch (Exception $e) {
            DB::rollBack();

            Log::channel('interoperability')->info('Balance-Inquiry-Exception:' . json_encode($e->getMessage()));
            $encryptedRequest = $this->encrypt(json_encode(['responseCode' => 100, 'error' => 'Exception occured,try again later!']), config("security.encryption_password"));
            return response()->json(['data' => $encryptedRequest]);
        }
    }

    private function encryptCardNumber($cardNumber)
    {
        return Crypt::encryptString($cardNumber);
    }

    public function decryptCardNumber($encryptedCardNumber)
    {
        return Crypt::decryptString($encryptedCardNumber);
    }

    public function reportView()
    {
        $today = now();
        return view('agency.interoperability.reports', compact('today'));
    }

    public function export(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'status' => 'required|in:' . self::ALL . ',' . self::SUCCESS . ',' . self::FAILED
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['notification' => $validator->errors()->first(), 'color' => 'danger']);
        }

        try {
            $fromDate = $request->from_date;
            $toDate = $request->to_date;

            $transactions = AbInteroperabilityTransaction::query()->select('created_at', 'transaction_code', 'card_number',
                'agent_id', 'terminal_id', 'acquire_bank', 'amount', 'from_account', 'to_account', 'rrn', 'transaction_charge', 'response_code', 'transaction_type')->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

            if ($request->status == self::SUCCESS) {
                $transactions = $transactions->where('response_code', '200');
            } elseif ($request->status == self::FAILED) {
                $transactions = $transactions->where('response_code', '<>', '200')->whereNotNull('response_code');
            }

            if (isset($request->transactionType) && is_array($request->transactionType)) {
                $transactions = $transactions->whereIn('transaction_code', $request->transactionType);
            }

            $transactions = $transactions->get();
            $xls = new AbInteroperabilityExport();
            $xls->transactions = $transactions;

            return Excel::download($xls, "AB-Interoperability-Report: $fromDate - $toDate.xlsx");

        } catch (Exception $e) {
            Log::info("INTEROPERABILITY-REPORT-EXCEPTION: " . json_encode($e->getMessage()));
            return redirect()->back()->with(['notification' => "Something went wrong, please try again later", 'color' => 'danger']);
        }
    }

    public function reversalInquiry(Request $request)
    {
		Log::channel('interoperability')->info('Reversal-Inquiry-Encrypted:' . json_encode($request->all()));
        $decryptedRequest = $this->decryptRequest($request->data, env('ENCRYPTION_PASSWORD'));
        $request = json_decode($decryptedRequest, true);
        $request = json_encode($request);
		
        Log::channel('interoperability')->info('Reversal-Inquiry-Request:' . $request);
		$request = json_decode($request, true);
        $validator = Validator::make($request, [
            'transactionId' => 'required|regex:/^[a-zA-Z0-9]+$/',
        ]);

        if ($validator->fails()) {
			Log::channel('interoperability')->info('Reversal-Inquiry-Response: '. json_encode($validator->errors()->first()));
            $encryptedRequest = $this->encryptResponse(json_encode(['responseCode' => 400, 'error' => $validator->errors()->first()]), env('ENCRYPTION_PASSWORD'));
            return response()->json(['data' => $encryptedRequest]);
        }	
		$request = json_decode($decryptedRequest, true);
        $transaction = AbInteroperabilityTransaction::where('transaction_id', $request['transactionId'])
            ->first();

        if (!$transaction) {
			Log::channel('interoperability')->info('Reversal-Inquiry-Response: Record not found');
            $encryptedRequest = $this->encryptResponse(json_encode(['responseCode' => 400, 'error' => 'Record not found']), env('ENCRYPTION_PASSWORD'));
            return response()->json(['data' => $encryptedRequest]);
        }

        //append 99 to original transaction id to mark it as reversal
        $reversalTransactionId = trim($request['transactionId']. '99');

		//check for duplicate reversals
        if ($this->checkDuplicateReversal($reversalTransactionId)) {
			Log::channel('interoperability')->info('Reversal-Inquiry-Response: Duplicate reversal');
            $encryptedRequest = $this->encryptResponse(json_encode(['responseCode' => 109, 'message' => 'Duplicate reversal for this transaction']), env('ENCRYPTION_PASSWORD'));
            return response()->json(['data' => $encryptedRequest]);
        }

        try {
            //store new reversal transaction
            AbInteroperabilityTransaction::create([
                'from_account' => $transaction->from_account,
                'rrn' => $transaction->rrn,
                'terminal_id' => $transaction->terminal_id,
                'amount' => $transaction->amount,
                'transaction_code' => $transaction->transaction_code,
                'charge_response' => $transaction->charge_response,
                'transaction_id' => $reversalTransactionId,
                'transaction_type' => $transaction->transaction_type,
                'card_number' => $transaction->card_number,
                'agent_id' => $transaction->agent_id,
                'acquire_bank' => $transaction->acquire_bank,
                'transaction_charge' => $transaction->transaction_charge,
                'uuid' => Str::uuid()
            ]);
            $encryptedRequest = $this->encryptResponse(json_encode(['responseCode' => 200, 'transaction' => trim($transaction->charge_response), 'reversalTransactionId' => $reversalTransactionId, 'authCode'=>$transaction->auth_code]), env('ENCRYPTION_PASSWORD'));
            return response()->json(['data' => $encryptedRequest]);
        } catch (Exception $e) {
            Log::channel('interoperability')->info('Reversal-Inquiry-Exception:' . json_encode($e->getMessage()));
            $encryptedRequest = $this->encryptResponse(json_encode(['responseCode' => 100, 'message' => 'Exception occured,try again later!']), env('ENCRYPTION_PASSWORD'));
            return response()->json(['data' => $encryptedRequest]);
        }
    }

	private function checkDuplicateReversal($reversedTransactionId)
    {
        return AbInteroperabilityTransaction::where('transaction_id', $reversedTransactionId)->exists();
    }
	public function encryptResponse(string $data, string $password): string
    {
        $iv_len = 12;
        $iv = random_bytes($iv_len);
        $salt_len = 16;
        $salt = random_bytes($salt_len);
        $pw = $password;


        $tag = "";
        $key = hash_pbkdf2('sha256', $pw, $salt, 65536, 32, true);

        $encrypted = openssl_encrypt(
            $data,
            "aes-256-gcm",
            $key,
            $options=OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING,
            $iv,
            $tag,
            "",
            16
        );

        $result = $iv . $salt . $encrypted . $tag;
        return base64_encode($result);
    }

    public function decryptRequest(string $encodedData, string $password): string
    {
        // Decode the base64 encoded data
        $decodedData = base64_decode($encodedData);

        // Extract IV, salt, tag, and ciphertext
        $iv_len = 12;
        $iv = substr($decodedData, 0, $iv_len);
        $salt_len = 16;
        $salt = substr($decodedData, $iv_len, $salt_len);
        $tag_len = 16;
        $ciphertext = substr($decodedData, $iv_len + $salt_len, -16); // Exclude last 16 bytes for tag
        $tag = substr($decodedData, -$tag_len); // Extract last 16 bytes for tag

        $pw = $password;

        // Generate key using PBKDF2
        $key = hash_pbkdf2('sha256', $pw, $salt, 65536, 32, true);

        // Decrypt using AES-256-GCM
        $decrypted = openssl_decrypt(
            $ciphertext,
            "aes-256-gcm",
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        return $decrypted;
    }
}
