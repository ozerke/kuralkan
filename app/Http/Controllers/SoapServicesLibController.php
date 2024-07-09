<?php
/*
SoapSendOrderController purpose: Get an order to the web service of Kuralkan
Always login -> send order -> logout
*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use DOMDocument;
use Exception;
use Illuminate\Support\Facades\Session;


class SoapServicesLibController extends Controller
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

	// Response in JSON format. Function to get product list, if the products.stock_code field is sent as a parameter as stockCode within the array, only that product's variation list is retrieved.

	public function productList(array $data)
	{
		$args = !empty($data['stockCode']) ? $data['stockCode'] : '';
		$parameters = [
			['serviceid', 'xsd:string', 'WEBSHOPGETMATERIAL'],
			['args', 'xsd:string', $args],
			['returntype', 'xsd:string', 'STRING'],
			['permanent', 'xsd:boolean', 'false']
		];
		$response = $this->callIASService($parameters);
		return $response;
	}


	// Response in JSON format. Function to get product specifications list if the products.stock_code field is sent as a parameter as stockCode within the array, only that product's variation list is retrieved.

	public function productSpecsList(array $data)
	{
		$args = !empty($data['stockCode']) ? $data['stockCode'] : '';
		$parameters = [
			['serviceid', 'xsd:string', 'WEBSHOPGETMATSPEC'],
			['args', 'xsd:string', $args],
			['returntype', 'xsd:string', 'STRING'],
			['permanent', 'xsd:boolean', 'false']
		];
		$response = $this->callIASService($parameters);
		return $response;
	}


	// Response in JSON format. Function to get sales points list, if the users.erp_user_id field is sent as a parameter as erpUserId within the array, only that sales point info is retrieved

	public function salesPointsList(array $data)
	{
		$args = !empty($data['erpUserId']) ? $data['erpUserId'] : '';
		$parameters = [
			['serviceid', 'xsd:string', 'WEBSHOPGETBRANCH'],
			['args', 'xsd:string', $args],
			['returntype', 'xsd:string', 'STRING'],
			['permanent', 'xsd:boolean', 'false']
		];
		$response = $this->callIASService($parameters);
		return $response;
	}



	// Response in JSON format. Function to get sales point's total product stocks list, if the users.erp_user_id field is sent as a parameter as erpUserId and/or if the products.stock_code field is sent as a parameter as stockCode within the array, only that sales point's and/or product's stock list is retrieved.

	public function salesPointsTotalStocksList(array $data)
	{
		$args = '';
		if (!empty($data['stockCode'])) {
			$args = !empty($data['erpUserId']) ? $data['erpUserId'] . ',' . $data['stockCode'] : ',' . $data['stockCode'];
		} else {
			$args = !empty($data['erpUserId']) ? $data['erpUserId'] : '';
		}

		$parameters = [
			['serviceid', 'xsd:string', 'WEBSHOPGETBRANCHSTOCK'],
			['args', 'xsd:string', $args],
			['returntype', 'xsd:string', 'STRING'],
			['permanent', 'xsd:boolean', 'false']
		];
		$response = $this->callIASService($parameters);
		return $response;
	}


	// Response in JSON format. Function to get customers list, if users.erp_user_id field is sent as a parameter as erpUserId within the array, only that customer info is retrieved

	public function customerList(array $data)
	{
		$args = !empty($data['erpUserId']) ? $data['erpUserId'] : '';
		$parameters = [
			['serviceid', 'xsd:string', 'WEBSHOPGETCUSTOMER'],
			['args', 'xsd:string', $args],
			['returntype', 'xsd:string', 'STRING'],
			['permanent', 'xsd:boolean', 'false']
		];
		$response = $this->callIASService($parameters);
		return $response;
	}


	// Function to check if the customer is currently registered into Findeks (https://www.findeks.com/). The Turkish National ID is sent and a 0/1 response is received from the KKBUYELIKKONTROL web service

	public function checkFindeksRegistration(array $data)
	{
		$response = false;
		if (!empty($data['userNationalID'])) {
			$parameters = [
				['serviceid', 'xsd:string', 'KKBUYELIKKONTROL'],
				['args', 'xsd:string', $data['userNationalID']],
				['returntype', 'xsd:string', 'STRING'],
				['permanent', 'xsd:boolean', 'false']
			];
			$response = $this->callIASService($parameters, false);
		}
		return $response;
	}

	// Response in JSON format. Function to get the credit card interest rates based on banks. No parameters are accepted. This values should be used when calculating the rates tables in the product detail's "Payment Options" tab.

	public function ccInstallmentList()
	{
		$parameters = [
			['serviceid', 'xsd:string', 'WEBSHOPBANKINSLIST'],
			['args', 'xsd:string', ''],
			['returntype', 'xsd:string', 'STRING'],
			['permanent', 'xsd:boolean', 'false']
		];
		$response = $this->callIASService($parameters);
		return $response;
	}

	// PAYMENT PLANS first step. Response in JSON format. Function to get the down payment values list for the requested product stock code products.stock_code

	public function productDownPaymentList($data = array())
	{
		if (empty($data['stockCode'])) {
			return false;
		}
		$args = $data['stockCode'];
		$parameters = [
			['serviceid', 'xsd:string', 'WEBSHOPSENETPESINATLISTESI'],
			['args', 'xsd:string', $args],
			['returntype', 'xsd:string', 'STRING'],
			['permanent', 'xsd:boolean', 'false']
		];
		$response = $this->callIASService($parameters);
		return $response;
	}

	// PAYMENT PLANS second step. Response in JSON format. Function to get the installment numbers and amounts list. Requires the comma deilmited stockCode (products.stock_code) and selected down payment value.

	public function productInstallmentList($data = array())
	{
		if (empty($data['stockPayment'])) {
			return false;
		}
		$args = $data['stockPayment'];
		$parameters = [
			['serviceid', 'xsd:string', 'WEBSHOPSENETTAKSITLISTESI'],
			['args', 'xsd:string', $args],
			['returntype', 'xsd:string', 'STRING'],
			['permanent', 'xsd:boolean', 'false']
		];
		$response = $this->callIASService($parameters);
		return $response;
	}


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
				$this->sendCurl($soapData);
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

		LoggerService::logDebug(LogChannelsEnum::Soap, '[Services] callIASService', [
			'request' => $parameters,
			'response' => print_r($data, true),
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

	// List the information with webservice SENETBILGILERI on demand, per request
	public function checkBondsOfCustomer($nationalId)
	{
		if (empty($nationalId)) {
			throw new Exception("National ID is missing");
		}

		$args = $nationalId;

		$parameters = [
			['serviceid', 'xsd:string', 'SENETBILGILERI'],
			['args', 'xsd:string', $args],
			['returntype', 'xsd:string', 'STRING'],
			['permanent', 'xsd:boolean', 'false']
		];

		$response = $this->callIASService($parameters);

		return $response;
	}
}
// --------- End Class
