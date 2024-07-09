<?php

namespace App\Models;

use App\Traits\SearchTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Maize\Searchable\HasSearch;

class OrderPayment extends Model
{
    use HasFactory, SoftDeletes, SearchTrait, HasSearch;

    protected $guarded = ['id'];

    const PAYMENT_TYPES = [
        'K' => 'credit-card',
        'H' => 'bank-transfer',
        'S' => 'sales-agreement'
    ];

    public function getSearchableAttributes(): array
    {
        return [
            'payment_ref_no',
            'payment_amount',
            'description',
            'user.fullname',
            'order.order_no'
        ];
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function bankAccount(): HasOne
    {
        return $this->hasOne(BankAccount::class, 'id', 'bank_account_id');
    }

    public function ebond(): HasOne
    {
        return $this->hasOne(Ebond::class, 'e_bond_no', 'e_bond_no');
    }

    public static function generatePaymentRefNo($orderNo)
    {
        $date = Carbon::now()->format('dhis');
        return $orderNo . $date;
    }

    public function getPaymentTypeTranslation()
    {
        return __('web.' . self::PAYMENT_TYPES[$this->payment_type]);
    }

    public function scopeApproved($query, $approved = true)
    {
        return $query->where('approved_by_erp', $approved ? 'Y' : 'N');
    }

    public function scopeFailedStatus($query, $failed = false)
    {
        return $query->where('failed', $failed);
    }

    public function scopeApplicationFee($query, $withFee = false)
    {
        return $query->where('is_fee_payment', $withFee);
    }

    public function scopeNotBondPayment($query)
    {
        return $query->whereNull('e_bond_no');
    }

    public function getBankLogo()
    {
        if ($this->payment_type == 'H') {
            return asset('/build/images/banks/main/' . $this->bankAccount->bank->logo);
        }

        return asset('/build/images/banks/' . $this->bankAccount->bank->ccGroup()->logo);
    }

    public function isCreditCardPayment()
    {
        return $this->payment_type === 'K';
    }

    public function getCreditCardPaymentInfo()
    {
        $btResponse = $this->payment_gateway_response;

        if (!$btResponse || $this->failed) {
            return [
                'params' => null,
                'collectedAmount' => null
            ];
        }

        $btResponse = json_decode($btResponse);

        $lastFourDigits = substr($btResponse->CreditCardNumber, -4);

        $params = join(' | ', [$btResponse->OrderRefNo ?: '-', $btResponse->PaymentID ?: '-', $btResponse->AuthCode ?: '-', "**" . $lastFourDigits]);

        $collectedAmount = null;

        if ($btResponse->Installment > 1) {
            $collectedAmount = number_format($btResponse->Amount, 2, ',', '.');
        }

        return [
            'params' => $params,
            'collectedAmount' => $collectedAmount
        ];
    }
}
