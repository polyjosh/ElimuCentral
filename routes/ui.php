<?php

/*
 * Component gallery (BUILD_PLAN Phase 0). Loaded from bootstrap/app.php after routes/web.php,
 * and only in the local and testing environments.
 */

use App\Http\Controllers\UiKitController;
use Illuminate\Support\Facades\Route;

Route::get('/ui', UiKitController::class)->name('ui');

// <x-term-switcher> posts to `term.switch`, which Phase 1 builds. Until then the gallery
// registers a stub under that name; once the real route exists this stub is skipped.
Route::getRoutes()->refreshNameLookups();

if (! Route::has('term.switch')) {
    Route::post('/ui/term', fn () => back())->name('term.switch');
}
