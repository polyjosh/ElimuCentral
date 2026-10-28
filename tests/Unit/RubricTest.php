<?php

use App\Support\Rubric;

it('maps scores to the 8-level CBE scale at every boundary', function (float $score, float $outOf, ?string $level) {
    expect(Rubric::fromScore($score, $outOf))->toBe($level);
})->with([
    [100, 100, 'EE1'],
    [90, 100, 'EE1'],
    [89, 100, 'EE2'],
    [75, 100, 'EE2'],
    [74, 100, 'ME1'],
    [58, 100, 'ME1'],
    [57, 100, 'ME2'],
    [41, 100, 'ME2'],
    [40, 100, 'AE1'],
    [31, 100, 'AE1'],
    [30, 100, 'AE2'],
    [21, 100, 'AE2'],
    [20, 100, 'BE1'],
    [11, 100, 'BE1'],
    [10, 100, 'BE2'],
    [1, 100, 'BE2'],
    '37/50 is 74%' => [37, 50, 'ME1'],
    '37.5/50 is 75%' => [37.5, 50, 'EE2'],
    'rounds 44.5% to 45%' => [22.25, 50, 'ME2'],
]);

it('returns no level without a score or a total', function () {
    expect(Rubric::fromScore(null))->toBeNull()
        ->and(Rubric::fromScore(0))->toBeNull()
        ->and(Rubric::fromScore(20, 0))->toBeNull();
});

it('describes a level for screen readers in English and Kiswahili', function () {
    expect(Rubric::meta('ME1', 'en'))->toMatchArray([
        'code' => 'ME1',
        'band' => 'me',
        'bars' => 3,
        'points' => 6,
        'label' => 'Meeting Expectations, level 1, 6 points',
    ])
        ->and(Rubric::meta('ME1', 'sw')['label'])->toBe('Kufikia Matarajio, kiwango 1, alama 6')
        ->and(Rubric::meta('ee', 'en'))->toMatchArray(['code' => 'EE', 'bars' => 4, 'points' => null, 'label' => 'Exceeding Expectations']);
});

it('rejects an unknown level', function () {
    Rubric::meta('XX1');
})->throws(InvalidArgumentException::class);

it('maps percentages to KCSE grades at every boundary', function (float $percent, string $grade, int $points) {
    expect(Rubric::kcseGrade($percent))->toBe(['grade' => $grade, 'points' => $points]);
})->with([
    [100, 'A', 12], [80, 'A', 12], [79.9, 'A-', 11], [75, 'A-', 11], [70, 'B+', 10], [65, 'B', 9],
    [60, 'B-', 8], [55, 'C+', 7], [50, 'C', 6], [45, 'C-', 5], [40, 'D+', 4], [35, 'D', 3],
    [30, 'D-', 2], [29.9, 'E', 1], [0, 'E', 1],
]);

it('turns mean points into a KCSE mean grade', function (float $points, string $grade) {
    expect(Rubric::kcseMeanGrade($points))->toBe($grade);
})->with([
    [12, 'A'], [11.5, 'A'], [11.49, 'A-'], [8.86, 'B'], [8.5, 'B'], [8.49, 'B-'], [1.5, 'D-'], [1.49, 'E'],
]);
