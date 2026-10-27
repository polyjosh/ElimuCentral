# ElimuCentral — instructions for Claude Code

ElimuCentral is a school management platform for Kenyan schools. It covers the academic year, CBE and 8-4-4 assessment, fees with M-Pesa, and parent communication in English and Kiswahili.

Read these before writing code:
- `docs/BUILD_PLAN.md`: the phases, in order, each with acceptance checks. Work one phase at a time.
- `docs/SCREENS.md`: what every screen shows and does. It is the design source of truth, because you cannot open the design canvas.
- `docs/DATA_MODEL.md`: tables, relationships and integrations (M-Pesa, SMS).
- `README.md`: the UI kit (Tailwind theme, Blade components, Alpine helpers) already in this repo.

## Stack (fixed — do not swap)

- PHP 8.3, Laravel 12, Livewire 3 (with its bundled Alpine), Tailwind CSS **3.4** (not v4: the kit's `app.css` uses v3 `@tailwind` / `@apply`), Vite.
- MySQL 8 (PostgreSQL also fine). Use Redis for queue and cache in production; the `database` driver is fine locally.
- Tests use Pest. Formatting uses Laravel Pint.
- Packages that are allowed (ask before adding any others): `mallardduck/blade-lucide-icons`, `spatie/laravel-permission`, `maatwebsite/excel`, `spatie/laravel-pdf` (or `barryvdh/laravel-dompdf` if Chrome is unavailable), `pestphp/pest`, `pestphp/pest-plugin-laravel`, `pestphp/pest-plugin-livewire`.
- **No SPA patterns.** No Inertia, Vue or React. Pages are server-rendered Blade or full-page Livewire components. Alpine is used only for small local behaviour.

## Commands

```bash
composer install && npm install
php artisan migrate:fresh --seed     # seeds the fictional demo school (see DATA_MODEL.md)
composer run dev                     # serve + queue + vite
php artisan test                     # Pest
vendor/bin/pint                      # format before finishing a task
npm run build                        # must pass before a phase is done
```

## UI rules

- **Use the kit.** Build screens from `resources/views/components/*` (`<x-button>`, `<x-input>`, `<x-data-table>`, `<x-rubric>`, …). Before writing new markup, check whether a component exists. If a pattern repeats twice, make it a component.
- **Tokens only.** Use semantic classes: `bg-surface`, `text-muted`, `bg-primary`, `text-accent-text`, `border-border`, `text-danger`. Never use raw hex, `text-gray-*`, `text-green-*` or `dark:` variants (the tokens already switch for dark mode). Use the `green-*` / `ochre-*` ramps only for charts and the logo.
- **Mobile first from 360 px.** Parent, teacher and learner screens are designed at phone width first. Admin and bursar screens also work at phone width: tables become cards and toolbars wrap.
- **Touch targets are at least 44 px** (`min-h-touch`). Focus is always visible (the global `:focus-visible` ring); never remove outlines.
- **Never use colour alone.** Status needs an icon or a word as well. Rubric levels always go through `<x-rubric>`.
- **Accessibility:** real `<button>`, `<a>`, `<label>`, `<table>`; `aria-label` on icon-only buttons; `aria-current` on active nav; `aria-live` for save status and toasts. Contrast must pass WCAG AA (the tokens already do).
- **Slow 3G budget:** no decorative images, at most one font (Inter, self-hosted woff2), only Lucide icons via the Blade package, and motion limited to 150 ms colour or opacity changes. Each page should send under 150 KB of HTML+CSS+JS gzipped. Lazy-load heavy dashboard widgets (`#[Lazy]` with `<x-stat state="loading">` placeholders). Poll only with `wire:poll.visible`.
- **Every data region designs four states:** loading, empty, error and ready (see SCREENS.md §15).

## Language

- Every user-facing string goes through `__()`. English is the source; Kiswahili lives in `lang/sw.json`. Add both in the same change.
- Kiswahili runs about 30% longer. Never use fixed widths on text, never truncate button labels, and let bottom-nav labels wrap.
- The locale comes from a cookie or `users.locale`, and parents can toggle it on every screen.

## Kenyan formats (always use `App\Support\Format`)

- Money: integer **cents** in the database. Display `KES 18,750.00` with `tabular-nums`.
- Phone: store E.164 `+254712000481`, display `+254 712 000 481`, mask as `+254 712 ••• 481` in lists.
- Dates: display `DD/MM/YYYY`; store `date` / `datetime`; timezone `Africa/Nairobi`.
- Rubric levels and KCSE grades: `App\Support\Rubric` (never re-implement the score bands).

## Data and safety rules

- Multi-tenant by school. Every school-owned model has `school_id` and the global scope `BelongsToSchool`; the current school is `session('school_id')`. Write a test proving another school's data is unreachable.
- Authorize every Livewire action and route with policies. Parents see only their own children. Teachers see only their streams and learning areas. Learners see only their own stream, assignments and approved results, and never fees or class position.
- Never write real people, schools, phone numbers or M-Pesa codes in seeds or tests. Use the fictional set in DATA_MODEL.md. M-Pesa codes in fixtures look like `TEST12AB34`.
- M-Pesa and SMS go through interfaces with a `fake` driver that is the default locally and in tests. Never call live APIs from tests.
- Never store M-Pesa PINs or ask for them. STK push means the parent types the PIN on their own phone.

## How to work

- Do one phase from `docs/BUILD_PLAN.md` at a time. Start each by listing the files you'll touch. End it by running `php artisan test`, `vendor/bin/pint` and `npm run build`, then tick the phase's acceptance checks in your summary, saying which ones you could not verify.
- Write Pest feature tests for every Livewire component and policy, and unit tests for money, phone, date and rubric logic.
- Keep migrations reversible. Never edit a migration that has already run in a shared environment; add a new one.
- When SCREENS.md and this file disagree, this file wins; mention the conflict.
- Ask before adding a package, changing the stack, or deleting data.
