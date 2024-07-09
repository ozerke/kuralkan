<?php

namespace App\Models;

use App\Contracts\CacheServiceInterface;
use App\Http\Controllers\Orders\SoapSendOrderController;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use App\Utils\SoapUtils;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Maize\Searchable\HasSearch;

class Order extends Model
{
    use HasFactory, HasSearch;

    protected $guarded = ['id'];

    protected $appends = ['is_by_shop', 'sa_status'];

    public function getSearchableAttributes(): array
    {
        return [
            'order_no',
            'total_amount',
            'chasis_no',
            'motor_no',
            'erp_order_id',
            'ordering_user_fullname',
            'invoice_user_fullname',
            'delivery_user_fullname',
            'product_name'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function invoiceUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invoice_user_id', 'id');
    }

    public function deliveryUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivery_user_id', 'id');
    }

    public function productVariation(): HasOne
    {
        return $this->hasOne(ProductVariation::class, 'id', 'product_variation_id');
    }

    public function legalRegistration(): HasOne
    {
        return $this->hasOne(LegalRegistration::class, 'order_id', 'id');
    }

    public function orderPayments(): HasMany
    {
        return $this->hasMany(OrderPayment::class, 'order_id', 'id')->whereNull('e_bond_no');
    }

    public function orderBondPayments(): HasMany
    {
        return $this->hasMany(OrderPayment::class, 'order_id', 'id')->whereNotNull('e_bond_no');
    }

    public function orderPaymentsWithoutFee(): HasMany
    {
        return $this->hasMany(OrderPayment::class, 'order_id', 'id')->where('is_fee_payment', false)->whereNull('e_bond_no');
    }

    public function orderPaymentsWithDeleted(): HasMany
    {
        return $this->hasMany(OrderPayment::class, 'order_id', 'id')->withTrashed();
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class, 'order_id', 'id');
    }

    public function latestStatusHistory(): HasOne
    {
        return $this->hasOne(OrderStatusHistory::class, 'order_id', 'id')->latestOfMany('created_at');
    }

    public function salesAgreement(): HasOne
    {
        return $this->hasOne(SalesAgreement::class, 'order_id', 'id');
    }

    public function orderCampaign(): HasOne
    {
        return $this->hasOne(OrderCampaign::class, 'order_id', 'id');
    }

    public function getDeliveryInformation()
    {
        return $this->deliveryUser->full_name . " - " . $this->deliveryUser->district->currentTranslation->district_name . " - " . $this->deliveryUser->district->city->currentTranslation->city_name;
    }

    public function isOrderedByShop()
    {
        return $this->user->isShopOrService();
    }

    public function isSalesAgreementOrder()
    {
        return $this->payment_type === 'S';
    }

    public function assignOrderNo($return = false)
    {
        $orderNo = "WS" . $this->created_at->format('Ym') . str_pad($this->id, 7, "0", STR_PAD_LEFT);

        if ($return) {
            return $orderNo;
        }

        $this->update([
            'order_no' => $orderNo
        ]);
    }

    public function getOrderPaymentsState($approvedOnly = false)
    {
        if ($this->isCampaignOrder()) {
            $totalAmount = (float)$this->total_amount;
            $downPayment = $this->orderCampaign->down_payment;

            $paidAmount = $this->orderPayments()->where('approved_by_erp', 'Y')->failedStatus(false)->sum('payment_amount');
            $paymentCount = $this->orderPayments()->failedStatus(false)->count();
            $paymentLimit = Configuration::getMaxPaymentsCount();

            $remainingAmount = $totalAmount - $paidAmount;
            $campaignRemainingAmount = $totalAmount - $downPayment;

            return [
                'is_paid' => $remainingAmount == 0,
                'remaining_amount' => $remainingAmount,
                'paid_amount' => $paidAmount,
                'payment_count' => $paymentCount . "/" . $paymentLimit,
                'total_amount' => $totalAmount,
                'campaign_remaining_amount' => $campaignRemainingAmount
            ];
        }

        if ($this->isSalesAgreementOrder() && !empty($this->salesAgreement->down_payment_amount)) {
            $totalAmount = (float)$this->salesAgreement->down_payment_amount;
            $paidAmount = $approvedOnly ? $this->orderPayments()->where('approved_by_erp', 'Y')->failedStatus(false)->applicationFee(false)->sum('payment_amount') : $this->orderPayments()->failedStatus(false)->applicationFee(false)->sum('payment_amount');
            $paymentCount = $this->orderPayments()->failedStatus(false)->applicationFee(false)->count();
            $paymentLimit = Configuration::getMaxPaymentsCount();

            $remainingAmount = $totalAmount - $paidAmount;

            return [
                'is_paid' => $remainingAmount == 0,
                'remaining_amount' => $remainingAmount,
                'paid_amount' => $paidAmount,
                'payment_count' => $paymentCount . "/" . $paymentLimit,
                'total_amount' => $totalAmount
            ];
        }

        $totalAmount = (float)$this->total_amount;
        $paidAmount = $approvedOnly ? $this->orderPayments()->where('approved_by_erp', 'Y')->failedStatus(false)->sum('payment_amount') : $this->orderPayments()->failedStatus(false)->sum('payment_amount');
        $paymentCount = $this->orderPayments()->failedStatus(false)->count();
        $paymentLimit = Configuration::getMaxPaymentsCount();

        $remainingAmount = $totalAmount - $paidAmount;

        return [
            'is_paid' => $remainingAmount == 0,
            'remaining_amount' => $remainingAmount,
            'paid_amount' => $paidAmount,
            'payment_count' => $paymentCount . "/" . $paymentLimit,
            'total_amount' => $totalAmount
        ];
    }

    public function getLatestPaymentAttribute()
    {
        return $this->orderPayments()->latest()->first();
    }

    public function getLatestStatusAttribute()
    {
        return $this->statusHistory()->latest()->first();
    }

    public function getIsByShopAttribute()
    {
        return $this->user->isShopOrService();
    }

    public function hasBankTransferPayments()
    {
        return $this->orderPayments->contains('payment_type', 'H');
    }

    public function isCancelled()
    {
        return $this->latest_order_status_id == 6;
    }

    public function updateOrderStatus($status)
    {
        if (!in_array($status, array_keys(OrderStatus::ASSIGNABLE_STATUSES))) {
            throw new Exception('Order status not found: ' . $status);
        }

        $this->statusHistory()->create([
            'order_status_id' => OrderStatus::ASSIGNABLE_STATUSES[$status],
            'user_id' => $this->invoice_user_id
        ]);
    }

    public function getOrderPaymentType($translate = false)
    {
        if ($this->isSalesAgreementOrder()) {
            if ($translate) {
                if ($this->salesAgreement) {
                    if ($this->salesAgreement->is_new_agreement) {
                        return $this->payment_type ? __('web.' . OrderPayment::PAYMENT_TYPES[$this->payment_type]) : __('web.pending');
                    } else {
                        return $this->payment_type ? "*" . __('web.' . OrderPayment::PAYMENT_TYPES[$this->payment_type]) : __('web.pending');
                    }
                }

                return $this->payment_type ? __('web.' . OrderPayment::PAYMENT_TYPES[$this->payment_type]) : __('web.pending');
            }

            return $this->payment_type ?? '-';
        }

        $payment = $this->orderPayments->first();

        if (!$payment) {
            if ($translate) {
                return $this->payment_type ?  __('web.' . OrderPayment::PAYMENT_TYPES[$this->payment_type]) : __('web.pending');
            }

            return $this->payment_type ?? '-';
        }

        if ($translate) {
            return __('web.' . OrderPayment::PAYMENT_TYPES[$payment->payment_type]);
        }

        return $payment->payment_type;
    }

    public function hasPaidApplicationFee()
    {
        if (!$this->salesAgreement) return false;

        if (!empty($this->salesAgreement->application_fee_payment_id)) {
            return true;
        }

        return false;
    }

    public function showLegalRegistrationFormForAdmin()
    {
        if ($this->isCancelled()) {
            return false;
        }

        $hasDocs = !empty($this->temprorary_licence_doc_link);
        $hasInvoice = !empty($this->invoice_link);
        $hasChasisNo = !empty($this->chasis_no);

        if ($hasDocs || !$hasInvoice || !$hasChasisNo) {
            return false;
        }

        return true;
    }

    public function showLegalRegistrationForm()
    {
        if ($this->isCancelled()) {
            return false;
        }

        $hasDocs = !empty($this->temprorary_licence_doc_link);
        $hasInvoice = !empty($this->invoice_link);

        if ($hasDocs || !$hasInvoice) {
            return false;
        }

        $existingForm = LegalRegistration::where('order_id', $this->id)->first();

        if (!$existingForm) {
            return true;
        }

        if ($existingForm && $existingForm->approved_by_erp === 'declined') {
            return true;
        } else {
            return false;
        }
    }

    public function pendingLegalForm()
    {
        $existingForm = LegalRegistration::where('order_id', $this->id)->first();

        if (!$existingForm) {
            return false;
        }

        if ($existingForm->approved_by_erp === 'pending') {
            return true;
        } else {
            return false;
        }
    }

    public function approvedLegalForm()
    {
        $existingForm = LegalRegistration::where('order_id', $this->id)->first();

        if (!$existingForm) {
            return false;
        }

        if ($existingForm->approved_by_erp === 'approved') {
            return true;
        } else {
            return false;
        }
    }

    public function checkLegalRegistrationState($onlyResponse = false)
    {
        try {
            if (!empty($this->temprorary_licence_doc_link)) {
                $registrationForm = LegalRegistration::where('order_id', $this->id)->first();

                if ($registrationForm) {
                    $registrationForm->update([
                        'approved_by_erp' => 'approved',
                        'params' => ''
                    ]);
                } else {
                    LegalRegistration::create([
                        'order_id' => $this->id,
                        'params' => '',
                        'approved_by_erp' => 'approved',
                    ]);
                }

                return null;
            }

            if (empty($this->chasis_no)) {
                return null;
            }

            $artesResponse = (new SoapSendOrderController())->getArtes($this->chasis_no);

            if (!$artesResponse) {
                return null;
            }

            $artesResponse = SoapUtils::parseRow($artesResponse, function ($item) {
                return [
                    'type' => is_array($item['BELGETIPI']) ? null : $item['BELGETIPI'],
                    'no' => is_array($item['BELGENO']) ? null : $item['BELGENO'],
                    'otv_status' => is_array($item['OTVDURUM']) ? null : $item['OTVDURUM'],
                    'otv_status_description' => is_array($item['OTVDURUMACIKLAMA']) ? null : $item['OTVDURUMACIKLAMA'],
                    'is_registered'  => is_array($item['ISTESCIL']) ? null : $item['ISTESCIL'],
                    'registration_description'  => is_array($item['TESCILDESC']) ? null : $item['TESCILDESC'],
                ];
            }, true);

            if ($onlyResponse) {
                return $artesResponse;
            }

            if (in_array($artesResponse['is_registered'], ["10", "11", "12"])) {
                $registrationForm = LegalRegistration::where('order_id', $this->id)->first();

                if ($registrationForm) {
                    $registrationForm->update([
                        'approved_by_erp' => 'approved',
                        'params' => ''
                    ]);
                } else {
                    LegalRegistration::create([
                        'order_id' => $this->id,
                        'params' => '',
                        'approved_by_erp' => 'approved',
                    ]);
                }
            } else if (in_array($artesResponse['is_registered'], ["40"])) {
                $registrationForm = LegalRegistration::where('order_id', $this->id)->first();

                if ($registrationForm) {
                    $registrationForm->update([
                        'approved_by_erp' => 'declined',
                    ]);
                }
            } else {
                return $artesResponse;
            }

            return $artesResponse;
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ErpArtes, 'checkLegalRegistrationState', ['e' => $e]);

            return null;
        }
    }

    public function getArtesFields()
    {
        try {
            $artesResponse = (new SoapSendOrderController())->getArtes($this->chasis_no);

            $artesResponse = SoapUtils::parseRow($artesResponse, function ($item) {
                $usageType = is_array($item['FATURATIPI']) ? null : $item['FATURATIPI'];

                if ($usageType) {
                    $usageType = $usageType === 'BİREYSEL' ? 'individual' : 'commercial';
                }

                $privateIdType = is_array($item['KIMLIKIPI']) ? null : $item['KIMLIKIPI'];

                if ($privateIdType) {
                    switch ($privateIdType) {
                        case '1':
                            $privateIdType = 'id_card';
                            break;
                        case '2':
                            $privateIdType = 'new_id_card';
                            break;
                        case '3':
                            $privateIdType = 'temp_id_doc';
                            break;
                        default:
                            $privateIdType = null;
                            break;
                    }
                }

                $proxyIdType = is_array($item['VKIMLIKIPI']) ? null : $item['VKIMLIKIPI'];

                if ($proxyIdType) {
                    switch ($proxyIdType) {
                        case '1':
                            $proxyIdType = 'id_card';
                            break;
                        case '2':
                            $proxyIdType = 'new_id_card';
                            break;
                        case '3':
                            $proxyIdType = 'temp_id_doc';
                            break;
                        default:
                            $proxyIdType = null;
                            break;
                    }
                }

                return [
                    'usage_type' => $usageType,
                    'insurance_number' => is_array($item['POLICENO']) ? null : $item['POLICENO'],
                    'private' => [
                        'id_type' => $privateIdType,
                        'id_serial' => is_array($item['ESKICUZDANSERI']) ? null : $item['ESKICUZDANSERI'],
                        'id_no' => is_array($item['ESKICUZDANNO']) ? null : $item['ESKICUZDANNO'],
                        'new_id_serial_no' => is_array($item['TCKIMLIKNO']) ? null : $item['TCKIMLIKNO'],
                        'temp_doc_no' => is_array($item['GECICIKIMLIKBELGENO']) ? null : $item['GECICIKIMLIKBELGENO'],
                    ],
                    'commercial' => [
                        'street' => is_array($item['CADDESOKAK']) ? null : $item['CADDESOKAK'],
                        'neighbourhood' => is_array($item['MAHALLE']) ? null : $item['MAHALLE'],
                        'building_no' => is_array($item['DISKAPINO']) ? null : $item['DISKAPINO'],
                        'flat_no' => is_array($item['ICKAPINO']) ? null : $item['ICKAPINO'],

                        'authorized_name' => is_array($item['VADSOYAD']) ? null : $item['VADSOYAD'],
                        'authorized_national_id' => is_array($item['VTCKIMLIKNO']) ? null : $item['VTCKIMLIKNO'],
                        'id_type' => $proxyIdType,
                        'id_serial' => is_array($item['VCUZDANSERI']) ? null : $item['VCUZDANSERI'],
                        'id_no' => is_array($item['VCUZDANNO']) ? null : $item['VCUZDANNO'],
                        'new_id_serial_no' => is_array($item['VKIMLIKSERINO']) ? null : $item['VKIMLIKSERINO'],
                        'temp_doc_no' => is_array($item['VGECICIKIMLIKBELGENO']) ? null : $item['VGECICIKIMLIKBELGENO'],

                        'representation_type' => is_array($item['VEKILTURU']) ? null : $item['VEKILTURU'],
                        'number_of_documents' => is_array($item['EVRAKSAYISI']) ? null : $item['EVRAKSAYISI'],
                        'place_of_retrieval' => is_array($item['ALINDIGIYER']) ? null : $item['ALINDIGIYER'],
                        'document_date' => is_array($item['EVRAKTARIHI']) ? null : $item['EVRAKTARIHI'],
                        'end_date' => is_array($item['VEKALETBITISTARIHI']) ? null : $item['VEKALETBITISTARIHI'],
                    ]
                ];
            }, true);


            return $artesResponse;
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::ErpArtes, 'getArtesFields', ['e' => $e]);

            return $e->getMessage();
        }
    }

    public function getDeliveryDate()
    {
        if (!$this->delivery_date) {
            return null;
        }

        return Carbon::parse($this->delivery_date)->format('d-m-Y');
    }

    public function scopeConfirmed($query, $withConfirmed)
    {
        if ($withConfirmed) {
            return $query->whereNotNull('erp_order_id');
        }

        return $query->whereNull('erp_order_id');
    }

    public function getSaStatusAttribute()
    {
        if (!$this->isSalesAgreementOrder()) return null;

        if (!$this->salesAgreement) {
            return [
                'text' => __('web.pending-status'),
                'color' => 'bg-gray-500'
            ];
        }

        if ($this->salesAgreement->approval_status == 'not_approved') {
            return [
                'text' => __('web.not-approved-status'),
                'color' => 'bg-gray-500'
            ];
        }

        if ($this->salesAgreement->approval_status == 'declined') {
            return [
                'text' => __('web.declined-status'),
                'color' => 'bg-red-500'
            ];
        }

        return [
            'text' => __('web.approved-status'),
            'color' => 'bg-green-500'
        ];
    }

    public function getBondsPayments()
    {
        if (!$this->salesAgreement || !$this->erp_prefix || !$this->erp_order_id) {
            return null;
        }

        if ($this->salesAgreement && !$this->salesAgreement->is_new_agreement) {
            return null;
        }

        $erpBonds = $this->salesAgreement->ebonds;

        if (!$erpBonds) {
            return null;
        }

        return $erpBonds;
    }

    public function checkIfBondExists(Collection $bonds, $bondNo)
    {
        return $bonds->where('e_bond_no', '=', $bondNo)->first();
    }

    public function getBondsPaymentStates(Collection $bonds, $bondNo = null)
    {
        if ($bondNo) {
            $bond = $bonds->where('e_bond_no', $bondNo)->first();

            if (!$bond) return null;

            $payments = $bond->payments;
            $paymentCount = $payments->where('failed', false)->count();
            $paymentLimit = Configuration::getMaxPaymentsCount();

            $bond['payments'] = $payments;
            $bond['paid_amount'] = $bond->bond_amount - $bond->remaining_amount;
            $bond['is_paid'] = $bond->isPaid();
            $bond['payment_count'] = $paymentCount . "/" . $paymentLimit;
            $bond['payment_count_raw'] = $paymentCount;

            return $bond;
        }

        $paymentsStates = $bonds->map(function ($bond) {
            $payments = $bond->payments;
            $paymentCount = $payments->where('failed', false)->count();
            $paymentLimit = Configuration::getMaxPaymentsCount();

            $bond['payments'] = $payments;
            $bond['paid_amount'] = $bond->bond_amount - $bond->remaining_amount;
            $bond['is_paid'] = $bond->isPaid();
            $bond['payment_count'] = $paymentCount . "/" . $paymentLimit;
            $bond['payment_count_raw'] = $paymentCount;

            return $bond;
        });

        return $paymentsStates;
    }

    public function getCampaignsForOrder()
    {
        $productCampaigns = $this->productVariation->product->campaigns;

        return $productCampaigns;
    }

    public function isCampaignOrder()
    {
        return $this->orderCampaign()->exists();
    }

    public function hasPayments()
    {
        return $this->orderPayments()->exists();
    }

    protected static function booted()
    {
        static::created(function ($order) {
            $order->update([
                'order_no' => "WS" . $order->created_at->format('Ym') . str_pad($order->id, 7, "0", STR_PAD_LEFT)
            ]);
        });
    }
}
