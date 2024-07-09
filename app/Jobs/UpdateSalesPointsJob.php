<?php

namespace App\Jobs;

use App\Models\City;
use App\Models\Language;
use App\Models\User;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateSalesPointsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $usersCollection;
    /**
     * Create a new job instance.
     */
    public function __construct($usersCollection)
    {
        $this->onQueue('erpjobs');

        $this->usersCollection = $usersCollection;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();

        try {
            LoggerService::logInfo(LogChannelsEnum::UpdateSalesPoints, 'Update Sales Points', ['users' => $this->usersCollection->toArray()]);

            $shopServices = User::shopsServices()->get('erp_user_id')->pluck('erp_user_id');

            $foundErpIds = collect();

            foreach ($this->usersCollection as $erpUser) {

                try {
                    $city = City::where('erp_city_name', $erpUser['city'])->first();

                    if (!$city) {
                        $city = City::create([
                            'country_id' => 1,
                            'slug' => strtolower($erpUser['city']),
                            'erp_city_name' => $erpUser['city']
                        ]);

                        $city->translations()->create([
                            'lang_id' => Language::AVAILABLE['tr'],
                            'city_name' => $erpUser['city']
                        ]);
                    }

                    $district = $city->districts()->where('erp_district_name', $erpUser['district'])->first();

                    if (!$district) {
                        $district = $city->districts()->create([
                            'slug' => strtolower($erpUser['city']) . '-' . strtolower($erpUser['district']),
                            'erp_district_name' => $erpUser['district']
                        ]);

                        $district->translations()->create([
                            'lang_id' => Language::AVAILABLE['tr'],
                            'district_name' => $erpUser['district']
                        ]);
                    }

                    $existingUser = User::where('erp_user_id', $erpUser['erp_user_id'])->first();
                    $email = str_contains($erpUser['email'], '@') ? $erpUser['email'] : $erpUser['erp_user_id'] . '@ekuralkan.com';
                    $phone = $erpUser['phone'] ?? '';

                    $email = str_contains($erpUser['erp_user_id'], '-') ? $erpUser['erp_user_id'] . '@ekuralkan.com' : $email;

                    if ($existingUser) {
                        $newEmail = $existingUser->email !== $email;
                        $isUnique = true;

                        if ($newEmail) {
                            $emailTaken = User::where('email', $email)->first();

                            if ($emailTaken != null) {
                                $isUnique = false;
                            }
                        }

                        $existingUser->update([
                            'site_user_name' => $erpUser['erp_user_name'],
                            'erp_user_name' => $erpUser['erp_user_name'],
                            'site_user_surname' => '',
                            'district_id' => $district->id,
                            'phone' => $phone,
                            'latitude' => $erpUser['latitude'],
                            'longitude' => $erpUser['longitude'],
                            'user_active' => 'Y',
                            'erp_user_id' => $erpUser['erp_user_id'],
                            'erp_email' => $erpUser['email'],
                            'shop' => $erpUser['shop'],
                            'service' => $erpUser['service'],
                            'address' => $erpUser['address'],
                            'email' => $newEmail && $isUnique ? $email : $existingUser->email
                        ]);

                        if ($erpUser['shop'] && $erpUser['service']) {
                            $existingUser->syncRoles([User::ROLES['shop-service']]);
                        }

                        if (!$erpUser['shop'] && $erpUser['service']) {
                            $existingUser->syncRoles([User::ROLES['service']]);
                        }

                        if ($erpUser['shop'] && !$erpUser['service']) {
                            $existingUser->syncRoles([User::ROLES['shop']]);
                        }

                        $foundErpIds->push($erpUser['erp_user_id']);
                    } else {
                        $existingEmailUser = User::where([
                            ['email', $email],
                            ['erp_user_id', '!=', $erpUser["erp_user_id"]]
                        ])->first();

                        if ($existingEmailUser) {
                            $email = $erpUser['erp_user_id'] . '@ekuralkan.com';
                        }

                        $user = User::create([
                            'email' => $email,
                            'site_user_name' => $erpUser['erp_user_name'],
                            'fullname' => $erpUser['erp_user_name'],
                            'erp_user_name' => $erpUser['erp_user_name'],
                            'site_user_surname' => '',
                            'district_id' => $district->id,
                            'phone' => $phone,
                            'latitude' => $erpUser['latitude'],
                            'longitude' => $erpUser['longitude'],
                            'user_active' => 'Y',
                            'erp_user_id' => $erpUser['erp_user_id'],
                            'erp_email' => $erpUser['email'],
                            'date_of_birth' => "1900-01-01",
                            'password' => $erpUser['erp_user_id'],
                            'shop' => $erpUser['shop'],
                            'service' => $erpUser['service'],
                            'address' => $erpUser['address']
                        ]);

                        if ($erpUser['shop'] && $erpUser['service']) {
                            $user->assignRole(User::ROLES['shop-service']);
                        }

                        if (!$erpUser['shop'] && $erpUser['service']) {
                            $user->assignRole(User::ROLES['service']);
                        }

                        if ($erpUser['shop'] && !$erpUser['service']) {
                            $user->assignRole(User::ROLES['shop']);
                        }
                    }
                } catch (Exception $e) {
                    LoggerService::logError(LogChannelsEnum::UpdateSalesPoints, 'Error in handler (User loop)', ['e' => $e, 'erpUser' => $erpUser]);
                }
            }

            $shopsServices = collect($shopServices);

            $missingIds = $shopsServices->diff($foundErpIds);

            User::whereIn('erp_user_id', $missingIds)->update(['user_active' => 'N']);

            LoggerService::logSuccess(LogChannelsEnum::UpdateSalesPoints, 'Update Sales Points');

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            LoggerService::logError(LogChannelsEnum::UpdateSalesPoints, 'Error in handler', ['e' => $e]);

            $this->fail($e);
        }
    }
}
