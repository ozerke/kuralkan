<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

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

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => false,
    ],

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
            'driver' => 'stack',
            'channels' => ['daily'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'critical'),
            'replace_placeholders' => true,
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => env('LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class),
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
                'connectionString' => 'tls://' . env('PAPERTRAIL_URL') . ':' . env('PAPERTRAIL_PORT'),
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
            'facility' => LOG_USER,
            'replace_placeholders' => true,
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

        // New logs
        'erpJobsUpdateBankInstallments' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/jobs/updateBankInstallments/log.log'),
            'days' => 8
        ],
        'erpJobsUpdateProducts' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/jobs/updateProducts/log.log'),
            'days' => 8
        ],
        'erpJobsUpdateProductsSpecs' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/jobs/updateProductsSpecs/log.log'),
            'days' => 8
        ],
        'erpJobsUpdateSalesPoints' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/jobs/updateSalesPoints/log.log'),
            'days' => 8
        ],
        'erpJobsUpdateSalesPointsStocks' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/jobs/updateSalesPointsStocks/log.log'),
            'days' => 8
        ],
        'erpJobsUpdatePaymentPlansForProduct' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/jobs/updatePaymentPlansForProduct/log.log'),
            'days' => 8
        ],
        'erpJobsErpOrder' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/jobs/erpOrder/log.log'),
            'days' => 8
        ],
        'erpJobsUpdateConsignedProducts' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/jobs/updateConsignedProducts/log.log'),
            'days' => 8
        ],
        'erpJobsUpdateEbonds' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/api/ebondsUpdate/log.log'),
            'days' => 8
        ],
        'erpApiGetPendingOrders' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/api/getPendingOrders/log.log'),
            'days' => 8
        ],
        'erpApiUpdateOrder' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/api/updateOrder/log.log'),
            'days' => 8
        ],
        'erpApiGetCCPaymentList' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/api/getCCPaymentList/log.log'),
            'days' => 8
        ],
        'erpApiEbondsList' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/api/ebondsList/log.log'),
            'days' => 8
        ],
        'erpApiUpdatePayment' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/api/updatePayment/log.log'),
            'days' => 8
        ],
        'erpDirect' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/direct/log.log'),
            'days' => 8
        ],
        'erpArtes' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/artes/log.log'),
            'days' => 8
        ],
        'erpDelays' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/delays/log.log'),
            'days' => 8
        ],
        'erpSalesAgreements' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/salesAgreements/log.log'),
            'days' => 8
        ],
        'soap' => [
            'driver' => 'daily',
            'path' => storage_path('logs/soap/log.log'),
            'days' => 8
        ],
        'verification' => [
            'driver' => 'daily',
            'path' => storage_path('logs/verification/log.log'),
            'days' => 8
        ],
        'messagesEmail' => [
            'driver' => 'daily',
            'path' => storage_path('logs/messages/email/log.log'),
            'days' => 8
        ],
        'messagesSms' => [
            'driver' => 'daily',
            'path' => storage_path('logs/messages/sms/log.log'),
            'days' => 8
        ],
        'application' => [
            'driver' => 'daily',
            'path' => storage_path('logs/application/log.log'),
            'days' => 8
        ],
        'applicationApi' => [
            'driver' => 'daily',
            'path' => storage_path('logs/applicationApi/log.log'),
            'days' => 8
        ],
        'applicationOrdering' => [
            'driver' => 'daily',
            'path' => storage_path('logs/applicationOrdering/log.log'),
            'days' => 8
        ],
        'erpApiGetConsignedProductsList' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/api/getConsignedProductsList/log.log'),
            'days' => 8
        ],

        'erpJobsInitiateFindeksRequest' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/jobs/initiateFindeksRequest/log.log'),
            'days' => 8
        ],
        'erpJobsFindeksRequestStatus' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/jobs/findeksRequestStatus/log.log'),
            'days' => 8
        ],
        'erpJobsFindeksRequestResult' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/jobs/findeksRequestResult/log.log'),
            'days' => 8
        ],
        'erpJobsFindeksMergeOrder' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/jobs/findeksMergeOrder/log.log'),
            'days' => 8
        ],
        'erpJobsSalesAgreementDocument' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/jobs/salesAgreementDocument/log.log'),
            'days' => 8
        ],
        'erpJobsCheckFindeksPin' => [
            'driver' => 'daily',
            'path' => storage_path('logs/erp/jobs/checkFindeksPin/log.log'),
            'days' => 8
        ],
    ],

];
