<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\LegalRegistration;
use App\Models\Order;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Exception;
use Illuminate\Http\Request;

class LegalRegistrationController extends Controller
{
    public function legalForm(Request $request, $orderNo)
    {
        try {
            if (auth()->user()->isAdmin()) {
                $order = Order::with(['productVariation', 'salesAgreement'])->where('order_no', $orderNo)->first();

                if (!$order) {
                    return back()->with('error', __('web.not-found'));
                }

                $form = LegalRegistration::where('order_id', $order->id)->first();

                if ($form && $form->approved_by_erp != 'declined') {
                    return back()->with('error', __('app.error-occured'));
                }

                if (empty($order->invoice_link) || !empty($order->temprorary_licence_doc_link)) {
                    return back()->with('error', __('app.error-occured'));
                }

                $artesFields = $order->getArtesFields();

                return view('home.orders.legal-registration-form')->with([
                    'order' => $order,
                    'isCompany' => $order->invoiceUser->isCompany(),
                    'artesFields' => $artesFields
                ]);
            }

            $order = auth()->user()->orders()->with(['productVariation', 'salesAgreement'])->where('order_no', $orderNo)->first();

            if (!$order) {
                $order = auth()->user()->createdOrders()->with(['productVariation', 'salesAgreement'])->where('order_no', $orderNo)->first();
                if (!$order) {
                    return redirect()->route('home');
                }
            }

            $form = LegalRegistration::where('order_id', $order->id)->first();

            if ($form && $form->approved_by_erp != 'declined') {
                return redirect()->route('home');
            }

            if (empty($order->invoice_link) || !empty($order->temprorary_licence_doc_link)) {
                return redirect()->route('home');
            }

            $artesFields = $order->getArtesFields();

            return view('home.orders.legal-registration-form')->with([
                'order' => $order,
                'isCompany' => $order->invoiceUser->isCompany(),
                'artesFields' => $artesFields
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Customer Legal Form', ['e' => $e]);

            if (auth()->user()->isAdmin()) {
                return redirect()->route('orders.details', ['orderId' => $order->id])->with('error',  $e->getMessage());
            }

            return redirect()->route('panel')->with('error', 'Error occurred. Contact the support.');
        }
    }

    public function submitForm(Request $request, $orderNo)
    {
        try {
            $order = auth()->user()->orders()->with(['productVariation', 'salesAgreement'])->where('order_no', $orderNo)->first();

            if (auth()->user()->isAdmin()) {
                $order = Order::with(['productVariation', 'salesAgreement'])->where('order_no', $orderNo)->first();

                if (!$order) {
                    return back()->with('error', __('web.not-found'));
                }
            }

            if (!$order) {
                $order = auth()->user()->createdOrders()->with(['productVariation', 'salesAgreement'])->where('order_no', $orderNo)->first();
                if (!$order) {
                    return redirect()->route('home');
                }
            }

            if (empty($order->invoice_link) || !empty($order->temprorary_licence_doc_link) || empty($order->chasis_no)) {
                return redirect()->route('home');
            }

            $form = LegalRegistration::where('order_id', $order->id)->first();

            if ($form && $form->approved_by_erp != 'declined') {
                return redirect()->route('home');
            }

            $userNationalId = $order->invoiceUser->national_id;

            $checkableData = $request->except('authorized_national_id');

            $containsNationalId = collect($checkableData)->contains(function ($value) use ($userNationalId) {
                return $value === $userNationalId;
            });

            if ($containsNationalId) {
                return back()->with('error', __('web.nationalIdError'));
            }

            $artesResponse = $order->checkLegalRegistrationState(true);

            if ($artesResponse && in_array($artesResponse['is_registered'], ["10", "11", "12"])) {
                return back()->with('success', __('web.already-verified'));
            }

            if (!$artesResponse || empty($artesResponse['type']) || empty($artesResponse['no'])) {
                LoggerService::logError(LogChannelsEnum::ErpArtes, 'Submit Artes form: No type & No', ['artesResponse' => $artesResponse, 'order' => $order->id]);

                return back()->with('error', __('app.error-occured'));
            }

            $usageType = $request->input('usage_type');
            $insuranceNumber = $request->input('insurance_number');

            if ($usageType === 'individual') {
                $idType = $request->input('id_type');

                switch ($idType) {
                    case 'id_card':
                        $idType = 1;
                        break;
                    case 'new_id_card':
                        $idType = 2;
                        break;
                    case 'temp_id_doc':
                        $idType = 3;
                        break;
                    default:
                        $idType = 0;
                        break;
                }

                $idDocFileFront = $request->file('id_doc_file_front');
                $idDocFileBack = $request->file('id_doc_file_back');

                if ($idDocFileFront) {
                    $idDocFileFront = LegalRegistration::uploadDocument($idDocFileFront, $order->chasis_no, 'kimlik_onyuz');
                }

                if ($idDocFileBack) {
                    $idDocFileBack = LegalRegistration::uploadDocument($idDocFileBack, $order->chasis_no, 'kimlik_arkayuz');
                }

                $idSerial = $request->input('id_serial');
                $idNo = $request->input('id_no');

                $newIdSerialNo = $request->input('new_id_serial_no');

                $tempDocNo = $request->input('temp_doc_no');

                $documentsList = join('$', [$idDocFileFront, $idDocFileBack]);

                $formData = [
                    "insurance_number" => $insuranceNumber,
                    "neighbourhood" => "",
                    "street" => "",
                    "buildingNo" => "",
                    "flatNo" => "",
                    "id_type" => $idType,
                    "authorized_name" => "",
                    "authorized_national_id" => "",
                    "id_serial" => $idSerial,
                    "id_no" => $idNo,
                    "new_id_serial_no" => $newIdSerialNo,
                    "temp_doc_no" => $tempDocNo,
                    "end_date" => "",
                    "representation_type" => "",
                    "place_of_retrieval" => "",
                    "document_date" => "",
                    "delimitedDocumentList" => $documentsList,
                    "usage_type" => 1,
                    "number_of_documents" => ""
                ];

                $setArtesResponse = (new SoapSendOrderController())->setArtes($artesResponse['type'], $artesResponse['no'], $order->invoiceUser, $formData);

                if ($setArtesResponse['response'] != "1") {
                    LoggerService::logError(LogChannelsEnum::ErpArtes, 'Set Artes Error', ['artesResponse' => $artesResponse, 'order' => $order->id, 'setArtes' => $setArtesResponse['response']]);

                    LegalRegistration::deleteByName($idDocFileFront);
                    LegalRegistration::deleteByName($idDocFileBack);

                    return back()->with('error', __('app.error-occured'));
                }

                $legalForm = LegalRegistration::where('order_id', $order->id)->first();

                if ($legalForm) {
                    $legalForm->update([
                        'approved_by_erp' => 'pending',
                        'params' => $setArtesResponse['params'],
                        'id_card_front' => $idDocFileFront,
                        'id_card_back' => $idDocFileBack,
                    ]);
                } else {
                    $legalForm = LegalRegistration::create([
                        'order_id' => $order->id,
                        'params' => $setArtesResponse['params'],
                        'id_card_front' => $idDocFileFront,
                        'id_card_back' => $idDocFileBack,
                    ]);
                }

                if (!$legalForm) {
                    LegalRegistration::deleteByName($idDocFileFront);
                    LegalRegistration::deleteByName($idDocFileBack);

                    return back()->with('error', __('app.error-occured'));
                }

                if (auth()->user()->isAdmin()) {
                    return redirect()->route('orders.details', ['orderId' => $order->id])->with('success', __('web.form-submitted'));
                }

                if (auth()->user()->isShopOrService()) {
                    return redirect()->route('shop.order-details', ['orderNo' => $order->order_no])->with('success', __('web.form-submitted'));
                } else {
                    return redirect()->route('customer.order-details', ['orderNo' => $order->order_no])->with('success', __('web.form-submitted'));
                }
            } else if ($usageType === 'commercial') {
                $street = $request->input('street');
                $neighbourhood = $request->input('neighbourhood');
                $buildingNo = $request->input('building_no');
                $flatNo = $request->input('flat_no');

                $idType = $request->input('id_type');

                switch ($idType) {
                    case 'id_card':
                        $idType = 1;
                        break;
                    case 'new_id_card':
                        $idType = 2;
                        break;
                    case 'temp_id_doc':
                        $idType = 3;
                        break;
                    default:
                        $idType = 0;
                        break;
                }

                $idSerial = $request->input('id_serial');
                $idNo = $request->input('id_no');
                $newIdSerialNo = $request->input('new_id_serial_no');
                $tempDocNo = $request->input('temp_doc_no');

                $authorizedName = $request->input('authorized_name');
                $authorizedNationalId = $request->input('authorized_national_id');
                $representationType = $request->input('representation_type');
                $numberOfDocuments = $request->input('number_of_documents');
                $placeOfRetrieval = $request->input('place_of_retrieval');
                $documentDate = $request->input('document_date');
                $endDate = $request->input('end_date');

                $signatureCircular = $request->file('signature_circular');
                $operatingCertificate = $request->file('operating_certificate');
                $commercialRegistryGazette = $request->file('commercial_registry_gazette');
                $officialIdentityFront = $request->file('official_identity_front');
                $officialIdentityBack = $request->file('official_identity_back');
                $powerOfAttorney = $request->file('power_of_attorney');

                if ($signatureCircular) {
                    $signatureCircular = LegalRegistration::uploadDocument($signatureCircular, $order->chasis_no, 'imza_sirkuleri');
                }

                if ($operatingCertificate) {
                    $operatingCertificate = LegalRegistration::uploadDocument($operatingCertificate, $order->chasis_no, 'faaliyet_belgesi');
                }

                if ($commercialRegistryGazette) {
                    $commercialRegistryGazette = LegalRegistration::uploadDocument($commercialRegistryGazette, $order->chasis_no, 'resmi_gazete');
                }

                if ($officialIdentityFront) {
                    $officialIdentityFront = LegalRegistration::uploadDocument($officialIdentityFront, $order->chasis_no, 'yetkili_kimlik_onyuz');
                }

                if ($officialIdentityBack) {
                    $officialIdentityBack = LegalRegistration::uploadDocument($officialIdentityBack, $order->chasis_no, 'yetkili_kimlik_arkayuz');
                }

                if ($powerOfAttorney) {
                    $powerOfAttorney = LegalRegistration::uploadDocument($powerOfAttorney, $order->chasis_no, 'vekaletname');
                }

                $documentsList = join('$', [
                    $signatureCircular,
                    $operatingCertificate,
                    $commercialRegistryGazette,
                    $officialIdentityFront,
                    $officialIdentityBack,
                    $powerOfAttorney
                ]);

                $formData = [
                    "insurance_number" => $insuranceNumber,
                    "neighbourhood" => $neighbourhood,
                    "street" => $street,
                    "buildingNo" => $buildingNo,
                    "flatNo" => $flatNo,
                    "id_type" => $idType,
                    "authorized_name" => $authorizedName,
                    "authorized_national_id" => $authorizedNationalId,
                    "id_serial" => $idSerial,
                    "id_no" => $idNo,
                    "new_id_serial_no" => $newIdSerialNo,
                    "temp_doc_no" => $tempDocNo,
                    "end_date" => $endDate,
                    "representation_type" => $representationType,
                    "place_of_retrieval" => $placeOfRetrieval,
                    "document_date" => $documentDate,
                    "delimitedDocumentList" => $documentsList,
                    "usage_type" => 2,
                    "number_of_documents" => $numberOfDocuments
                ];

                $setArtesResponse = (new SoapSendOrderController())->setArtes($artesResponse['type'], $artesResponse['no'], $order->invoiceUser, $formData);

                if ($setArtesResponse['response'] != "1") {
                    LoggerService::logError(LogChannelsEnum::ErpArtes, 'Set Artes Error', ['artesResponse' => $artesResponse, 'order' => $order->id, 'setArtes' => $setArtesResponse['response']]);

                    LegalRegistration::deleteByName($signatureCircular);
                    LegalRegistration::deleteByName($operatingCertificate);
                    LegalRegistration::deleteByName($commercialRegistryGazette);
                    LegalRegistration::deleteByName($officialIdentityFront);
                    LegalRegistration::deleteByName($officialIdentityBack);
                    LegalRegistration::deleteByName($powerOfAttorney);

                    return back()->with('error', __('app.error-occured'));
                }

                $legalForm = LegalRegistration::where('order_id', $order->id)->first();

                if ($legalForm) {
                    $legalForm->update([
                        'approved_by_erp' => 'pending',
                        'params' => $setArtesResponse['params'],
                        'signature_circular' => $signatureCircular,
                        'operating_certificate' => $operatingCertificate,
                        'registry_gazzete' => $commercialRegistryGazette,
                        'circular_indentity_front' => $officialIdentityFront,
                        'circular_indentity_back' => $officialIdentityBack,
                        'power_of_attorney' => $powerOfAttorney
                    ]);
                } else {
                    $legalForm = LegalRegistration::create([
                        'order_id' => $order->id,
                        'params' => $setArtesResponse['params'],
                        'signature_circular' => $signatureCircular,
                        'operating_certificate' => $operatingCertificate,
                        'registry_gazzete' => $commercialRegistryGazette,
                        'circular_indentity_front' => $officialIdentityFront,
                        'circular_indentity_back' => $officialIdentityBack,
                        'power_of_attorney' => $powerOfAttorney
                    ]);
                }

                if (!$legalForm) {
                    LegalRegistration::deleteByName($signatureCircular);
                    LegalRegistration::deleteByName($operatingCertificate);
                    LegalRegistration::deleteByName($commercialRegistryGazette);
                    LegalRegistration::deleteByName($officialIdentityFront);
                    LegalRegistration::deleteByName($officialIdentityBack);
                    LegalRegistration::deleteByName($powerOfAttorney);

                    return back()->with('error', __('app.error-occured'));
                }

                if (auth()->user()->isAdmin()) {
                    return redirect()->route('orders.details', ['orderId' => $order->id])->with('success', __('web.form-submitted'));
                }

                if (auth()->user()->isShopOrService()) {
                    return redirect()->route('shop.order-details', ['orderNo' => $order->order_no])->with('success', __('web.form-submitted'));
                } else {
                    return redirect()->route('customer.order-details', ['orderNo' => $order->order_no])->with('success', __('web.form-submitted'));
                }
            } else {
                return back()->with('error', __('web.form-has-invalid-inputs'));
            }
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ErpArtes, 'Error while submitting set Artes form', ['e' => $e]);

            if (auth()->user()->isAdmin()) {
                return redirect()->route('orders.details', ['orderId' => $order->id])->with('error',  __('app.error-occured'));
            }

            return redirect()->route('panel')->with('error',  __('app.error-occured'));
        }
    }
}
