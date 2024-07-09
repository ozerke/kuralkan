<?php
/*
SoapSendOrderController purpose: Get an order to the web service of Kuralkan
Always login -> send order -> logout
*/

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use DOMDocument;
use App\Models\Order;
use App\Models\ProductVariation;
use App\Models\User;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class SoapSendOrderController extends Controller
{
	protected $soapHeader = "<soapenv:Envelope xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xmlns:xsd='http://www.w3.org/2001/XMLSchema' xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:web='http://web.ias.com'><soapenv:Body>";
	protected $soapFooter = "</soapenv:Body></soapenv:Envelope>";
	protected $loginParameters = [
		['p_strClient', 'xsd:string', '00'],
		['p_strLanguage', 'xsd:string', 'T'],
		['p_strDBName', 'xsd:string', 'KURALKAN'],
		['p_strDBServer', 'xsd:string', 'CANIAS'],
		['p_strAppServer', 'xsd:string', 'KRNERP:27499/WEB'],
		['p_strUserName', 'xsd:string', 'WEBSHOP'],
		['p_strPassword', 'xsd:string', 'w3bSh0p**1']
	];

	private function soapData($data)
	{
		$return = $this->soapHeader . "<web:" . $data['function'] . " soapenv:encodingStyle='http://schemas.xmlsoap.org/soap/encoding/'>";
		foreach ($data['parameters'] as $param) {
			$return .= "<" . $param[0] . " xsi:type='" . $param[1] . "'>" . $param[2] . "</" . $param[0] . ">";
		}
		$return .= "</web:" . $data['function'] . ">" . $this->soapFooter;
		return $return;
	}

	private function sendCurl($soapData)
	{
		if (!config('app.erp_url')) {
			throw new Exception('ERP_URL is missing');
		}

		$ch = curl_init(config('app.erp_url'));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml', 'Authorization: Basic', 'SOAPAction: http://apache.org'));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $soapData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		$data = curl_exec($ch);

		return $data;
	}

	public function clearResponse($data)
	{
		$responseStr = str_replace('<' . '?' . 'xml version="1.0" encoding="UTF-8"' . '?' . '>', '', $data['data']);
		$responseStr = str_replace('<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">', '', $responseStr);
		$responseStr = str_replace('<soapenv:Body>', '', $responseStr);
		$responseStr = str_replace('<ns1:' . $data['function'] . 'Response soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" xmlns:ns1="http://web.ias.com">', '', $responseStr);

		$responseStr = str_replace('<' . $data['function'] . 'Return xsi:type="soapenc:string" xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/">', '', $responseStr);


		if (!empty($data['addStr'])) {
			foreach ($data['addStr'] as $addStr) {
				$responseStr = str_replace($addStr, '', $responseStr);
			}
		}
		$responseStr = str_replace('<' . $data['function'] . 'Return xsi:type="xsd:string">', '', $responseStr);
		$responseStr = str_replace('</' . $data['function'] . 'Return>', '', $responseStr);
		$responseStr = str_replace('</ns1:' . $data['function'] . 'Response>', '', $responseStr);
		$responseStr = str_replace('</soapenv:Body>', '', $responseStr);
		$responseStr = str_replace('</soapenv:Envelope>', '', $responseStr);
		return $responseStr;
	}

	public function getSessionId()
	{
		try {
			$function = 'login';
			$soapData = $this->soapData(['function' => $function, 'parameters' => $this->loginParameters]);
			$data = $this->sendCurl($soapData);
			$sessionID = $this->clearResponse(['function' => $function, 'addStr' => '', 'data' => $data]);
			Session::put('soapSessionID', $sessionID);

			return $sessionID;
		} catch (\Exception $e) {
			LoggerService::logError(LogChannelsEnum::Soap, 'getSessionId', ['e' => $e]);
			Session::forget('soapSessionID');
			return false;
		}
	}

	public function logOut($soapSessionID = '')
	{
		Session::forget('soapSessionID');
		$function = 'logout';
		if (!empty($soapSessionID)) {
			try {
				$soapData = $this->soapData(['function' => $function, 'parameters' => [['p_strSessionId', 'xsd:string', $soapSessionID]]]);
				$data = $this->sendCurl($soapData);
				return true;
			} catch (\Exception $e) {
				LoggerService::logError(LogChannelsEnum::Soap, 'logOut', ['e' => $e]);
				return false;
			}
		}
		return false;
	}


	private function callIASService($parameters, $jsonResponse = true)
	{
		$soapSessionID = $this->getSessionId();

		if (empty($soapSessionID)) {
			return false;
		}

		$function = 'callIASService';
		array_push($parameters, array('sessionid', 'xsd:string', $soapSessionID));
		$soapData = $this->soapData(['function' => $function, 'parameters' => $parameters]);
		$data = $this->sendCurl($soapData);

		LoggerService::logDebug(LogChannelsEnum::Soap, '[Orders]: callIASService', [
			'request' => $parameters,
			'response' => print_r($data, true)
		]);

		$response = $this->clearResponse(['function' => $function, 'addStr' => ['&lt;?xml version=&quot;1.0&quot; encoding=&quot;UTF-8&quot;?&gt;'], 'data' => $data]);
		$soapSessionID = $this->logOut($soapSessionID);
		if (!$jsonResponse) {
			return $response;
		} else {
			if ($response == "NULL") {
				return null;
			}

			$responseDecode = trim(htmlspecialchars_decode($response));
			$dom = new DOMDocument();
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$dom->loadXML($responseDecode);
			$responseRead = $dom->saveXML($dom->documentElement);
			$rows = simplexml_load_string($responseRead);
			$json = json_decode(json_encode($rows));
			return $json;
		}
	}



	public function submitOrder(ProductVariation $variation, User $user, User $servicePoint, Order $order, User $salesPoint = null)
	{
		$orderNo = $order->order_no;
		$invoiceUserNo = "WM" . date('Ym') . str_pad($user->id, 7, "0", STR_PAD_LEFT);
		$servicePoint = $servicePoint->erp_user_id;
		$productStockCode = $variation->product->stock_code;
		$productName = $variation->product->currentTranslation->product_name;
		$productVariantKey = $variation->variant_key;
		$productColorCode = $variation->color->color_code;
		$totalAmount = $variation->price / $variation->getOtvRatio();
		$chasisNo = $order->chasis_no;
		$orderDate = $order->created_at->format('Y-m-d');
		$customerName = $user->isCompany() ? $user->company_name : $user->site_user_name;
		$customerSurname = $user->isCompany() ? '' : $user->site_user_surname;
		$customerAddress = $user->address;
		$customerDistrict = $user->district->erp_district_name; // Uppercase, do we need to Capitalize it?
		$customerCity = $user->district->city->erp_city_name;
		$customerPhone = $user->phone;
		$customerEmail = $user->email;
		$customerNationalId = $user->national_id;
		$customerTaxId = $user->tax_id;
		$customerTaxOffice = $user->tax_office;
		$paymentType = $order->payment_type === 'S' ? 'S' : 'NULL';
		$salesPoint = $salesPoint ? $salesPoint->erp_user_id : null;
		$erpPrefix = isset($order->erp_prefix) ? $order->erp_prefix : 'SY';

		$data = [
			'orderNo' => $orderNo,
			'invoiceUserNo' => $invoiceUserNo,
			'servicePoint' => $servicePoint,
			'productStockCode' => $productStockCode,
			'productName' => $productName,
			'productVariantKey' => $productVariantKey,
			'productColorCode' => $productColorCode,
			'totalAmount' => $totalAmount,
			'chasisNo' => $chasisNo,
			'orderDate' => $orderDate,
			'customerName' => $customerName,
			'customerSurname' => $customerSurname,
			'customerAddress' => $customerAddress,
			'customerDistrict' => $customerDistrict,
			'customerCity' => $customerCity,
			'customerPhone' => $customerPhone,
			'customerEmail' => $customerEmail,
			'customerNationalId' => $customerNationalId,
			'customerTaxId' => $customerTaxId,
			'customerTaxOffice' => $customerTaxOffice,
			'paymentType' => $paymentType,
			'salesPoint' => $salesPoint,
			'erpPrefix' => $erpPrefix
		];

		return $this->sendOrder($data);
	}

	public function sendOrder(array $data)
	{
		$delim = ',';
		if (!empty($data)) {
			$orderNo = $data['orderNo']; # orders.order_no ex: WS23120000076 "WS", year 2023, month 12 and orders.id=76
			$invoiceUserNo = $data['invoiceUserNo']; # users.user_no where users.id=orders.invoice_user_id ex: WM23120000157 "WM", year 2023, month 12, users.id=157
			$servicePoint = $data['servicePoint']; # Service Point to Deliver users.erp_user_id where users.id=orders.delivery_user_id ex: MY003016 (ERP user ID)

			$quantity = 1;
			$belgeyeEkle = '<BELGEYEEKLE/>';
			$kaynakBelge = '<KAYNAKBELGE>10</KAYNAKBELGE>';
			$isAlani = '<ISALANI>*</ISALANI>';

			/*
			Encoded inventory list <INVLIST><ROW>...</ROW></INVLIST>
				<MIKTAR>1</MIKTAR>: Quantity always 1
				<BELGEYEEKLE/>: Always empty
				<KAYNAKBELGE>10</KAYNAKBELGE>: Always 10
				<STOKKODU></STOKKODU>: Product Stock Code products.stock_code where product_variations.id=orders.product_variation_id and products.id=product_variations.product_id ex: TMB8402NDF10
				<URUNADI></URUNADI>: Product Name product_translations.product_name where product_variations.id=orders.product_variation_id and product_translations.product_id=product_variations.product_id ex: Bajaj Pulsar NS 200
				<BIRIMFIYAT></BIRIMFIYAT>: Product Unit Price which will always be the Total Amount orders.total_amount in number_format($totalAmount, 2, '.', '') ex: 98125.00
				<URUNFIYAT></URUNFIYAT>: Product Price, same as Product Unit Price ex: 98125.00
				<ISALANI>*</ISALANI>: Always '*'
				<VARIANTKEY></VARIANTKEY>: Product Variant Key product_variations.variant_key where product_variations.id=orders.product_variation_id and products.id=product_variations.product_id ex: PULSARNS200E5
				<PFSECENEK></PFSECENEK>: Product Variant Color Code colors.color_code where product_variations.id=orders.product_variation_id and colors.id=product_variations.color_id ex: #OZE5##RE020#
				<SASI></SASI>: Product chasis number orders.chasis_no this will be empty for normal orders but in case the order is submitted by a sales point and the product belongs to the inventory of the sales point

			*/
			$productStockCode = $data['productStockCode']; #
			$productName = $data['productName'];
			$productVariantKey = $data['productVariantKey'];
			$productColorCode = $data['productColorCode'];
			$chasisNo = $data['chasisNo'];

			$totalAmount = number_format($data['totalAmount'], 2, '.', '');

			$invListTxt = '<INVLIST><ROW><MIKTAR>' . $quantity . '</MIKTAR>' . $belgeyeEkle . $kaynakBelge . '<STOKKODU>' . $productStockCode . '</STOKKODU><URUNADI>' . $productName . '</URUNADI><BIRIMFIYAT>' . $totalAmount . '</BIRIMFIYAT><URUNFIYAT>' . $totalAmount . '</URUNFIYAT>' . $isAlani . '<VARIANTKEY>' . $productVariantKey . '</VARIANTKEY><PFSECENEK>' . $productColorCode . '</PFSECENEK><SASI>' . $chasisNo . '</SASI></ROW></INVLIST>';
			$invList = str_replace('<', '&lt;', str_replace('>', '&gt;', $invListTxt));

			$afterInvList = ',2,1,NULL';
			$zero = 0;
			$one = 1;
			$two = 2;

			$null = 'NULL';
			$orderDate = $data['orderDate']; # Order date: date('Y-m-d')

			$customerName = $data['customerName']; # Customer Name users.site_user_name where users.id=orders.invoice_user_id
			$customerSurname = $data['customerSurname']; # Customer Surname users.site_user_surname where users.id=orders.invoice_user_id
			$customerAddress = str_replace(',', ' ', $data['customerAddress']); # Customer Address users.address where users.id=orders.invoice_user_id
			$customerDistrict = $data['customerDistrict']; # Customer District Name district_translations.district_name where users.id=orders.invoice_user_id and district_translations.id=users.district_id
			$customerCity = $data['customerCity']; # Customer City Name city_translations.city_name where users.id=orders.invoice_user_id and districts.id=users.district_id and city_translations.city_id=districts.city_id
			$customerPhone = $data['customerPhone']; # Customer Phone users.phone where users.id=orders.invoice_user_id
			$customerEmail = $data['customerEmail']; # Customer Email users.email where users.id=orders.invoice_user_id
			$customerNationalId = $data['customerNationalId']; # Customer National ID users.national_id where users.id=orders.invoice_user_id
			$customerTaxId = !empty($data['customerTaxId']) ? $data['customerTaxId'] : $null; # Customer Tax ID users.tax_id where users.id=orders.invoice_user_id if empty NULL
			$customerTaxOffice = !empty($data['customerTaxOffice']) ? $data['customerTaxOffice'] : $null; # Customer Tax Office users.tax_office where users.id=orders.invoice_user_id
			$paymentType = !empty($data['paymentType']) ? $data['paymentType'] : 'H'; # 20240126 - OE - IT requires default payment type as H
			# Payment Type orders.payment_type H: Electronic Funds Transfer (EFT), K: Credit Card, S: Payment Plan
			$salesPoint = !empty($data['salesPoint']) ? $data['salesPoint'] : ''; # Sales Point users.user_no IF the orders.user_id is a sales point THEN where users.id=orders.user_id ELSE empty

			$erpPrefix = !empty($data['erpPrefix']) ? $data['erpPrefix'] : 'SY'; # 20240126 - OE - this will change when consigned shop stocks are added later on
			$args = $orderNo . $delim . '01' . $delim . $erpPrefix . $delim . $invoiceUserNo . $delim . $servicePoint . $delim . $invList . $afterInvList . $delim . $orderDate . $delim . $null . $delim . $zero . $delim . $customerName . $delim . $customerSurname . $delim . $customerAddress . $delim . $customerDistrict . $delim . $customerCity . $delim . $customerPhone . $delim . $customerEmail . $delim . $one . $delim . $customerName . $delim . $customerSurname . $delim . $customerAddress . $delim . $customerDistrict . $delim . $customerCity . $delim . $customerPhone . $delim . $customerNationalId . $delim . $customerTaxOffice . $delim . $customerTaxId . $delim . $paymentType . $delim . $salesPoint . $delim . $null;

			$parameters = [
				['serviceid', 'xsd:string', 'SATISBELGESIOLUSTUR3DSW'],
				['args', 'xsd:string', $args],
				['returntype', 'xsd:string', 'STRING'],
				['permanent', 'xsd:boolean', 'false']
			];

			$orderResponse = $this->callIASService($parameters, false);

			if ($orderResponse != "0") {
				$responseParts = explode('&amp;', $orderResponse);
				$erpPrefix = $responseParts[1]; # orders.erp_prefix
				$erpOrderId = $responseParts[2]; # orders.erp_order_id
				$responseAmount = $responseParts[3]; # order amount in response
				$responseErpUserID = $responseParts[4]; # ERP response for users.erp_user_id by orders.invoice_user_id

				return [
					'erpPrefix' => $erpPrefix,
					'erpOrderId' => $erpOrderId,
					'amount' => $responseAmount,
					'erpUserId' => $responseErpUserID,
					'orderResponse' => $orderResponse,
					'parameters' => $parameters
				];
			} else {
				LoggerService::logError(LogChannelsEnum::Soap, 'sendOrder: Failed order', ['params' => $parameters]);

				return null;
			}
		}
	}

	// Function to check if the customer is currently registered into Findeks (https://www.findeks.com/). The Turkish National ID is sent and a 0/1 response is received from the KKBUYELIKKONTROL web service
	public function checkFindeksRegistration($nationalId)
	{
		$response = false;
		if (!empty($nationalId)) {
			$parameters = [
				['serviceid', 'xsd:string', 'KKBUYELIKKONTROL'],
				['args', 'xsd:string', $nationalId],
				['returntype', 'xsd:string', 'STRING'],
				['permanent', 'xsd:boolean', 'false']
			];
			$response = $this->callIASService($parameters, false);
		}
		return $response;
	}



	// function to send a payment information to the web service
	public function sendPayment($erpOrderId, $paymentRefNo, $erpUserId, $paymentAmount, $paymentType, $bankName = '', $numberOfInstallments = 1, $erpOrderType = 'SY', $findeksApplicationFeeTxt = '')
	{
		$delim = ',';
		$erpFunction = 'ODEMEBILGILERI';

		$delim = ',';

		// 20240220 - OE - $findeksApplicationFeeTxt will read "FINDEKS KAPORA"

		$asterisk = "*";
		$currencyCode = "TL";
		$args = $asterisk . $delim . $erpUserId . $delim . $numberOfInstallments . $delim . $paymentAmount . $delim . $currencyCode . $delim . $delim . $delim . $delim . $delim . $delim . $findeksApplicationFeeTxt . $delim . $erpOrderType . $delim . $erpOrderId . $delim . $delim . $delim . $delim . $delim . $delim . $delim . $paymentType . $delim . $bankName . $delim . $delim . $paymentRefNo . $delim . $delim . $delim;

		try {
			$parameters = [
				['serviceid', 'xsd:string', $erpFunction],
				['args', 'xsd:string', $args],
				['returntype', 'xsd:string', 'STRING'],
				['permanent', 'xsd:boolean', 'false']
			];

			$orderPaymentResponse = $this->callIASService($parameters, false);

			return ['response' => (bool) $orderPaymentResponse];
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

	/* Sales agreements section ---------------- */

	/*
	downPayments Usage: Retrieve the down payments for the product with the stock code
	params: products.stock_code

	Sample Request: downPayments/TMB8412NDG
	Sample Response: {"ROW":[{"PESINATTUTARI":"39900.0"},{"PESINATTUTARI":"40150.0"},{"PESINATTUTARI":"40400.0"},{"PESINATTUTARI":"40650.0"},{"PESINATTUTARI":"40900.0"},{"PESINATTUTARI":"41150.0"},{"PESINATTUTARI":"41400.0"},{"PESINATTUTARI":"41650.0"},{"PESINATTUTARI":"41900.0"},{"PESINATTUTARI":"42150.0"},{"PESINATTUTARI":"42400.0"},{"PESINATTUTARI":"42650.0"},...
	*/

	public function downPayments($stockCode)
	{
		if (empty($stockCode)) {
			return false;
		}

		$parameters = [
			['serviceid', 'xsd:string', 'WEBSHOPSENETPESINATLISTESI'],
			['args', 'xsd:string', $stockCode],
			['returntype', 'xsd:string', 'STRING'],
			['permanent', 'xsd:boolean', 'false']
		];

		$response = $this->callIASService($parameters);

		return $response;
	}

	/*
	installmentOptions Usage: Retrieve the installment months and corresponding monthly payments for the selected product code and the down payment amount 
	params: products.stock_code,selectedDownPaymentAmount
	Sample Request: installmentOptions/TMB8412NDG,40150.0
	Sample Response: {"ROW":[{"SENETSAYISI":"1","SENETTUTARI":"123955.0"},{"SENETSAYISI":"2","SENETTUTARI":"64390.0"},{"SENETSAYISI":"3","SENETTUTARI":"44535.0"},{"SENETSAYISI":"4","SENETTUTARI":"34610.0"},{"SENETSAYISI":"5","SENETTUTARI":"28655.0"},{"SENETSAYISI":"6","SENETTUTARI":"24685.0"},{"SENETSAYISI":"7","SENETTUTARI":"21850.0"},{"SENETSAYISI":"8","SENETTUTARI":"19725.0"},...
	*/

	public function installmentOptions($params)
	{
		if (empty($params)) {
			return false;
		}
		$parameters = [
			['serviceid', 'xsd:string', 'WEBSHOPSENETTAKSITLISTESI'],
			['args', 'xsd:string', $params],
			['returntype', 'xsd:string', 'STRING'],
			['permanent', 'xsd:boolean', 'false']
		];
		$response = $this->callIASService($parameters);
		return $response;
	}

	/*
	initiateFindeksRequest Usage: Initiate a Findeks request with the selected parameters. Returns a Findeks ID if successful otherwise some Turkish text explaning the error. 
	params: users.national_id,users.site_user_name,users.site_user_surname,users.phone,products.stock_code,colors.erp_color_code,products.product_name,product_variations.price*products.vat_ratio,selectedDownPaymentAmount,selectedInstallmentAmount,selectedNumberOfInstallments,users.date_of_birth
	Sample Request: initiateFindeksRequest/11..64,OZER ERKE,5304108044,TMB8412NDG,#OZE5##RE521#,Bajaj Dominar D 250,149150.0,40150.0,44535.0,3,1..-0.-13
	Sample Response: 226374836
	*/

	public function initiateFindeksRequest($nationalId, $fullname, $phone, $stockCode, $colorCode, $productName, $priceWithVat, $downPayment, $installmentAmount, $numberOfInstallments, $dateOfBirth, $erpUserId = '')
	{
		$data = [$nationalId, $fullname, $phone, $stockCode, $colorCode, $productName, $priceWithVat, $downPayment, $installmentAmount, $numberOfInstallments, $dateOfBirth, $erpUserId];

		$params = join(',', $data);

		$parameters = [
			['serviceid', 'xsd:string', 'WEBSHOPKKBTALEPBASLAT'],
			['args', 'xsd:string', $params],
			['returntype', 'xsd:string', 'STRING'],
			['permanent', 'xsd:boolean', 'false']
		];

		$response = $this->callIASService($parameters, false);

		return $response;
	}

	/*
	findeksRequestStatus Usage: Retrieve the status of a Findeks Request ID. Response: 5 => success, Response: 3 => The customer has received a PIN code with SMS, else => error
	params: sales_agreements.findeks_request_id
	Sample Request: findeksRequestStatus/226374836
	Sample Response: 5
	*/

	public function findeksRequestStatus($requestNo)
	{
		if (empty($requestNo)) {
			return false;
		}

		$parameters = [
			['serviceid', 'xsd:string', 'KKBTALEPDURUM'],
			['args', 'xsd:string', $requestNo],
			['returntype', 'xsd:string', 'STRING'],
			['permanent', 'xsd:boolean', 'false']
		];

		$response = $this->callIASService($parameters, false);

		return $response;
	}

	/*
	findeksPinConfirmation Usage: In case the findeksRequestStatus has returned 3, we should request the PIN code sent by FINDEKS
	params: sales_agreements.findeks_request_id,date('Y',users.date_of_birth),otpPinEntered

	Sample Request: findeksPinConfirmation/226374836,1968,1234
	Sample Response: 0/1
	*/

	public function findeksPinConfirmation($requestId, $userBirthYear, $pin)
	{
		$data = [$requestId, $userBirthYear, $pin];

		if (in_array(null, $data) || in_array('', $data)) {
			return false;
		}

		$params = join(',', $data);

		$parameters = [
			['serviceid', 'xsd:string', 'KKBTALEPONAYPIN'],
			['args', 'xsd:string', $params],
			['returntype', 'xsd:string', 'STRING'],
			['permanent', 'xsd:boolean', 'false']
		];

		$response = $this->callIASService($parameters, false);

		return $response;
	}

	/*
	findeksRequestResult Usage: In case the findeksRequestStatus has returned 5, the result of the request is retrieved. 1 => success, else => error
	params: sales_agreements.findeks_request_id

	Sample Request: findeksRequestResult/226374836
	Sample Response: 1
	*/

	public function findeksRequestResult($requestNo)
	{
		if (empty($requestNo)) {
			return false;
		}

		$parameters = [
			['serviceid', 'xsd:string', 'KKBTALEPSONUC'],
			['args', 'xsd:string', $requestNo],
			['returntype', 'xsd:string', 'STRING'],
			['permanent', 'xsd:boolean', 'false']
		];

		$response = $this->callIASService($parameters, false);

		return $response;
	}

	/*
	findeksMergeOrder Usage: Used to merge the successfull FINDEKS Request ID with the ERP order ID we have created before entering the sales agreement process.  Response 1 => success, else => error
	params: users.national_id, sales_agreements.findeks_request_id, order.erp_prefix, orders.erp_order_id

	Sample Request: findeksMergeOrder/226374836,SY,00078577
	Sample Response: 1 
	*/


	public function findeksMergeOrder($nationalId, $findeksRequestId, $erpPrefix, $erpOrderId)
	{
		$data = [$nationalId, $findeksRequestId, $erpPrefix, $erpOrderId];

		if (in_array(null, $data) || in_array('', $data)) {
			return false;
		}

		$params = join(',', $data);

		$parameters = [
			['serviceid', 'xsd:string', 'WEBSHOPUPDATESALESDOCS'],
			['args', 'xsd:string', $params],
			['returntype', 'xsd:string', 'STRING'],
			['permanent', 'xsd:boolean', 'false']
		];

		$response = $this->callIASService($parameters, false);

		return $response;
	}

	/*
	salesAgreementDocs Usage: Retrieve the sales agreement document link for the customer to download at the Thank You! Page. Response => link text, else => error
	params: sales_agreements.findeks_request_id, salesAgreenentType (0: Standard, 1:E-sales agreement)
  	The response PDF file has the same name but the content is different depending on the sales agreement type

	Sample Request: 228837138,1
	Sample Response: https://iframe.kuralkan.com.tr/Multimedia/Dokumanlar/Senet/5131959227454837_findeks.pdf
	*/

	public function salesAgreementDocs($findeksRequestId, $salesAgreementType = 0)
	{
		if (empty($findeksRequestId)) {
			return false;
		}

		$salesAgreementParam = $findeksRequestId;

		if ($salesAgreementType == 1) {
			$salesAgreementParam = "$findeksRequestId,1";
		}

		$parameters = [
			['serviceid', 'xsd:string', 'SENETLINK'],
			['args', 'xsd:string', $salesAgreementParam],
			['returntype', 'xsd:string', 'STRING'],
			['permanent', 'xsd:boolean', 'false']
		];

		$response = $this->callIASService($parameters, false);

		return $response;
	}


	/* To be used for retreieving data of the legal registration by the chasis no
	Each time the user visits the detail page, unless it's registered we will check the getArtes()
	UNLESS the orders.temprorary_licence_doc_link is empty. When ERP fills in this field, we will not check it anymore
	*/
	public function getArtes($chasisNo)
	{
		if (empty($chasisNo)) {
			return false;
		}

		$parameters = [
			['serviceid', 'xsd:string', 'WEBSHOPGETARTES'],
			['args', 'xsd:string', $chasisNo],
			['returntype', 'xsd:string', 'STRING'],
			['permanent', 'xsd:boolean', 'false']
		];

		$response = $this->callIASService($parameters);

		return $response;
	}

	// To be used to send the legal registration form information. Also, requires data from the getArtes() and users table
	// expected response 1 => Success, 0 => Failure
	public function setArtes($artesType, $artesNo, User $user, $formData)
	{
		try {
			$docType = $artesType;
			$docnum = $artesNo;
			$city = $user->getCity()->erp_city_name;
			$district = $user->district->erp_district_name;
			$phone = $user->phone;
			$insuranceNumber = $formData["insurance_number"] ?? '';

			if ($user->company == 'Y') {
				$invoiceType = 2;
				$taxNo = $user->tax_id;
				$taxOffice = $user->tax_office;

				$neighbourhood = $formData["neighbourhood"] ?? '';
				$street = $formData["street"] ?? '';
				$buildingNo = $formData["buildingNo"] ?? '';
				$flatNo = $formData["flatNo"] ?? '';

				$individualNationalId = '';
				$individualIdType = '';
				$individualIdSerial = '';
				$individualIdNo = '';
				$individualNewIdSerialNo = '';
				$individualTempDocNo = '';

				$representationStatus = 1;
				$representationIdType = $formData["id_type"] ?? '';
				$representationNameSurname = $formData["authorized_name"] ?? '';
				$representationNationalId = $formData["authorized_national_id"] ?? '';
				$representationIdSerial = $formData["id_serial"] ?? '';
				$representationIdNo = $formData["id_no"] ?? '';
				$representationNewIdSerialNo = $formData["new_id_serial_no"] ?? '';
				$representationTempDocNo = $formData["temp_doc_no"] ?? '';
				$representationProxyType = $formData["representation_type"] ?? '';
				$representationPlaceOfRetrieval = $formData["place_of_retrieval"] ?? '';
				$representationDocumentDate = $formData["document_date"] ?? '';
				$representationExists = 1;
				$representationEndDate = $formData["end_date"] ?? '';
				$representationType = $formData["representation_type"] ?? '';
				$title = $user->company_name;
			} else {
				$invoiceType = 1;
				$taxNo = '';
				$taxOffice = '';
				$neighbourhood = '';
				$street = '';
				$buildingNo = '';
				$flatNo = '';

				$individualNationalId = $user->national_id;
				$individualIdType = $formData["id_type"] ?? '';
				$individualIdSerial = $formData["id_serial"] ?? '';
				$individualIdNo = $formData["id_no"] ?? '';
				$individualNewIdSerialNo = $formData["new_id_serial_no"] ?? '';
				$individualTempDocNo = $formData["temp_doc_no"] ?? '';

				$representationStatus = 2;
				$representationIdType = '';
				$representationNameSurname = '';
				$representationNationalId = '';
				$representationIdSerial = '';
				$representationIdNo = '';
				$representationNewIdSerialNo = '';
				$representationTempDocNo = '';
				$representationProxyType = '';
				$representationPlaceOfRetrieval = '';
				$representationDocumentDate = '';
				$representationExists = 2;
				$representationEndDate = '';
				$representationType = 0;
				$title = $user->full_name;
			}

			$documentList = $formData["delimitedDocumentList"] ?? '';
			$usageType = $formData["usage_type"] ?? '';

			$numberOfDocuments = $formData["number_of_documents"] ?? '';

			$delim = ',';

			$setArtesParams = $docType . $delim
				. $docnum . $delim
				. $invoiceType . $delim
				. $taxNo . $delim
				. $taxOffice . $delim
				. $city . $delim
				. $district . $delim
				. $neighbourhood . $delim
				. $street . $delim
				. $buildingNo . $delim
				. $flatNo . $delim
				. $individualNationalId . $delim
				. $individualIdType . $delim
				. $individualIdSerial . $delim
				. $individualIdNo . $delim
				. $individualNewIdSerialNo . $delim
				. $individualTempDocNo . $delim
				. '' . $delim
				. '' . $delim
				. $phone . $delim
				. $phone . $delim
				. $representationStatus . $delim
				. $representationIdType . $delim
				. $representationNameSurname . $delim
				. $representationNationalId . $delim
				. $representationIdSerial . $delim
				. $representationIdNo . $delim
				. $representationNewIdSerialNo . $delim
				. $representationTempDocNo . $delim
				. $representationProxyType . $delim
				. $representationPlaceOfRetrieval . $delim
				. $representationDocumentDate . $delim
				. $representationExists . $delim
				. $representationEndDate . $delim
				. $representationType . $delim
				. $insuranceNumber . $delim
				. '' . $delim
				. '' . $delim
				. '' . $delim
				. $documentList . $delim
				. $usageType . $delim
				. $title . $delim
				. $numberOfDocuments;

			$parameters = [
				['serviceid', 'xsd:string', 'WEBSHOPSETARTES'],
				['args', 'xsd:string', $setArtesParams],
				['returntype', 'xsd:string', 'STRING'],
				['permanent', 'xsd:boolean', 'false']
			];

			$response = $this->callIASService($parameters, false);

			return ['params' => $setArtesParams, 'response' => $response];
		} catch (Exception $e) {
			LoggerService::logError(LogChannelsEnum::ErpArtes, 'setArtes: Failed Soap', ['e' => $e, 'params' => $setArtesParams]);

			return ['params' => null, 'response' => "0"];
		}
	}

	public function ebondsForOrder($erpPrefix, $erpOrderId)
	{
		if (empty($erpPrefix) || empty($erpOrderId)) {
			return false;
		}

		$params = "$erpPrefix,$erpOrderId";

		$parameters = [
			['serviceid', 'xsd:string', 'ESENETBILGILERI'],
			['args', 'xsd:string', $params],
			['returntype', 'xsd:string', 'STRING'],
			['permanent', 'xsd:boolean', 'false']
		];

		$response = $this->callIASService($parameters);

		return $response;
	}

	public function getAllEbonds()
	{
		$parameters = [
			['serviceid', 'xsd:string', 'ESENETBILGILERI'],
			['args', 'xsd:string', ''],
			['returntype', 'xsd:string', 'STRING'],
			['permanent', 'xsd:boolean', 'false']
		];

		$response = $this->callIASService($parameters);

		return $response;
	}
}
