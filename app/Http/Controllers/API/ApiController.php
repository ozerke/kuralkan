<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Campaign;
use App\Models\LegalRegistration;
use App\Models\OrderCampaign;
use App\Models\ProductTranslation;
use App\Models\SalesAgreement;
use App\Services\CreditCardGateway;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ApiController extends Controller
{
    public function getInstallmentsByCreditCardDigits(Request $request)
    {
        try {
            $creditCardDigits = $request->input('creditCardDigits');
            $price = $request->input('price');
            $paymentExpCode = $request->input('campaignCode');
            $orderId = $request->input('orderId');

            $installmentCount = 12;

            if (isset($paymentExpCode) && isset($orderId)) {
                $campaign = OrderCampaign::where('order_id', $orderId)->first();

                if ($campaign) {
                    $installmentCount = $campaign->installments;
                }
            }

            // 20240131 - OE - converting to BulutTahsilat's
            $gateway = new CreditCardGateway();
            $installments = $gateway->bulutInstallments($price, $creditCardDigits, isset($paymentExpCode) ? $paymentExpCode : null);

            if (!$installments) {
                return response()->json(['data' => ['logo' => null, 'color' => '#101D32', 'installments' => Bank::getDefaultInstallment($price)]]);
            }

            $vPosBankCode = str_pad($installments[0]->VPosFirmBankCode, 5, "0", STR_PAD_LEFT);

            $bank = Bank::where('vpos_bank_code', $vPosBankCode)->first();

            $erpBankName = ($bank) ? $bank->erp_bank_name : '';
            $bankLogo = ($bank) ? $bank->ccGroup()->logo : '';

            $data = [
                'color' => Bank::COLORS[$erpBankName] ?? '#101D32',
                'logo' => asset('/build/images/banks/' . $bankLogo),
                'installments' => $gateway->findInstallments($installments, $installmentCount)
            ];

            return response()->json(['data' => $data]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ApplicationApi, 'API getInstallmentsByCreditCardDigits', ['e' => $e]);

            return response()->json(['error' => 'Could not get installments list.'], 400);
        }
    }

    public function searchForProducts(Request $request)
    {
        try {
            $searchQuery = $request->input('q');

            if (empty($searchQuery)) {
                return response()->json([
                    'products' => []
                ]);
            }

            if ($request->input('locale')) {
                App::setLocale($request->locale);
            }

            $results = ProductTranslation::with('product')->get();

            $results = $results
                ->unique('product_id')
                ->map(fn ($translation) => [
                    'url' => $translation->product->detailsUrl(),
                    'img' => $translation->product->getImageUrl(),
                    'title' => $translation->getSearchableTitle(),
                    'keywords' => $translation->product->currentTranslation->getSearchableKeywords(),
                    'fullTitle' => $translation->product->currentTranslation->product_name,
                    'display' => $translation->product->display == 't'
                ])
                ->filter(fn ($translation) => (
                    str_contains($translation['title'], strtolower($searchQuery)) ||
                    str_contains(strtolower($translation['fullTitle']), strtolower($searchQuery)) ||
                    str_contains(strtolower($translation['keywords']), strtolower($searchQuery)))
                    && $translation['display'])
                ->values();

            return response()->json([
                'products' => $results
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ApplicationApi, 'API searchForProducts', ['e' => $e]);

            return response()->json(['error' => 'Error occurred.'], 400);
        }
    }

    public function getLegalDocument(Request $request, $document)
    {
        return LegalRegistration::returnDocument($document);
    }

    public function getNotaryDocument(Request $request, $document)
    {
        return SalesAgreement::returnDocument($document);
    }
}
