<?php

namespace App\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

/**
 * Kenyan display and input formats: KES money stored as integer cents,
 * +254 phone numbers stored as E.164, dates shown as DD/MM/YYYY.
 */
final class Format
{
    /** 1875000 => "KES 18,750.00" */
    public static function kes(?int $cents, bool $withCurrency = true): string
    {
        $value = number_format(($cents ?? 0) / 100, 2, '.', ',');

        return $withCurrency ? "KES {$value}" : $value;
    }

    /** 1740230000 => "KES 17.40M" for stat tiles; keep the exact value in a title attribute. */
    public static function kesShort(int $cents): string
    {
        $shillings = $cents / 100;

        return match (true) {
            $shillings >= 1_000_000 => 'KES '.number_format($shillings / 1_000_000, 2).'M',
            $shillings >= 10_000 => 'KES '.number_format($shillings / 1_000, 1).'K',
            default => self::kes($cents),
        };
    }

    /** "18,750.00", "KES 18 750", "18750" => 1875000. Returns null when not a number. */
    public static function parseKes(?string $input): ?int
    {
        $clean = preg_replace('/[^0-9.]/', '', (string) $input);

        if ($clean === '' || substr_count($clean, '.') > 1) {
            return null;
        }

        return (int) round(((float) $clean) * 100);
    }

    /** "0712 000 481", "712000481", "+254 712 000 481" => "+254712000481". Null if not 9 digits. */
    public static function phoneE164(?string $input): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $input);

        if (str_starts_with($digits, '254')) {
            $digits = substr($digits, 3);
        }
        $digits = ltrim($digits, '0');

        return preg_match('/^[17]\d{8}$/', $digits) ? "+254{$digits}" : null;
    }

    /** "+254712000481" => "+254 712 000 481"; masked => "+254 712 ••• 481" */
    public static function phone(?string $e164, bool $mask = false): string
    {
        $digits = substr(preg_replace('/\D/', '', (string) $e164), -9);

        if (strlen($digits) !== 9) {
            return (string) $e164;
        }

        $middle = $mask ? '•••' : substr($digits, 3, 3);

        return '+254 '.substr($digits, 0, 3).' '.$middle.' '.substr($digits, 6);
    }

    public static function date(?CarbonInterface $date): string
    {
        return $date?->format('d/m/Y') ?? '';
    }

    /** "20/11/2026", "20-11-26", "20112026" => Carbon, or null when invalid. */
    public static function parseDate(?string $input): ?Carbon
    {
        $digits = preg_replace('/\D/', '', (string) $input);

        if (strlen($digits) === 6) {
            $digits = substr($digits, 0, 4).'20'.substr($digits, 4);
        }

        if (strlen($digits) !== 8) {
            return null;
        }

        [$d, $m, $y] = [(int) substr($digits, 0, 2), (int) substr($digits, 2, 2), (int) substr($digits, 4)];

        return checkdate($m, $d, $y) ? Carbon::create($y, $m, $d)->startOfDay() : null;
    }
}
