<?php

use App\Support\Format;
use Illuminate\Support\Carbon;

it('formats integer cents as KES', function () {
    expect(Format::kes(1875000))->toBe('KES 18,750.00')
        ->and(Format::kes(1875000, false))->toBe('18,750.00')
        ->and(Format::kes(5))->toBe('KES 0.05')
        ->and(Format::kes(null))->toBe('KES 0.00');
});

it('shortens large amounts for stat tiles', function () {
    expect(Format::kesShort(1740230000))->toBe('KES 17.40M')
        ->and(Format::kesShort(1875000))->toBe('KES 18.8K')
        ->and(Format::kesShort(950000))->toBe('KES 9,500.00');
});

it('parses typed amounts into integer cents', function (?string $input, ?int $cents) {
    expect(Format::parseKes($input))->toBe($cents);
})->with([
    ['18,750.00', 1875000],
    ['KES 18 750', 1875000],
    ['18750', 1875000],
    ['0.1', 10],
    ['', null],
    ['abc', null],
    ['1.2.3', null],
    [null, null],
]);

it('stores Kenyan phone numbers as E.164', function (string $input, ?string $e164) {
    expect(Format::phoneE164($input))->toBe($e164);
})->with([
    ['0712 000 481', '+254712000481'],
    ['712000481', '+254712000481'],
    ['+254 712 000 481', '+254712000481'],
    ['254712000481', '+254712000481'],
    ['0110 000 123', '+254110000123'],
    ['0712 00', null],
    ['0512 000 481', null],
]);

it('displays and masks phone numbers', function () {
    expect(Format::phone('+254712000481'))->toBe('+254 712 000 481')
        ->and(Format::phone('+254712000481', mask: true))->toBe('+254 712 ••• 481')
        ->and(Format::phone('12345'))->toBe('12345');
});

it('displays dates as DD/MM/YYYY', function () {
    expect(Format::date(Carbon::create(2026, 11, 20)))->toBe('20/11/2026')
        ->and(Format::date(null))->toBe('');
});

it('parses typed dates and rejects impossible ones', function (string $input, ?string $iso) {
    expect(Format::parseDate($input)?->toDateString())->toBe($iso);
})->with([
    ['20112026', '2026-11-20'],
    ['20/11/2026', '2026-11-20'],
    ['20-11-26', '2026-11-20'],
    ['31/09/2026', null],
    ['29/02/2026', null],
    ['29/02/2028', '2028-02-29'],
    ['2011', null],
]);

it('runs on Nairobi time', function () {
    expect(config('app.timezone'))->toBe('Africa/Nairobi');
});
