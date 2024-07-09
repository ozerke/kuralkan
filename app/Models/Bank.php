<?php

namespace App\Models;

use App\Services\CreditCardGateway;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bank extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['color'];

    const COLORS = [
        'Akbank' => '#FEA601',
        'T. İş Bankası' => '#E5017A',
        'Finansbank' => '#000F78',
        'Vakıf Bank' => '#63418C',
        'T. Halk Bankası' => '#16DDFF',
        'Ziraat Bankası' => '#ED1E24',
        'Garanti' => '#67CF5B',
        'Türkiye Finans' => '#1DC5B5',
        'Kuveyt Türk' => '#036647',
    ];

    public function installmentRates(): HasMany
    {
        return $this->hasMany(InstallmentRate::class, 'bank_id', 'id');
    }

    public function getColorAttribute()
    {
        return self::COLORS[$this->erp_bank_name] ?? [];
    }

    public function bankAccount(): HasOne
    {
        return $this->hasOne(BankAccount::class, 'bank_id', 'id');
    }

    public function ccGroups(): HasMany
    {
        return $this->hasMany(CcGroup::class, 'bank_id', 'id');
    }

    public function ccGroup(): ?CcGroup
    {
        return $this->ccGroups()->first();
    }

    private static function calculatePrices($vatPrice, $rate, $installments)
    {
        $totalPrice = round($vatPrice * (1 + $rate));
        $pricePerInstallment = round($totalPrice / $installments);

        return [
            'total' => $totalPrice,
            'perOne' => $pricePerInstallment
        ];
    }

    public static function calculateTotalPriceWithRates($amountToBePaid, $installments, $cardNumber, $campaignCode = null)
    {
        $ccSixDigits = strlen($cardNumber) > 6 ? substr($cardNumber, 0, 6) : $cardNumber;

        $gateway = new CreditCardGateway();
        $installmentsList = $gateway->bulutInstallments($amountToBePaid, $ccSixDigits, $campaignCode);

        $installmentsList = collect($gateway->findInstallments($installmentsList));

        $installment = $installmentsList->where('months', $installments)->first();

        $installment = str_replace('.', '', $installment);
        $installment = str_replace(',', '.', $installment);

        if ($installment) {
            return (float)$installment['total'];
        }

        return (float)$amountToBePaid;
    }

    public static function getInstallmentRatesForVariation(ProductVariation $variation)
    {
        $banks = self::with(['installmentRates', 'ccGroups'])->whereHas('installmentRates')->get();

        $banks = $banks->map(function ($bank) use ($variation) {
            $installments = $bank->installmentRates->map(function ($installment) use ($variation) {
                $prices = self::calculatePrices($variation->vat_price, $installment->rate, $installment->number_of_months);

                return [
                    'months' => $installment->number_of_months,
                    'total' => number_format($prices['total'], 2, ',', '.'),
                    'perOne' => number_format($prices['perOne'], 2, ',', '.'),
                ];
            });

            return [
                'image' => $bank->ccGroups->first()->logo,
                'color' => $bank->color,
                'installments' => $installments->sortBy('months')->toArray(),
                'minInstallment' => $installments->min('perOne')
            ];
        });

        $lowestInstallment = $banks->min('minInstallment');

        return ['banks' => $banks, 'lowestInstallment' => $lowestInstallment];
    }

    public function getInstallmentRatesByPrice(float $price)
    {
        if (count($this->installmentRates) < 1) return null;

        $installments = $this->installmentRates->map(function ($installment) use ($price) {
            $prices = self::calculatePrices($price, $installment->rate, $installment->number_of_months);

            return [
                'months' => $installment->number_of_months,
                'total' => number_format($prices['total'], 2, ',', '.'),
                'perOne' => number_format($prices['perOne'], 2, ',', '.'),
            ];
        });

        return $installments->sortBy('months')->toArray();
    }

    public static function getDefaultInstallment($price): array
    {
        $data = [
            [
                'months' => 1,
                'total' => number_format($price, 2, ',', '.'),
                'perOne' => number_format($price, 2, ',', '.'),
            ]
        ];

        return $data;
    }
}
