<?php

use App\Helpers\BanglaHelper;

if (! function_exists('bn_num')) {
    function bn_num(int|float|string|null $number, ?string $locale = null, int $decimals = 0): string
    {
        return BanglaHelper::formatNumber($number, $locale, $decimals);
    }
}

if (! function_exists('bn_curr')) {
    function bn_curr(int|float|string|null $amount, ?string $locale = null, int $decimals = 0, ?string $symbol = null): string
    {
        return BanglaHelper::formatCurrency($amount, $locale, $decimals, $symbol);
    }
}

if (! function_exists('seller_trans')) {
    function seller_trans(string $key, array $replace = [], ?string $locale = null): string
    {
        return BanglaHelper::trans($key, $replace, $locale);
    }
}

if (! function_exists('bn_time')) {
    function bn_time(DateTimeInterface|string|null $time, ?string $locale = null, string $format = 'h:i A'): string
    {
        return BanglaHelper::formatTime($time, $locale, $format);
    }
}

if (! function_exists('bn_date')) {
    function bn_date(DateTimeInterface|string|null $date, ?string $locale = null, string $format = 'D, d M Y'): string
    {
        return BanglaHelper::formatDate($date, $locale, $format);
    }
}
