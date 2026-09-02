<?php

namespace App\Helpers;

use App\Models\CartSetting;
use Carbon\Carbon;
use DateTimeInterface;

class BanglaHelper
{
    /**
     * Map English numerals to Bangla numerals
     */
    protected static array $enToBnDigits = [
        '0' => '০',
        '1' => '১',
        '2' => '২',
        '3' => '৩',
        '4' => '৪',
        '5' => '৫',
        '6' => '৬',
        '7' => '৭',
        '8' => '৮',
        '9' => '৯',
    ];

    /**
     * Convert any numeric string containing 0-9 to Bangla digits (০-৯)
     */
    public static function toBanglaNumeral(int|float|string|null $input): string
    {
        if ($input === null) {
            return '';
        }

        return strtr((string) $input, self::$enToBnDigits);
    }

    /**
     * Format a number according to active or specified locale (Bangla or English numerals)
     */
    public static function formatNumber(int|float|string|null $number, ?string $locale = null, int $decimals = 0): string
    {
        if ($number === null) {
            return '';
        }

        $loc = $locale ?? app()->getLocale();
        $formatted = number_format((float) $number, $decimals);

        if ($loc === 'bn') {
            return self::toBanglaNumeral($formatted);
        }

        return $formatted;
    }

    /**
     * Format currency amount with symbol and locale-aware numerals
     */
    public static function formatCurrency(int|float|string|null $amount, ?string $locale = null, int $decimals = 0, ?string $symbol = null): string
    {
        $currencySymbol = $symbol ?? CartSetting::currency();
        $formattedNum = self::formatNumber($amount, $locale, $decimals);

        return $currencySymbol.$formattedNum;
    }

    /**
     * Get seller UI translation
     */
    public static function trans(string $key, array $replace = [], ?string $locale = null): string
    {
        $loc = $locale ?? app()->getLocale();
        $fullKey = "seller.{$key}";

        return trans($fullKey, $replace, $loc);
    }

    /**
     * Format time with locale digits in Bangladesh Timezone
     */
    public static function formatTime(DateTimeInterface|string|null $time, ?string $locale = null, string $format = 'h:i A'): string
    {
        if (! $time) {
            return '';
        }

        $carbon = is_string($time) ? Carbon::parse($time) : Carbon::instance($time);
        $carbon = $carbon->setTimezone('Asia/Dhaka');
        $timeStr = $carbon->format($format);
        $loc = $locale ?? app()->getLocale();

        if ($loc === 'bn') {
            return self::toBanglaNumeral($timeStr);
        }

        return $timeStr;
    }

    /**
     * Format date with locale digits and translated weekday/month in Bangladesh Timezone
     */
    public static function formatDate(DateTimeInterface|string|null $date, ?string $locale = null, string $format = 'D, d M Y'): string
    {
        if (! $date) {
            return '';
        }

        $carbon = is_string($date) ? Carbon::parse($date) : Carbon::instance($date);
        $carbon = $carbon->setTimezone('Asia/Dhaka');
        $loc = $locale ?? app()->getLocale();

        if ($loc !== 'bn') {
            return $carbon->format($format);
        }

        $dayName = $carbon->format('l');
        $dayShort = $carbon->format('D');
        $monthName = $carbon->format('F');
        $monthShort = $carbon->format('M');
        $dayNum = self::toBanglaNumeral($carbon->format('d'));
        $yearNum = self::toBanglaNumeral($carbon->format('Y'));

        $bnDayName = trans("seller.days.{$dayName}", [], 'bn');
        $bnDayShort = trans("seller.days.{$dayShort}", [], 'bn');
        $bnMonthName = trans("seller.months.{$monthName}", [], 'bn');
        $bnMonthShort = trans("seller.months.{$monthShort}", [], 'bn');

        if ($format === 'l, d M' || $format === 'l, d F') {
            return "{$bnDayName}, {$dayNum} {$bnMonthShort}";
        }

        if ($format === 'l, F j, Y') {
            $jNum = self::toBanglaNumeral($carbon->format('j'));

            return "{$bnDayName}, {$jNum} {$bnMonthName} {$yearNum}";
        }

        return "{$bnDayShort}, {$dayNum} {$bnMonthShort} {$yearNum}";
    }

    /**
     * Get greeting based on hour in Bangladesh Timezone and locale
     */
    public static function getGreeting(?string $locale = null): string
    {
        $hour = Carbon::now('Asia/Dhaka')->hour;
        $loc = $locale ?? app()->getLocale();

        if ($hour < 12) {
            return trans('seller.good_morning', [], $loc);
        } elseif ($hour < 17) {
            return trans('seller.good_afternoon', [], $loc);
        }

        return trans('seller.good_evening', [], $loc);
    }
}
