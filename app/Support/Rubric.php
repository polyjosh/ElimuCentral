<?php

namespace App\Support;

/**
 * CBE rubric levels and 8-4-4 KCSE grades.
 *
 * The default scales live here; a school can override them by passing its own
 * scale (e.g. loaded from a grading_scales table) to fromScore() / kcseGrade().
 */
final class Rubric
{
    /** 8-level CBE scale: code => [min %, max %, points, band]. */
    public const CBE_SCALE = [
        'EE1' => [90, 100, 8, 'EE'],
        'EE2' => [75, 89, 7, 'EE'],
        'ME1' => [58, 74, 6, 'ME'],
        'ME2' => [41, 57, 5, 'ME'],
        'AE1' => [31, 40, 4, 'AE'],
        'AE2' => [21, 30, 3, 'AE'],
        'BE1' => [11, 20, 2, 'BE'],
        'BE2' => [1, 10, 1, 'BE'],
    ];

    /** 4-level bands. `bars` is the signal-pip count shown on chips. */
    public const BANDS = [
        'EE' => ['bars' => 4, 'en' => 'Exceeding Expectations', 'sw' => 'Kuzidi Matarajio'],
        'ME' => ['bars' => 3, 'en' => 'Meeting Expectations', 'sw' => 'Kufikia Matarajio'],
        'AE' => ['bars' => 2, 'en' => 'Approaching Expectations', 'sw' => 'Kukaribia Matarajio'],
        'BE' => ['bars' => 1, 'en' => 'Below Expectations', 'sw' => 'Chini ya Matarajio'],
    ];

    /** KCSE subject grades: grade => [min %, points]. Ordered high to low. */
    public const KCSE_SCALE = [
        'A' => [80, 12], 'A-' => [75, 11], 'B+' => [70, 10], 'B' => [65, 9],
        'B-' => [60, 8], 'C+' => [55, 7], 'C' => [50, 6], 'C-' => [45, 5],
        'D+' => [40, 4], 'D' => [35, 3], 'D-' => [30, 2], 'E' => [0, 1],
    ];

    /** Returns the 8-level code (e.g. "ME1") for a raw score, or null for no score / 0. */
    public static function fromScore(?float $score, float $outOf = 100, ?array $scale = null): ?string
    {
        if ($score === null || $outOf <= 0) {
            return null;
        }

        $pct = (int) round($score / $outOf * 100);

        foreach ($scale ?? self::CBE_SCALE as $code => [$min, $max]) {
            if ($pct >= $min && $pct <= $max) {
                return $code;
            }
        }

        return null;
    }

    /**
     * Everything a chip needs: code, band, bars, points and a spoken label.
     * Accepts 8-level codes ("EE1") and 4-level bands ("EE").
     */
    public static function meta(string $code, ?string $locale = null): array
    {
        $code = strtoupper(trim($code));
        $band = substr($code, 0, 2);
        $sub = substr($code, 2);

        if (! isset(self::BANDS[$band])) {
            throw new \InvalidArgumentException("Unknown rubric level [{$code}].");
        }

        $locale = $locale ?? app()->getLocale();
        $name = self::BANDS[$band][$locale === 'sw' ? 'sw' : 'en'];
        $points = self::CBE_SCALE[$code][2] ?? null;

        $label = $name;
        if ($sub !== '') {
            $label .= $locale === 'sw' ? ", kiwango {$sub}" : ", level {$sub}";
        }
        if ($points !== null) {
            $label .= $locale === 'sw' ? ", alama {$points}" : ", {$points} points";
        }

        return [
            'code' => $code,
            'band' => strtolower($band),
            'bars' => self::BANDS[$band]['bars'],
            'points' => $points,
            'name' => $name,
            'label' => $label,
        ];
    }

    /** @return array{grade: string, points: int} */
    public static function kcseGrade(float $percent, ?array $scale = null): array
    {
        foreach ($scale ?? self::KCSE_SCALE as $grade => [$min, $points]) {
            if ($percent >= $min) {
                return ['grade' => $grade, 'points' => $points];
            }
        }

        return ['grade' => 'E', 'points' => 1];
    }

    /** Mean grade from mean points (e.g. 8.86 => "B"). */
    public static function kcseMeanGrade(float $meanPoints): string
    {
        $bands = [
            'A' => 11.5, 'A-' => 10.5, 'B+' => 9.5, 'B' => 8.5, 'B-' => 7.5, 'C+' => 6.5,
            'C' => 5.5, 'C-' => 4.5, 'D+' => 3.5, 'D' => 2.5, 'D-' => 1.5,
        ];

        foreach ($bands as $grade => $min) {
            if ($meanPoints >= $min) {
                return $grade;
            }
        }

        return 'E';
    }
}
