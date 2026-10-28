# ElimuCentral — instructions for Claude Code

ElimuCentral is a school management platform for Kenyan schools. It covers the academic year, CBE and 8-4-4 assessment, fees with M-Pesa, and parent communication in English and Kiswahili.

Read these before writing code:
- `docs/BUILD_PLAN.md`: the phases, in order, each with acceptance checks. Work one phase at a time.
- `docs/SCREENS.md`: what every screen shows and does. It is the design source of truth, because you cannot open the design canvas.
- `docs/DATA_MODEL.md`: tables, relationships and integrations (M-Pesa, SMS).
- `README.md`: the UI kit (Tailwind theme, Blade components, Alpine helpers) already in this repo.

## Stack (fixed — do not swap)

- PHP 8.4, Laravel 13, Livewire 4 (with its bundled Alpine), Tailwind CSS 4, Vite. The owner chose the latest versions over the kit's original Laravel 12 / Livewire 3 / Tailwind 3.4 pin (October 2026).
- **Tailwind 4 has no `tailwind.config.js`.** The theme lives in `resources/css/theme.css` (`@theme`). The default Tailwind palette is switched off, so only the semantic tokens and the brand ramps exist: a class like `text-gray-500` silently generates nothing. `app.css` restores the v3 defaults the kit relies on (border colour, button cursor).
- **Livewire 4:** `wire:model` binds only on the element that carries it (it no longer picks up events from children), so put it on the real `<input>`, as the kit components already do. Livewire view components live in `resources/views/livewire`; full-page components use the kit layout `components.layouts.app` (`config/livewire.php`).
- MySQL 8.4 (PostgreSQL also fine). Use Redis for queue and cache in production; the `database` driver is fine locally.
- Tests use Pest 4. Formatting uses Laravel Pint.
- Packages that are allowed (ask before adding any others): `mallardduck/blade-lucide-icons`, `spatie/laravel-permission`, `maatwebsite/excel`, `spatie/laravel-pdf` (or `barryvdh/laravel-dompdf` if Chrome is unavailable), `pestphp/pest`, `pestphp/pest-plugin-laravel`, `pestphp/pest-plugin-livewire`, `laravel/sail` (dev only).
- **No SPA patterns.** No Inertia, Vue or React. Pages are server-rendered Blade or full-page Livewire components. Alpine is used only for small local behaviour.

## Commands

PHP, Composer and MySQL run in Docker through Laravel Sail (the host machine has no PHP). Node runs on the host.

```bash
./vendor/bin/sail up -d                          # app http://localhost:8000, MySQL :3308, Redis :6380
./vendor/bin/sail composer install && npm install
./vendor/bin/sail artisan migrate:fresh --seed   # seeds the fictional demo school (see DATA_MODEL.md)
npm run dev                                      # Vite dev server with hot reload (port 5173)
./vendor/bin/sail artisan test                   # Pest, against the MySQL `testing` database
./vendor/bin/sail bin pint                       # format before finishing a task
npm run build                                    # must pass before a phase is done
./vendor/bin/sail stop                           # stop the containers (data is kept)
```

If PHP 8.4 is ever installed on the host, the plain `php artisan …` / `vendor/bin/pint` forms work too.

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
