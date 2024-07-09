<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    const KEYS = [
        'min_payment_percentage' => 'min_payment_percentage',
        'max_payments_count' => 'max_payments_count',
        'sa_application_fee' => 'sa_application_fee',
        'home_title' => 'home_title',
        'home_description' => 'home_description',
        'home_keywords' => 'home_keywords',
        'e_sales_agreement_en' => 'e_sales_agreement_en',
        'e_sales_agreement_tr' => 'e_sales_agreement_tr',
        'sales_agreement_explanation_tr' => 'sales_agreement_explanation_tr',
        'sales_agreement_explanation_en' => 'sales_agreement_explanation_en',
    ];

    public static function getMinPartialPercent()
    {
        $min = self::where('key', self::KEYS['min_payment_percentage'])->first();

        return $min ? (int)$min->value : 100;
    }

    public static function getMaxPaymentsCount()
    {
        $max = self::where('key', self::KEYS['max_payments_count'])->first();

        return $max ? (int)$max->value : 30;
    }

    public static function getApplicationFee()
    {
        $fee = self::where('key', self::KEYS['sa_application_fee'])->first();

        return $fee;
    }

    public static function getHomeTitle()
    {
        $title = self::where('key', self::KEYS['home_title'])->first();

        return $title;
    }

    public static function getHomeDescription()
    {
        $desc = self::where('key', self::KEYS['home_description'])->first();

        return $desc;
    }

    public static function getHomeKeywords()
    {
        $keywords = self::where('key', self::KEYS['home_keywords'])->first();

        return $keywords;
    }

    public static function getESalesAgreement($lang = 'tr')
    {
        if ($lang == 'tr') {
            $agreement = self::where('key', self::KEYS['e_sales_agreement_tr'])->first();
        } else {
            $agreement = self::where('key', self::KEYS['e_sales_agreement_en'])->first();
        }

        return $agreement;
    }

    public static function getSalesAgreementExplanation($lang = 'tr')
    {
        if ($lang == 'tr') {
            $explanation = self::where('key', self::KEYS['sales_agreement_explanation_tr'])->first();
        } else {
            $explanation = self::where('key', self::KEYS['sales_agreement_explanation_en'])->first();
        }

        return $explanation;
    }
}
