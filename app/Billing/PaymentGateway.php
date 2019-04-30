<?php


namespace App\Billing;


interface PaymentGateway
{
    public function charge($amount, $token, $designationAccountId);

    public function getValidTestToken();


    public function newChargesDuring($callback);
}