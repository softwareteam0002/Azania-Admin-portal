<?php

use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
     */

    'default' => env('LOG_CHANNEL', 'daily'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
     */

    'channels' => [
        'stack' => [
            'driver' => 'daiy',
            'channels' => ['single', 'bugsnag'],
            'ignore_exceptions' => false,
        ],
        'activity' => [
            'driver' => 'daily',
            'path' => storage_path('logs/activity/activity.log'),
            'level' => 'debug',
            'days' => 5478,
        ],

        'interoperability' => [
            'driver' => 'daily',
            'path' => storage_path('logs/interoperability/transactions.log'),
            'level' => 'debug',
            'days' => 5478,
        ],

        'sms' => [
            'driver' => 'daily',
            'path' => storage_path('logs/sms/sms.log'),
            'level' => 'debug',
            'days' => 5478,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 5000,
        ],

        'agency' => [
            'driver' => 'daily',
            'path' => storage_path('logs/agency/agency.log'),
            'level' => 'debug',
            'days' => 5000,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],

        'bugsnag' => [
            'driver' => 'bugsnag',
        ],
    ],

];
