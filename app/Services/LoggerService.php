<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

enum LogChannelsEnum: string
{
    case UpdateBankInstallments = 'updateBankInstallments';
    case UpdateProducts = 'updateProducts';
    case UpdateProductsSpecs = 'updateProductsSpecs';
    case UpdateSalesPoints = 'updateSalesPoints';
    case UpdateSalesPointsStocks = 'updateSalesPointsStocks';
    case UpdatePaymentPlansForProduct = 'updatePaymentPlansForProduct';
    case UpdateConsignedProducts = 'updateConsignedProducts';
    case UpdateEbonds = 'updateEbonds';
    case ErpOrder = 'erpOrder';

    case InitiateFindeksRequest = 'initiateFindeksRequest';
    case FindeksRequestStatus = 'findeksRequestStatus';
    case FindeksRequestResult = 'findeksRequestResult';
    case FindeksMergeOrder = 'findeksMergeOrder';
    case SalesAgreementDocument = 'salesAgreementDocument';
    case CheckFindeksPin = 'checkFindeksPin';

    case GetPendingOrders = 'getPendingOrders';
    case UpdateOrder = 'updateOrder';
    case GetCCPaymentList = 'getCCPaymentList';
    case UpdatePayment = 'updatePayment';
    case ErpDirect = 'erpDirect';
    case ErpArtes = 'erpArtes';
    case ErpDelays = 'erpDelays';
    case ErpSalesAgreements = 'erpSalesAgreements';
    case Soap = 'soap';
    case Verification = 'verification';
    case MessagesEmail = 'messagesEmail';
    case MessagesSms = 'messagesSms';
    case Application = 'application';
    case ApplicationApi = 'applicationApi';
    case ApplicationOrdering = 'applicationOrdering';
    case GetConsignedProductsList = 'getConsignedProductsList';
    case EbondsList = 'getEbondsList';
}

class LoggerService
{
    public static function getChannel(LogChannelsEnum $channel): string
    {
        switch ($channel) {
            case LogChannelsEnum::UpdateBankInstallments:
                return 'erpJobsUpdateBankInstallments';
            case LogChannelsEnum::UpdateProducts:
                return 'erpJobsUpdateProducts';
            case LogChannelsEnum::UpdateProductsSpecs:
                return 'erpJobsUpdateProductsSpecs';
            case LogChannelsEnum::UpdateSalesPoints:
                return 'erpJobsUpdateSalesPoints';
            case LogChannelsEnum::UpdateSalesPointsStocks:
                return 'erpJobsUpdateSalesPointsStocks';
            case LogChannelsEnum::UpdatePaymentPlansForProduct:
                return 'erpJobsUpdatePaymentPlansForProduct';
            case LogChannelsEnum::UpdateConsignedProducts:
                return 'erpJobsUpdateConsignedProducts';
            case LogChannelsEnum::UpdateEbonds:
                return 'erpJobsUpdateEbonds';
            case LogChannelsEnum::ErpOrder:
                return 'erpJobsErpOrder';
            case LogChannelsEnum::GetPendingOrders:
                return 'erpApiGetPendingOrders';
            case LogChannelsEnum::UpdateOrder:
                return 'erpApiUpdateOrder';
            case LogChannelsEnum::GetCCPaymentList:
                return 'erpApiGetCCPaymentList';
            case LogChannelsEnum::UpdatePayment:
                return 'erpApiUpdatePayment';
            case LogChannelsEnum::ErpDirect:
                return 'erpDirect';
            case LogChannelsEnum::ErpArtes:
                return 'erpArtes';
            case LogChannelsEnum::ErpDelays:
                return 'erpDelays';
            case LogChannelsEnum::ErpSalesAgreements:
                return 'erpSalesAgreements';
            case LogChannelsEnum::Soap:
                return 'soap';
            case LogChannelsEnum::Verification:
                return 'verification';
            case LogChannelsEnum::MessagesEmail:
                return 'messagesEmail';
            case LogChannelsEnum::MessagesSms:
                return 'messagesSms';
            case LogChannelsEnum::Application:
                return 'application';
            case LogChannelsEnum::ApplicationApi:
                return 'applicationApi';
            case LogChannelsEnum::ApplicationOrdering:
                return 'applicationOrdering';
            case LogChannelsEnum::GetConsignedProductsList:
                return 'erpApiGetConsignedProductsList';
            case LogChannelsEnum::EbondsList:
                return 'erpApiEbondsList';

            case LogChannelsEnum::InitiateFindeksRequest:
                return 'erpJobsInitiateFindeksRequest';
            case LogChannelsEnum::FindeksRequestStatus:
                return 'erpJobsFindeksRequestStatus';
            case LogChannelsEnum::FindeksRequestResult:
                return 'erpJobsFindeksRequestResult';
            case LogChannelsEnum::FindeksMergeOrder:
                return 'erpJobsFindeksMergeOrder';
            case LogChannelsEnum::SalesAgreementDocument:
                return 'erpJobsSalesAgreementDocument';
            case LogChannelsEnum::CheckFindeksPin:
                return 'erpJobsCheckFindeksPin';
            default:
                return 'stack'; // Default logging channel for Laravel
        }
    }

    public static function logError(LogChannelsEnum $channel, string $message, array $context = []): void
    {
        Log::channel(self::getChannel($channel))->error($message, $context);
    }

    public static function logInfo(LogChannelsEnum $channel, string $message, array $context = []): void
    {
        Log::channel(self::getChannel($channel))->info($message, $context);
    }

    public static function logDebug(LogChannelsEnum $channel, string $message, array $context = []): void
    {
        Log::channel(self::getChannel($channel))->debug($message, $context);
    }

    public static function logSuccess(LogChannelsEnum $channel, string $message, array $context = []): void
    {
        Log::channel(self::getChannel($channel))->notice($message, $context);
    }

    public static function logWarning(LogChannelsEnum $channel, string $message, array $context = []): void
    {
        Log::channel(self::getChannel($channel))->warning($message, $context);
    }
}
