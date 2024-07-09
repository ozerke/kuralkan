<?php

namespace App\Models;

use App\Traits\HasOrders;
use Carbon\Carbon;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\HasApiTokens;
use Maize\Searchable\HasSearch;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, CanResetPassword, HasSearch, HasOrders;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    const ROLES = [
        'admin' => 'admin',
        'customer' => 'customer',
        'shop' => 'shop',
        'service' => 'service',
        'shop-service' => 'shop-service'
    ];

    public function getSearchableAttributes(): array
    {
        return ['email' => 5, 'site_user_name' => 5, 'site_user_surname' => 5, 'erp_user_id' => 5, 'erp_email', 'company_name', 'fullname' => 5];
    }

    public function district(): HasOne
    {
        return $this->hasOne(District::class, 'id', 'district_id');
    }

    public function registeredBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'registered_by');
    }

    public function shopStocks(): HasMany
    {
        return $this->hasMany(ShopStock::class, 'user_id', 'id');
    }

    public function ebonds(): HasMany
    {
        return $this->hasMany(Ebond::class, 'user_id', 'id')->orderBy('due_date');
    }

    public function createdOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'invoice_user_id', 'id');
    }

    public function unpaidOrder(): ?Order
    {
        return $this->orders()->whereDoesntHave('orderPayments')->first();
    }

    public function orderPayments(): HasMany
    {
        return $this->hasMany(OrderPayment::class, 'user_id', 'id');
    }

    public function consignedProducts(): HasMany
    {
        return $this->hasMany(ConsignedProduct::class, 'user_id', 'id');
    }

    public function getCity()
    {
        if ($this->district) {
            return $this->district->city;
        }

        return null;
    }

    public function isCompany()
    {
        return $this->company === 'Y';
    }

    public function getInvoiceName()
    {
        if ($this->isCompany()) {
            return $this->company_name;
        }

        return $this->full_name;
    }

    public function getTaxOrNationalId()
    {
        if ($this->isCompany()) {
            return $this->tax_id;
        }

        return $this->national_id;
    }

    public function scopeFilteredRoles($query, $roles)
    {
        if ($roles == 'admin') {
            return $query->role(self::ROLES['admin']);
        }

        if ($roles == 'shop-service') {

            return $query->role([self::ROLES['shop-service']]);
        }

        if ($roles == 'shop') {
            return $query->role(self::ROLES['shop']);
        }

        if ($roles == 'service') {
            return $query->role(self::ROLES['service']);
        }

        if ($roles == 'customer') {
            return $query->role(self::ROLES['customer']);
        }

        return $query;
    }

    public function scopeShops($query)
    {
        return $query->role([self::ROLES['shop-service'], self::ROLES['shop']]);
    }

    public function scopeServices($query)
    {
        return $query->role([self::ROLES['shop-service'], self::ROLES['service']]);
    }

    public function scopeShopsServices($query)
    {
        return $query->role([self::ROLES['shop-service'], self::ROLES['service'], self::ROLES['shop']])->whereNotNull('erp_user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('user_active', 'Y');
    }

    public function getFullNameAttribute()
    {
        return $this->site_user_name . ' ' . $this->site_user_surname;
    }

    public function getShopDetails()
    {
        return $this->erp_user_id . ' - ' . $this->erp_user_name;
    }

    public function getErpName()
    {
        return $this->erp_user_id;
        if ($this->hasExactRoles(self::ROLES['customer'])) {
            return $this->erp_user_id;
        }

        return $this->erp_user_id . ' / ' . $this->erp_user_name;
    }

    public function getUserType()
    {
        if ($this->hasExactRoles(self::ROLES['admin'])) {
            return __('app.admin');
        }

        if ($this->hasExactRoles(self::ROLES['customer'])) {
            return __('app.customer');
        }

        if ($this->hasExactRoles([self::ROLES['shop-service']])) {
            return __('app.shop-service');
        }

        if ($this->hasExactRoles([self::ROLES['shop']])) {
            return __('app.shop');
        }

        if ($this->hasExactRoles([self::ROLES['service']])) {
            return __('app.service');
        }

        return 'Unrecognizable role';
    }

    public function isShopOrService()
    {
        return $this->hasAnyRole([self::ROLES['shop'], self::ROLES['service'], self::ROLES['shop-service']]);
    }

    public function isAdmin()
    {
        return $this->hasExactRoles(self::ROLES['admin']);
    }

    public function getMapsUrl()
    {
        if (!$this->latitude || !$this->longitude) {
            if ($this->address) {
                return "https://www.google.com/maps/embed/v1/place?key=AIzaSyDS7I86-ZIHwqlylNOKQfftkprdym6Uuss&q=" . $this->address;
            } else {
                return "https://www.google.com/maps/embed/v1/place?key=AIzaSyDS7I86-ZIHwqlylNOKQfftkprdym6Uuss&q=" . $this->district->currentTranslation->district_name . ' ' . $this->district->city->currentTranslation->city_name;
            }
        }

        return "https://www.google.com/maps/embed/v1/place?key=AIzaSyDS7I86-ZIHwqlylNOKQfftkprdym6Uuss&q=" . $this->latitude . ',' . $this->longitude;
    }

    public function getInvoiceInformation($forOtp = false)
    {
        $district = $this->district()->with('city.country')->first();

        $generalInformation = [
            'name' => $this->site_user_name,
            'surname' => $this->site_user_surname,
            'phone' => $this->phone,
            'email' => $this->email,
            'company' => $this->company,
            'address' => $this->address,
            'country' => $forOtp ? optional($district->city)->country->id : optional($district->city)->country,
            'city' => $forOtp ? optional($district)->city->id : optional($district)->city,
            'district' => $forOtp ? $district->id : $district,
            'postal_code' => $this->postal_code,
        ];

        $birthdate = Carbon::parse($this->date_of_birth);

        $individualInformation = [
            'national_id' => $this->national_id,
            'date_of_birth' => $birthdate->format('d-m-Y'),
            'birth_day' => $birthdate->format('d'),
            'birth_month' => $birthdate->format('m'),
            'birth_year' => $birthdate->format('Y'),
        ];

        $companyInformation = [
            'company_name' => $this->company_name,
            'tax_office' => $this->tax_office,
            'tax_id' => $this->tax_id,
        ];

        $information = array_merge($generalInformation, $companyInformation, $individualInformation);

        if ($this->isShopOrService()) {
            $information = array_fill_keys(array_keys($information), null);
        }

        if ($forOtp) {
            unset($information['email']);
            unset($information['phone']);
        }

        return $information;
    }

    public function updateUserNo($userNo = null)
    {
        if ($userNo) {
            $this->update([
                'user_no' => $userNo
            ]);
        } else {
            $this->update([
                'user_no' => "EK" . date('Ym') . str_pad($this->id, 7, 0, STR_PAD_LEFT)
            ]);
        }
    }

    public function isFindeksVerified()
    {
        return (bool) $this->is_findeks_verified;
    }

    public function sendPasswordResetNotification($token)
    {
        Mail::send('templates.mail.users.reset-password', ['fullname' => $this->full_name, 'resetUrl' => route('password.reset', ['token' => $token, 'email' => $this->email])], function ($message) {
            $message->subject('Şifre değiştirme talebi');
            $message->to($this->email);
        });
    }

    public function addConsignedProductForShop($productVariationId, $chasisNo, $inStock = false): bool
    {
        if (!$productVariationId || !$chasisNo) {
            return false;
        }

        $exists = $this->consignedProducts()->where([
            ['chasis_no', $chasisNo],
            ['product_variation_id', $productVariationId],
        ])->first();

        if (!$exists) {
            if ($inStock) {
                $this->consignedProducts()->create([
                    'chasis_no' => $chasisNo,
                    'product_variation_id' => $productVariationId,
                    'in_stock' => $inStock
                ]);
            }
        } else {
            if (!$inStock) {
                $exists->delete();
            } else {
                $exists->update([
                    'in_stock' => $inStock
                ]);
            }
        }

        return true;
    }
}
