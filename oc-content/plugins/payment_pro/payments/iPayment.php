<?php

interface iPayment
{
    public static function button($products, $extra = null);
    public static function recurringButton($subscription, $extra = null);
    public static function processPayment();
}