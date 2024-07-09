<?php

namespace App\Services;

class SMSTemplateParser
{
    public static function bankPayment($fullname, $orderNo, $productNameColor, $bankName, $paymentAmount)
    {
        return view('templates.sms.bankPayment', compact('fullname', 'orderNo', 'productNameColor', 'bankName', 'paymentAmount'))->render();
    }

    public static function bankPaymentDetails($orderNo, $selectedBank, $selectedBranch, $selectedBranchNo, $selectedAccountNo, $selectedIban)
    {
        return view('templates.sms.bankPaymentDetails', compact('orderNo', 'selectedBank', 'selectedBranch', 'selectedBranchNo', 'selectedAccountNo', 'selectedIban'))->render();
    }

    public static function bankPaymentReceived($fullname, $orderNo, $productNameColor, $bankName, $paymentAmount)
    {
        return view('templates.sms.bankPaymentReceived', compact('fullname', 'orderNo', 'productNameColor', 'bankName', 'paymentAmount'))->render();
    }

    public static function cardPartial($fullname, $orderNo, $productNameColor, $paymentAmount)
    {
        return view('templates.sms.cardPartial', compact('fullname', 'orderNo', 'productNameColor', 'paymentAmount'))->render();
    }

    public static function cardFullPayment($fullname, $orderNo, $productNameColor, $paymentAmount)
    {
        return view('templates.sms.cardFullPayment', compact('fullname', 'orderNo', 'productNameColor', 'paymentAmount'))->render();
    }

    public static function orderConfirmed($fullname, $orderNo)
    {
        return view('templates.sms.orderConfirmed', compact('fullname', 'orderNo'))->render();
    }

    public static function orderSupplying($fullname, $orderNo, $deliveryDate)
    {
        return view('templates.sms.orderSupplying', compact('fullname', 'orderNo', 'deliveryDate'))->render();
    }

    public static function orderShipped($fullname, $orderNo)
    {
        return view('templates.sms.orderShipped', compact('fullname', 'orderNo'))->render();
    }

    public static function orderInvoiceReady($fullname, $orderNo)
    {
        return view('templates.sms.orderInvoiceReady', compact('fullname', 'orderNo'))->render();
    }

    public static function orderDelivered($fullname, $orderNo, $deliveryPoint)
    {
        return view('templates.sms.orderDelivered', compact('fullname', 'orderNo', 'deliveryPoint'))->render();
    }

    public static function orderProcessed($fullname, $orderNo)
    {
        return view('templates.sms.orderProcessed', compact('fullname', 'orderNo'))->render();
    }

    // OTP Templates

    public static function quickRegister($fullname, $verificationCode)
    {
        return view('templates.sms.quickRegister', compact('fullname', 'verificationCode'))->render();
    }

    public static function userRegisterByShop($fullname, $shopName, $verificationCode)
    {
        return view('templates.sms.userRegisterByShop', compact('fullname', 'shopName', 'verificationCode'))->render();
    }

    public static function existingUserVerification($fullname, $shopName, $verificationCode)
    {
        return view('templates.sms.existingUserVerification', compact('fullname', 'shopName', 'verificationCode'))->render();
    }

    public static function userLogins($userEmail, $userPassword)
    {
        return view('templates.sms.userLogins', compact('userEmail', 'userPassword'))->render();
    }

    public static function defaultVerificationCode($fullname, $verificationCode)
    {
        return view('templates.sms.defaultVerificationCode', compact('fullname', 'verificationCode'))->render();
    }

    // Ebonds

    public static function ebondsCreated($fullname)
    {
        return view('templates.sms.ebonds.created', compact('fullname'))->render();
    }

    public static function beforeDueDate($fullname, $eBondNo, $eBondDueDate, $eBondAmount)
    {
        return view('templates.sms.ebonds.beforeDueDate', compact('fullname', 'eBondNo', 'eBondDueDate', 'eBondAmount'))->render();
    }

    public static function onDueDate($fullname, $eBondNo, $eBondDueDate, $eBondAmount)
    {
        return view('templates.sms.ebonds.onDueDate', compact('fullname', 'eBondNo', 'eBondDueDate', 'eBondAmount'))->render();
    }

    public static function afterDueDate($fullname, $eBondNo, $eBondDueDate, $eBondAmount)
    {
        return view('templates.sms.ebonds.afterDueDate', compact('fullname', 'eBondNo', 'eBondDueDate', 'eBondAmount'))->render();
    }

    public static function ebondPenalty($fullname, $eBondNo, $eBondDueDate, $eBondAmount)
    {
        return view('templates.sms.ebonds.ebondPenalty', compact('fullname', 'eBondNo', 'eBondDueDate', 'eBondAmount'))->render();
    }
}
