<?php

use Illuminate\Support\Facades\Blade;

it('renders the component gallery in light mode', function () {
    $this->get('/ui')
        ->assertOk()
        ->assertSee('ElimuCentral UI kit')
        ->assertDontSee('<html lang="en" class="dark">', false);
});

it('renders the component gallery in dark mode', function () {
    $this->get('/ui?theme=dark')
        ->assertOk()
        ->assertSee('<html lang="en" class="dark">', false);
});

it('shows the sample validation errors on the form controls', function () {
    $this->get('/ui')
        ->assertSee('Enter a mark between 0 and 50.')
        ->assertSee('aria-invalid="true"', false);
});

it('resolves every icon named in the navigation config', function () {
    $icons = collect(config('navigation.sidebar'))
        ->merge(collect(config('navigation.bottom'))->flatten(1))
        ->pluck('icon')
        ->filter()
        ->unique();

    expect($icons)->not->toBeEmpty();

    foreach ($icons as $icon) {
        expect(Blade::render('<x-dynamic-component :component="$c" />', ['c' => 'lucide-'.$icon]))
            ->toContain('<svg');
    }
});

it('serves the placeholder home page with the self-hosted font and favicon', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('/fonts/inter-latin-wght-normal.woff2', false)
        ->assertSee('/favicon.svg', false)
        ->assertDontSee('fonts.googleapis.com', false);

    expect(public_path('fonts/inter-latin-wght-normal.woff2'))->toBeFile()
        ->and(public_path('favicon.svg'))->toBeFile();
});

it('does not register the gallery outside local and testing', function () {
    $original = $_SERVER['APP_ENV'] ?? null;
    $_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = 'production';
    putenv('APP_ENV=production');

    try {
        $this->refreshApplication();

        expect(app()->environment())->toBe('production');
        $this->get('/ui')->assertNotFound();
    } finally {
        $_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = $original ?? 'testing';
        putenv('APP_ENV='.($original ?? 'testing'));
        $this->refreshApplication();
    }
});
