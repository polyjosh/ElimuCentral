<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\View;

/**
 * Renders every UI kit component with sample props (/ui, local and testing only).
 * ?theme=dark shows the dark tokens. All sample data is the fictional demo set.
 */
class UiKitController extends Controller
{
    public function __invoke(Request $request): View
    {
        // Give the error-state examples something to show, without a form submission.
        view()->share('errors', (new ViewErrorBag)->put('default', new MessageBag([
            'sample_score' => 'Enter a mark between 0 and 50.',
            'sample_phone' => 'Enter 9 digits after +254, e.g. 712 000 481.',
        ])));

        $terms = collect([
            [1, 'Term 1', 2025, '2025-01-06', '2025-04-04', 'closed'],
            [2, 'Term 2', 2025, '2025-04-28', '2025-08-01', 'closed'],
            [3, 'Term 3', 2025, '2025-08-25', '2025-10-24', 'closed'],
            [4, 'Term 1', 2026, '2026-01-05', '2026-04-02', 'closed'],
            [5, 'Term 2', 2026, '2026-04-27', '2026-07-31', 'closed'],
            [6, 'Term 3', 2026, '2026-08-24', '2026-11-20', 'current'],
        ])->map(fn ($t) => (object) [
            'id' => $t[0],
            'name' => $t[1],
            'year' => $t[2],
            'opens_on' => Carbon::parse($t[3]),
            'closes_on' => Carbon::parse($t[4]),
            'status' => $t[5],
        ]);

        $school = (object) [
            'id' => 1,
            'name' => 'Mlima Vista Secondary School',
            'short_name' => 'Mlima Vista Sec.',
            'county' => 'Nakuru',
        ];

        // Shell components read these as shared view data (Phase 1's view composer), not props.
        view()->share(['school' => $school, 'schools' => collect([$school])]);

        return view('ui.index', [
            'theme' => $request->query('theme') === 'dark' ? 'dark' : 'light',
            'terms' => $terms,
            'currentTerm' => $terms->firstWhere('status', 'current'),
            'learners' => [
                ['name' => 'Chebet Kiprono', 'adm' => 'MVS/2024/0412', 'stream' => 'Grade 8 Blue', 'balance' => 1875000, 'phone' => '+254712000481', 'curriculum' => 'cbe'],
                ['name' => 'Amina Hassan', 'adm' => 'MVS/2024/0388', 'stream' => 'Grade 8 Blue', 'balance' => 0, 'phone' => '+254722000154', 'curriculum' => 'cbe'],
                ['name' => 'Mutua Musyoka', 'adm' => 'MVS/2023/0127', 'stream' => 'Form 4 East', 'balance' => 642000, 'phone' => '+254733000902', 'curriculum' => '844'],
            ],
        ]);
    }
}
