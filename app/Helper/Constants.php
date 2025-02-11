<?php

namespace App\Helper;

class Constants
{
    /*
     * MTI MESSAGES
    */
    public const FUND_TRANSFER = '0003';
    public const CARD_WITHDRAW = '0009';
    public const DEPOSIT = '0013';
    public const DEPOSIT_QUERY_NAME = '0014';
    public const BALANCE_INQUIRY = '0018';
    public const CUSTOMER_MINISTATEMENT = '0023';
    public const UTILITY_PAYMENTS = '0028';
    public const AGENT_MINISTATEMENT = '0030';
    public const REPRINTING_DATA = '0031';
    public const SAVE_REPRINTING_DETAILS = '0032';
    public const CARDLESS_WITHDRAW = '1012';
    public const AGENT_STATEMENT = '1025';
    public const CUSTOMER_QUERY_CTL_DETAILS = '1026';
    public const DATA_MAP = '1027';
    public const NIDA = '1028';
    public const ACCOUNT_OPENING = '1029';
    public const GEPG_PAYMENTS = '1008';


    /*
     * RESPONSE MESSAGES
    */
    public const INVALID_CREDENTIALS = 'Invalid Credentials';
    public const FAILED_VALIDATING_IMEI = self::INVALID_CREDENTIALS;
    public const OPERATOR_INACTIVE_SUSPENDED = 'Account Locked';// OPERATOR/AGENT IS INACTIVE/SUSPENDED
    public const OPERATOR_NOT_EXIST = self::INVALID_CREDENTIALS;
    public const AGENT_NOT_EXIST = self::INVALID_CREDENTIALS;
    public const AGENT_USERNAME_REQUIRED = 'Agent Username Required';
    public const FAILED_TO_GET_KEY = 'Bad Request';
    public const SUCCESS_OPERATOR_PIN_CHANGE = 'PIN Changed Successfully';
    public const NO_MATCHING_RECORDS = self::INVALID_CREDENTIALS;
    public const DECRYPTION_FAILED = self::FAILED_TO_GET_KEY;
    public const DEVICE_INACTIVE_SUSPENDED = 'Device Locked';

}
