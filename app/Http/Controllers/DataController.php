<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\User;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use SoapClient;

class DataController extends Controller
{
    public function getCitiesFromCountry($countryId)
    {
        $country = Country::findOrFail($countryId);

        return response()->json($country->cities()->orderBy('erp_city_name')->get('id'));
    }

    public function getDistrictsFromCity($cityId)
    {
        $city = City::findOrFail($cityId);

        return response()->json($city->districts()->orderBy('erp_district_name')->get('id'));
    }

    public function getDeliveryDistrictsFromCity($cityId)
    {
        $city = City::with('districtsWithServicePoints')->findOrFail($cityId);

        return response()->json($city->districtsWithServicePoints()->get('id'));
    }

    public function getServicePointsFromDistrict($districtId)
    {
        $servicePoints = User::active()->services()->where('district_id', $districtId)->orderBy('site_user_name')->get(['id', 'site_user_name', 'address']);

        return response()->json($servicePoints);
    }

    public function checkNationalId(Request $request)
    {
        $client = new SoapClient("https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx?WSDL");

        try {
            $nationalId = $request->input('nationalId');
            $name = $request->input('name');
            $surname = $request->input('surname');

            $birthYear = preg_match('/\d/', $request->input('birthDate')) ? $request->input('birthDate') : null;
            $birthYear = Carbon::parse($birthYear)->year;

            $email = $request->input('email');

            $result = $client->TCKimlikNoDogrula([
                'TCKimlikNo' => $nationalId,
                'Ad' => $name,
                'Soyad' => $surname,
                'DogumYili' => $birthYear
            ]);

            $user = User::where('national_id', $nationalId)->first();

            if ($user) {
                if ($user->email !== $email) {
                    $parts = explode('@', $user->email);

                    $username = $parts[0];
                    $domain = $parts[1];

                    $maskedUsername = substr($username, 0, 2) . str_repeat('*', strlen($username) - 2);
                    $maskedDomain = substr($domain, 0, 1) . str_repeat('*', strlen($domain) - 5) . substr($domain, -4);

                    $maskedEmail = $maskedUsername . '@' . $maskedDomain;

                    return response()->json(['status' => false, 'emailMismatch' => true, 'message' => __('web.national-id-belongs-to-email', ['email' => $maskedEmail])], 200);
                }
            }

            if ($result->TCKimlikNoDogrulaResult) {
                return response()->json(['status' => true, 'emailMismatch' => false, 'email' => null], 200);
            }

            return response()->json(['status' => false, 'emailMismatch' => false, 'email' => null], 200);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, "CheckNationalId", ['e' => $e]);
            return response()->json(['status' => false, 'emailMismatch' => false, 'email' => null], 200);
        }
    }
}
