<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\CacheServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Exception;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    protected $cacheService;

    public function __construct(CacheServiceInterface $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $configs = collect(Configuration::all());

        $min = $configs->where('key', Configuration::KEYS['min_payment_percentage'])->first();
        $max = $configs->where('key', Configuration::KEYS['max_payments_count'])->first();
        $fee = $configs->where('key', Configuration::KEYS['sa_application_fee'])->first();
        $homeTitle = $configs->where('key', Configuration::KEYS['home_title'])->first();
        $homeDesc = $configs->where('key', Configuration::KEYS['home_description'])->first();
        $homeKeywords = $configs->where('key', Configuration::KEYS['home_keywords'])->first();
        $eSalesAgreementEn = $configs->where('key', Configuration::KEYS['e_sales_agreement_en'])->first();
        $eSalesAgreementTr = $configs->where('key', Configuration::KEYS['e_sales_agreement_tr'])->first();
        $salesAgreementExplanationEn = $configs->where('key', Configuration::KEYS['sales_agreement_explanation_en'])->first();
        $salesAgreementExplanationTr = $configs->where('key', Configuration::KEYS['sales_agreement_explanation_tr'])->first();

        $data = [
            'min' => $min ? (int) $min->value : null,
            'max' => $max ? (int) $max->value : null,
            'fee' => $fee ? (int) $fee->value : null,
            'homeTitle' => $homeTitle ? $homeTitle->value : null,
            'homeDesc' => $homeDesc ? $homeDesc->value : null,
            'homeKeywords' => $homeKeywords ? $homeKeywords->value : null,
            'eSalesAgreementEn' => $eSalesAgreementEn ? $eSalesAgreementEn->value : null,
            'eSalesAgreementTr' => $eSalesAgreementTr ? $eSalesAgreementTr->value : null,
            'salesAgreementExplanationEn' => $salesAgreementExplanationEn ? $salesAgreementExplanationEn->value : null,
            'salesAgreementExplanationTr' => $salesAgreementExplanationTr ? $salesAgreementExplanationTr->value : null,
        ];

        return view('admin.configuration.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $min = Configuration::firstOrCreate(
                ['key' => Configuration::KEYS['min_payment_percentage']],
                ['value' => (int) $request->input('min_payment_percentage')]
            );

            $min->update([
                'value' => (int) $request->input('min_payment_percentage')
            ]);

            $max = Configuration::firstOrCreate(
                ['key' => Configuration::KEYS['max_payments_count']],
                ['value' => (int) $request->input('max_payments_count')]
            );

            $max->update([
                'value' => (int) $request->input('max_payments_count')
            ]);

            $fee = Configuration::firstOrCreate(
                ['key' => Configuration::KEYS['sa_application_fee']],
                ['value' => (int) $request->input('sa_application_fee')]
            );

            $fee->update([
                'value' => (int) $request->input('sa_application_fee')
            ]);

            $homeTitle = Configuration::firstOrCreate(
                ['key' => Configuration::KEYS['home_title']],
                ['value' => $request->input('home_title')]
            );

            $homeTitle->update([
                'value' => $request->input('home_title')
            ]);

            $homeDesc = Configuration::firstOrCreate(
                ['key' => Configuration::KEYS['home_description']],
                ['value' => $request->input('home_description')]
            );

            $homeDesc->update([
                'value' => $request->input('home_description')
            ]);

            $homeKeywords = Configuration::firstOrCreate(
                ['key' => Configuration::KEYS['home_keywords']],
                ['value' => $request->input('home_keywords')]
            );

            $homeKeywords->update([
                'value' => $request->input('home_keywords')
            ]);

            $eSalesAgreementEn = Configuration::firstOrCreate(
                ['key' => Configuration::KEYS['e_sales_agreement_en']],
                ['value' => $request->input('e_sales_agreement_en')]
            );

            $eSalesAgreementEn->update([
                'value' => $request->input('e_sales_agreement_en')
            ]);

            $eSalesAgreementTr = Configuration::firstOrCreate(
                ['key' => Configuration::KEYS['e_sales_agreement_tr']],
                ['value' => $request->input('e_sales_agreement_tr')]
            );

            $eSalesAgreementTr->update([
                'value' => $request->input('e_sales_agreement_tr')
            ]);

            $salesAgreementExplanationEn = Configuration::firstOrCreate(
                ['key' => Configuration::KEYS['sales_agreement_explanation_en']],
                ['value' => $request->input('sales_agreement_explanation_en')]
            );

            $salesAgreementExplanationEn->update([
                'value' => $request->input('sales_agreement_explanation_en')
            ]);

            $salesAgreementExplanationTr = Configuration::firstOrCreate(
                ['key' => Configuration::KEYS['sales_agreement_explanation_tr']],
                ['value' => $request->input('sales_agreement_explanation_tr')]
            );

            $salesAgreementExplanationTr->update([
                'value' => $request->input('sales_agreement_explanation_tr')
            ]);

            return back()->with('success', 'Saved successfully');
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Store configurations update', ['e' => $e]);

            return back()->with('error', 'An error occured');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function flushCache(Request $request)
    {
        $this->cacheService->flush();
        return back()->with('success', 'Cache cleared successfully');
    }
}
