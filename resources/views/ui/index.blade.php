{{--
    UI kit gallery (BUILD_PLAN Phase 0). Local and testing only; not a user-facing page,
    so the sample copy below is not translated. Components keep their own __() strings.
    Light: /ui   Dark: /ui?theme=dark
--}}
@php use App\Support\Format; use App\Support\Rubric; @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => $theme === 'dark'])>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>UI kit · ElimuCentral</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="preload" href="/fonts/inter-latin-wght-normal.woff2" as="font" type="font/woff2" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-dvh bg-bg">
    <x-offline-banner />

    <header class="sticky top-0 z-30 border-b border-border bg-surface">
        <div class="mx-auto flex max-w-content flex-wrap items-center gap-3 px-4 py-3 sm:px-6">
            <x-shell.mark class="size-8" />
            <div class="min-w-0 flex-1">
                <h1 class="text-lg font-semibold">ElimuCentral UI kit</h1>
                <p class="text-sm text-muted">Every component with sample props. Check light and dark before shipping a screen.</p>
            </div>
            <nav aria-label="Theme" class="flex gap-2">
                <x-button :variant="$theme === 'light' ? 'primary' : 'secondary'" size="sm" icon="sun" href="{{ route('ui') }}">Light</x-button>
                <x-button :variant="$theme === 'dark' ? 'primary' : 'secondary'" size="sm" icon="moon" href="{{ route('ui', ['theme' => 'dark']) }}">Dark</x-button>
            </nav>
        </div>
    </header>

    <main id="main" class="mx-auto flex max-w-content flex-col gap-10 px-4 py-8 sm:px-6">

        {{-- ───────────── Tokens ───────────── --}}
        <section aria-labelledby="tokens" class="flex flex-col gap-4">
            <h2 id="tokens" class="text-2xl font-semibold">Tokens</h2>

            <x-card title="Semantic colours">
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-6">
                    @foreach ([
                        'bg' => 'bg-bg', 'surface' => 'bg-surface', 'surface-2' => 'bg-surface-2', 'surface-3' => 'bg-surface-3',
                        'border' => 'bg-border', 'border-strong' => 'bg-border-strong', 'text' => 'bg-text', 'text-2' => 'bg-text-2',
                        'muted' => 'bg-muted', 'subtle' => 'bg-subtle', 'ring' => 'bg-ring', 'inverse' => 'bg-inverse',
                        'primary' => 'bg-primary', 'primary-hover' => 'bg-primary-hover', 'primary-soft' => 'bg-primary-soft', 'primary-text' => 'bg-primary-text',
                        'accent' => 'bg-accent', 'accent-hover' => 'bg-accent-hover', 'accent-soft' => 'bg-accent-soft', 'accent-text' => 'bg-accent-text',
                        'success' => 'bg-success', 'success-bg' => 'bg-success-bg', 'warning' => 'bg-warning', 'warning-bg' => 'bg-warning-bg',
                        'danger' => 'bg-danger', 'danger-solid' => 'bg-danger-solid', 'danger-bg' => 'bg-danger-bg', 'info' => 'bg-info',
                        'info-bg' => 'bg-info-bg', 'plum' => 'bg-plum', 'plum-bg' => 'bg-plum-bg',
                    ] as $name => $class)
                        <div class="flex items-center gap-2">
                            <span class="{{ $class }} size-10 shrink-0 rounded border border-border"></span>
                            <code class="text-xs text-text-2">{{ $name }}</code>
                        </div>
                    @endforeach
                </div>
            </x-card>

            <div class="grid gap-4 lg:grid-cols-2">
                <x-card title="Brand ramps (charts and logo only)">
                    @foreach (['green' => ['bg-green-50', 'bg-green-100', 'bg-green-200', 'bg-green-300', 'bg-green-400', 'bg-green-500', 'bg-green-600', 'bg-green-700', 'bg-green-800', 'bg-green-900', 'bg-green-950'],
                               'ochre' => ['bg-ochre-50', 'bg-ochre-100', 'bg-ochre-200', 'bg-ochre-300', 'bg-ochre-400', 'bg-ochre-500', 'bg-ochre-600', 'bg-ochre-700', 'bg-ochre-800', 'bg-ochre-900', 'bg-ochre-950'],
                               'stone' => ['bg-stone-25', 'bg-stone-50', 'bg-stone-100', 'bg-stone-200', 'bg-stone-300', 'bg-stone-400', 'bg-stone-500', 'bg-stone-600', 'bg-stone-700', 'bg-stone-800', 'bg-stone-900']] as $ramp => $classes)
                        <p class="mb-1 text-sm font-semibold text-text-2">{{ $ramp }}</p>
                        <div class="mb-3 flex overflow-hidden rounded border border-border">
                            @foreach ($classes as $class)
                                <span class="{{ $class }} h-8 flex-1"></span>
                            @endforeach
                        </div>
                    @endforeach
                </x-card>

                <x-card title="Type, radius and elevation">
                    <div class="flex flex-col gap-1">
                        <p class="text-4xl font-bold">4xl 36/44</p>
                        <p class="text-3xl font-semibold">3xl 30/38</p>
                        <p class="text-2xl font-semibold">2xl 24/32</p>
                        <p class="text-xl">xl 20/28</p>
                        <p class="text-lg">lg 18/28</p>
                        <p class="text-base">base 16/24 — minimum for parent screens</p>
                        <p class="text-sm">sm 14/20</p>
                        <p class="text-xs">xs 12/16</p>
                        <p class="text-base tabular-nums">tabular-nums: {{ Format::kes(1875000) }} · 1,284 · 20/11/2026</p>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-3">
                        @foreach (['rounded-sm', 'rounded-md', 'rounded', 'rounded-lg', 'rounded-xl'] as $r)
                            <span class="{{ $r }} grid h-14 w-24 place-items-center border border-border-strong bg-surface-2 text-xs">{{ $r }}</span>
                        @endforeach
                    </div>
                    <div class="mt-4 flex flex-wrap gap-4">
                        @foreach (['shadow-sm', 'shadow-md', 'shadow-lg'] as $s)
                            <span class="{{ $s }} grid h-14 w-24 place-items-center rounded bg-surface text-xs">{{ $s }}</span>
                        @endforeach
                    </div>
                </x-card>
            </div>
        </section>

        {{-- ───────────── Actions ───────────── --}}
        <section aria-labelledby="actions" class="flex flex-col gap-4">
            <h2 id="actions" class="text-2xl font-semibold">Buttons, badges and avatars</h2>

            <x-card title="&lt;x-button&gt;">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <x-button>Save marks</x-button>
                        <x-button variant="secondary" icon="download">Export</x-button>
                        <x-button variant="ghost">Cancel</x-button>
                        <x-button variant="accent" icon="wallet">Pay with M-Pesa</x-button>
                        <x-button variant="danger" icon="trash-2">Delete draft</x-button>
                        <x-button disabled>Disabled</x-button>
                        <x-button variant="secondary" href="#actions" icon="link">Link button</x-button>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-button size="sm" variant="secondary">sm 36 px (desktop toolbars)</x-button>
                        <x-button size="md">md 44 px</x-button>
                        <x-button size="lg">lg 52 px</x-button>
                    </div>
                    <div class="max-w-sm"><x-button size="xl" variant="accent" icon="smartphone">Pay KES 18,750.00</x-button></div>
                </div>
            </x-card>

            <div class="grid gap-4 lg:grid-cols-2">
                <x-card title="&lt;x-badge&gt;">
                    <div class="flex flex-wrap gap-2">
                        <x-badge>Neutral</x-badge>
                        <x-badge tone="success" icon="check">Paid</x-badge>
                        <x-badge tone="warning" icon="clock">Due soon</x-badge>
                        <x-badge tone="danger" icon="x">Absent</x-badge>
                        <x-badge tone="info" icon="info">Upcoming</x-badge>
                        <x-badge tone="primary">Current</x-badge>
                        <x-badge tone="accent">Needs a match</x-badge>
                        <x-badge tone="plum">Arts</x-badge>
                    </div>
                </x-card>

                <x-card title="&lt;x-avatar&gt;">
                    <div class="flex flex-wrap items-end gap-3">
                        <x-avatar name="Chebet Kiprono" size="sm" />
                        <x-avatar name="Otieno Odhiambo" />
                        <x-avatar name="Amina Hassan" size="lg" />
                        <x-avatar name="Naserian Lekishon" size="xl" />
                    </div>
                </x-card>
            </div>
        </section>

        {{-- ───────────── Forms ───────────── --}}
        <section aria-labelledby="forms" class="flex flex-col gap-4">
            <h2 id="forms" class="text-2xl font-semibold">Form controls</h2>

            <div class="grid gap-4 lg:grid-cols-2">
                <x-card title="Text, select and choice">
                    <div class="flex flex-col gap-5">
                        <x-input name="sample_name" label="Learner's full name" hint="As written on the birth certificate." required />
                        <x-input name="sample_score" label="Score (out of 50)" type="text" inputmode="decimal" value="54" />
                        <x-input name="sample_disabled" label="Admission number" value="MVS/2024/0412" disabled />
                        <x-select name="sample_stream" label="Stream" placeholder="Choose a stream"
                                  :options="['1' => 'Grade 8 Blue', '2' => 'Grade 8 Green', '3' => 'Form 4 East']" />
                        <x-segmented name="sample_residence" label="Residence" :label-sr-only="false" value="day"
                                     :options="['day' => 'Day', 'boarding' => 'Boarding']" />
                        <div class="flex flex-col">
                            <x-checkbox name="sample_notify" checked>Send SMS to guardians</x-checkbox>
                            <x-checkbox name="sample_off" disabled>Disabled option</x-checkbox>
                        </div>
                        <x-field name="sample_field" label="Custom control in x-field" hint="Any control can sit inside x-field.">
                            <textarea id="sample-field" class="ec-control" rows="3" aria-describedby="sample-field-hint">Chebet has improved in Mathematics this term.</textarea>
                        </x-field>
                    </div>
                </x-card>

                <x-card title="Kenyan inputs">
                    <div class="flex flex-col gap-5">
                        <x-phone-input name="sample_phone_ok" label="Guardian's phone" value="712 000 481" />
                        <x-phone-input name="sample_phone" label="Phone with an error" value="712 00" />
                        <x-currency-input name="sample_amount" label="Amount" value="18,750.00" />
                        <x-date-input name="sample_closing" label="Closing date" hint="Type 20112026 or use the calendar." />
                        <x-file-upload accept=".xlsx,.csv" hint=".xlsx or .csv, up to 5 MB" />
                    </div>
                </x-card>
            </div>
        </section>

        {{-- ───────────── Data ───────────── --}}
        <section aria-labelledby="data" class="flex flex-col gap-4">
            <h2 id="data" class="text-2xl font-semibold">Cards, stats and tables</h2>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-stat label="Fees collected · Term 3" icon="wallet" :value="Format::kesShort(1740230000)"
                        :exact="Format::kes(1740230000)" :progress="0.70" hint="70.0% of KES 24.86M expected" />
                <x-stat label="Attendance today" state="loading" />
                <x-stat label="Attendance today" icon="clipboard-check" state="empty" hint="No registers taken yet." />
                <x-stat label="M-Pesa today" icon="smartphone" state="error" hint="Couldn't load this. Ref EC-4410." />
            </div>
            <x-stat class="max-w-xs" tone="accent" label="Needs a match" icon="link" value="4" hint="M-Pesa payments without a learner" />

            <x-tabs label="Learner sections" active="academics" :tabs="[
                'academics' => ['label' => 'Academics', 'href' => '#data'],
                'attendance' => ['label' => 'Attendance', 'href' => '#data'],
                'fees' => ['label' => 'Fees', 'href' => '#data', 'badge' => 'KES 18,750', 'tone' => 'danger'],
                'discipline' => ['label' => 'Discipline', 'href' => '#data'],
                'documents' => ['label' => 'Documents', 'href' => '#data'],
            ]" />

            <x-breadcrumbs :items="['Learners' => '#data', 'Grade 8 Blue' => '#data', 'Chebet Kiprono' => null]" />

            <x-bulk-bar :count="2">
                <x-button size="sm" variant="secondary" icon="message-square">Send SMS reminder</x-button>
            </x-bulk-bar>

            <x-data-table max-height="420px">
                <x-slot:head>
                    <th class="w-10"><input type="checkbox" aria-label="Select all"></th>
                    <th aria-sort="ascending">Learner ↑</th>
                    <th>Stream</th>
                    <th>Curriculum</th>
                    <th>Guardian</th>
                    <th class="text-right">Balance (KES)</th>
                </x-slot:head>

                @foreach ($learners as $i => $learner)
                    <tr @class(['is-selected' => $i < 2])>
                        <td><input type="checkbox" aria-label="Select {{ $learner['name'] }}" @checked($i < 2)></td>
                        <td>
                            <div class="flex items-center gap-2.5">
                                <x-avatar :name="$learner['name']" size="sm" />
                                <div><b class="block">{{ $learner['name'] }}</b><span class="text-xs text-muted tabular-nums">{{ $learner['adm'] }}</span></div>
                            </div>
                        </td>
                        <td>{{ $learner['stream'] }}</td>
                        <td><x-curriculum-badge :curriculum="$learner['curriculum']" /></td>
                        <td class="tabular-nums">{{ Format::phone($learner['phone'], mask: true) }}</td>
                        <td class="text-right tabular-nums">{{ Format::kes($learner['balance'], false) }}</td>
                    </tr>
                @endforeach

                <x-slot:cards>
                    @foreach ($learners as $i => $learner)
                        <x-data-table.card :selected="$i < 2">
                            <div class="flex items-center gap-2.5">
                                <x-avatar :name="$learner['name']" size="sm" />
                                <div class="min-w-0 flex-1"><b class="block">{{ $learner['name'] }}</b><span class="text-xs text-muted">{{ $learner['stream'] }} · {{ $learner['adm'] }}</span></div>
                                <x-curriculum-badge :curriculum="$learner['curriculum']" />
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-muted">Balance</span>
                                <b class="tabular-nums">{{ Format::kes($learner['balance']) }}</b>
                            </div>
                        </x-data-table.card>
                    @endforeach
                </x-slot:cards>
            </x-data-table>

            <div class="grid gap-4 lg:grid-cols-3">
                <x-card title="Empty"><x-empty-state icon="user-plus" title="No learners in Grade 8 Blue" description="Import your class list from Excel, or add learners one at a time."><x-button icon="upload">Import from Excel</x-button></x-empty-state></x-card>
                <x-card title="No results"><x-empty-state tone="neutral" icon="search" title="No learners match “chebbet”" description="Did you mean Chebet? Check the spelling or clear the filters." /></x-card>
                <x-card title="Error"><x-empty-state tone="danger" icon="triangle-alert" title="We couldn't load learners" description="Check your connection and try again. Ref EC-4410." /></x-card>
            </div>
        </section>

        {{-- ───────────── Assessment ───────────── --}}
        <section aria-labelledby="assessment" class="flex flex-col gap-4">
            <h2 id="assessment" class="text-2xl font-semibold">Assessment</h2>

            <div class="grid gap-4 lg:grid-cols-2">
                <x-card title="&lt;x-rubric&gt;">
                    <div class="flex flex-col gap-3">
                        <div class="flex flex-wrap gap-2">
                            @foreach (array_keys(Rubric::CBE_SCALE) as $code)
                                <x-rubric :level="$code" />
                            @endforeach
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach (array_keys(Rubric::BANDS) as $band)
                                <x-rubric :level="$band" size="lg" />
                            @endforeach
                            <x-rubric level="ME1" size="sm" />
                            <x-rubric :level="null" />
                        </div>
                        <p class="text-sm text-muted">From a score: 37/50 → <x-rubric :level="Rubric::fromScore(37, 50)" size="sm" /></p>
                    </div>
                </x-card>

                <x-card title="KCSE, curriculum and pathways">
                    <div class="flex flex-col gap-3">
                        <div class="flex flex-wrap gap-2">
                            @foreach (array_keys(Rubric::KCSE_SCALE) as $grade)
                                <x-kcse-grade :grade="$grade" />
                            @endforeach
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <x-curriculum-badge curriculum="cbe" />
                            <x-curriculum-badge curriculum="844" />
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <x-pathway-badge pathway="stem" />
                            <x-pathway-badge pathway="social_sciences" />
                            <x-pathway-badge pathway="arts_sports" />
                        </div>
                    </div>
                </x-card>
            </div>

            <x-card title="&lt;x-rubric-picker&gt; (keys 1–8 jump to a level)">
                <x-rubric-picker name="sample_strand" label="2.0 Measurement" />
            </x-card>
        </section>

        {{-- ───────────── Overlays and shell ───────────── --}}
        <section aria-labelledby="overlays" class="flex flex-col gap-4">
            <h2 id="overlays" class="text-2xl font-semibold">Overlays, feedback and shell pieces</h2>

            <x-card title="Modal, drawer and toasts">
                <div class="flex flex-wrap gap-3">
                    <x-button variant="secondary" icon="message-square" x-data x-on:click="$dispatch('open-modal', 'ui-confirm')">Open modal</x-button>
                    <x-button variant="secondary" icon="panel-right" x-data x-on:click="$dispatch('open-drawer', 'ui-drawer')">Open drawer</x-button>
                    <x-button variant="secondary" icon="circle-check" x-data x-on:click="$dispatch('toast', { message: 'Register saved. SMS sent to 3 guardians.', tone: 'success' })">Success toast</x-button>
                    <x-button variant="secondary" icon="triangle-alert" x-data x-on:click="$dispatch('toast', { message: 'Could not send 2 SMS. Check the numbers and try again.', tone: 'error' })">Error toast</x-button>
                </div>
                <p class="mt-3 text-sm text-muted">The offline banner appears at the top of the page when you switch DevTools to offline.</p>
            </x-card>

            <div class="grid gap-4 lg:grid-cols-2">
                <x-card title="&lt;x-term-switcher&gt;">
                    <x-term-switcher :terms="$terms" :current="$currentTerm" />
                </x-card>
                <x-card title="&lt;x-shell.school-switcher&gt; and &lt;x-shell.mark&gt;">
                    <div class="flex flex-col gap-4">
                        <div class="max-w-64"><x-shell.school-switcher /></div>
                        <div class="flex items-end gap-3">
                            <x-shell.mark class="size-4" />
                            <x-shell.mark class="size-8" />
                            <x-shell.mark class="size-16" />
                        </div>
                        <p class="text-sm text-muted">The sidebar, top bar, bottom nav and profile menu need signed-in users and their routes; they are checked from Phase 2.</p>
                    </div>
                </x-card>
            </div>
        </section>
    </main>

    <x-modal name="ui-confirm" title="Send 48 fee reminders?">
        Guardians of 48 learners with a balance above KES 5,000 will get one SMS each, in their own language.
        <x-slot:footer>
            <x-button variant="secondary" x-on:click="close()">Cancel</x-button>
            <x-button icon="send" x-on:click="close(); $dispatch('toast', { message: 'Queued 48 SMS.', tone: 'success' })">Send 48 SMS</x-button>
        </x-slot:footer>
    </x-modal>

    <x-drawer name="ui-drawer" title="TEST12AB34" eyebrow="Payment">
        <dl class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-2 text-sm">
            <dt class="text-muted">Amount</dt><dd class="tabular-nums font-semibold">{{ Format::kes(1875000) }}</dd>
            <dt class="text-muted">Paid by</dt><dd class="tabular-nums">{{ Format::phone('+254712000481', mask: true) }}</dd>
            <dt class="text-muted">Account</dt><dd>MVS0412</dd>
            <dt class="text-muted">Matched to</dt><dd>Chebet Kiprono · Grade 8 Blue</dd>
        </dl>
        <x-slot:footer><x-button variant="secondary" icon="printer">Print receipt</x-button></x-slot:footer>
    </x-drawer>

    <x-toast-stack />
    @livewireScriptConfig
</body>
</html>
