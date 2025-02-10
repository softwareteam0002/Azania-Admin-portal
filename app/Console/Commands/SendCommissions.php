<?php

namespace App\Console\Commands;

use App\AbBranch;
use App\Devices;
use App\Mail\PasswordResetMail;
use App\Operator;
use App\TblABBankAccounts;
use App\TblAgent;
use App\TblAgentDevice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailNotification;

class SendCommissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncing old agency data to new agency';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Mail::to('costantine.tarimo@ubx.co.tz')->queue(new PasswordResetMail("Costa", 'https://google.com'));
		$this->info('Email Sent.............');
		die;

        try {
            $subscribers = DB::connection('sqlsrv5')->select("
            SELECT *
            FROM tbl_Flares_Ussd_Subscribers AS a
            LEFT JOIN tbl_Flares_Ussd_User_Bank_Accounts AS b ON a.subscriber_id = b.subscriber_id
            LEFT JOIN tbl_agency_banking_registered_agent_details AS c ON a.subscriber_id = c.agent_id
            LEFT JOIN tbl_agency_banking_agent_location as d ON c.agent_location = d.location_id
            LEFT JOIN tbl_agency_banking_bank_branches AS e on c.branch_id = e.branch_id
            WHERE a.agent_mapp_enabled = ? AND a.subscribe_bank_id = ?
        ", ['A', 12]);


            if (!$subscribers) {
                $this->info('Data not found...');
            }

            DB::beginTransaction();
            foreach ($subscribers as $agent) {
                $branch = AbBranch::create([
                    'branchName' => $agent->branch_name,
                    'branch_code' => 001,
                    'initiator_id' => 2051,
                    'approver_id' => 2080,
                    'description' => 'MHB Branch',
                    'address' => $agent->branch_address,
                    'created_at' => null,
                    'updated_at' => null,
                    'isWaitingApproval' => 0,
                    'isDisabled' => null,
                    'disabledBy_id' => null,
                    'status' => null

                ]);

                $agenting = TblAgent::create([
                    'agent_msisdn' => $agent->subscriber_msisdn,
                    'agent_language' => $agent->subscriber_language,
                    'agent_date_registered' => $agent->subscriber_date_registered,
                    'agent_username' => $agent->subscriber_msisdn,
                    'agent_password' => Hash::make(random_int(1000, 9999)),
                    'agent_valid_id_number' => null,
                    'agent_full_name' => $agent->subscriber_full_name,
                    'agent_business_license_number' => $agent->business_license,
                    'business_certificate_registration_number' => $agent->business_certificate_regID,
                    'agent_status' => ($agent->subscriber_status == 'A') ? 1 : 0,
                    'agent_bank_id' => 1,
                    'agent_reg_source' => $agent->subscriber_reg_source,
                    'is_initiator' => 1,
                    'is_approver' => 1,
                    'agent_address' => $agent->subscriber_address,
                    'agent_location' => $agent->location_name,
                    'agent_float_limit' => 50000000,
                    'agent_daily_limit' => 10000000,
                    'branch_id' => $branch->branch_id,
                    'agent_menu' => "BI~DC~WC~FT~AS~MS~UP~BW",
                    'initiator_id' => 2051,
                    'approver_id' => 2080,
                    'isWaitingApproval' => 0,
                    'branchName' => $branch->branch_name,
                    'cbsbranchID' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'clientId' => null,
                    'email' => null,

                ]);

                $account = TblABBankAccounts::create([
                    'account_type_id' => ($agent->subscribe_bank_account_type == '00') ? 1 : 2,
                    'bank_account' => $agent->subscribe_bank_account_id,
                    'account_status' => 1,
                    'agent_id' => $agenting->agent_id,
                    'registration_status' => 1,
                    'initiator_id' => 2080,
                    'approver_id' => 2051,
                    'isWaitingApproval' => 0,
                    'responseCode' => null,
                    'responseMessage' => null,
                    'transactionTimestamp' => null,
                    'transactionId' => null,
                    'branchId' => $branch->id,
                    'clientId' => $agent->subscriber_id,
                    'clientName' => null,
                    'currencyID' => null,
                    'productID' => null,
                    'productName' => null,
                    'accountName' => null,
                    'address' => null,
                    'city' => null,
                    'countryID' => null,
                    'countryName' => null,
                    'mobile' => null,
                    'emailID' => null,
                    'aCStatus' => null,
                    'branchName' => null,
                    'createdOn' => null,
                    'updateCount' => null,
                ]);

                $device = Devices::create([
                    'device_status' => 1,
                    'registered_by' => 2080,
                    'terminal_ID' => $agent->agent_terminal_id,
                    'branch_id' => $branch->id,
                    'device_imei1' => $agent->mapp_imei_number,
                    'device_imei2' => null,
                    'trading_account_id' => $account->id,
                    'commision_account_id' => $account->id,
                ]);

                Operator::create([
                    'operator_fullname' => $agent->subscriber_full_name,
                    'operator_password' => Hash::make(random_int(1000, 9999)),
                    'operator_msisdn' => $agent->subscriber_msisdn,
                    'location' => $agent->location_name,
                    'device_id' => $device->device_id,
                    'agent_id' => $agenting->agent_id,
                    'operator_status' => ($agent->subscriber_status == 'A') ? 1 : 0,
                    'token' => null,
                    'operator_menu' => 'BI~DC~WC~AO~AS~MS~UP~BW~AC',
                    'initiator_id' => 2051,
                    'approver_id' => 2080,
                    'is_initiator' => 1,
                    'is_approver' => 1,
                    'login_counts' => 0,
                    'isWaitingApproval' => 0,
                    'isDeleted' => null,
                    'deletedBy_id' => null,
                ]);

                TblAgentDevice::create([
                    'agent_id' => $agenting->agent_id,
                    'device_id' => $device->device_id,
                    'status' => 1,
                    'initiator_id' => 2051,
                    'approver_id' => 2080,
                    'isWaitingApproval' => 0
                ]);

            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $this->info($e);
        }


    }


}
