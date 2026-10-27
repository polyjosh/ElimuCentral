# ElimuCentral — build plan

Work top to bottom. One phase per session (`/build-phase N`). A phase is done only when:
- `php artisan test` is green, `vendor/bin/pint` has run, and `npm run build` succeeds;
- every acceptance check below is ticked in your summary, or explicitly marked "not verified" with the reason;
- the demo seed still runs: `php artisan migrate:fresh --seed`.

Screens are specified in `docs/SCREENS.md` (S1–S18); tables in `docs/DATA_MODEL.md`.

---

## Phase 0 — Project setup

Context: the repo is a fresh `laravel/laravel` 12 app with this UI kit copied on top.

1. Install Livewire 3, `mallardduck/blade-lucide-icons`, `spatie/laravel-permission`, and Pest with its Laravel and Livewire plugins.
2. Replace Laravel 12's Tailwind v4 setup with **Tailwind 3.4**:
   - Remove `@tailwindcss/vite`.
   - Install `tailwindcss@^3.4 postcss autoprefixer`.
   - Add `postcss.config.js`.
   - Keep the kit's `tailwind.config.js` and `resources/css/app.css`.
3. In `vite.config.js`, set the inputs to `resources/css/app.css` and `resources/js/app.js`, and keep `refresh: true`.
4. Self-host Inter (latin, 400/500/600/700 woff2) under `public/fonts`, add `@font-face` to `app.css`, and remove the Google Fonts `<link>` from the layout.
5. Config:
   - `APP_TIMEZONE=Africa/Nairobi`, default locale `en`, fallback `en`.
   - Create `lang/sw.json` (empty object to start).
   - `services.sms.driver=fake`, `services.mpesa.driver=fake`.
6. Add `public/favicon.svg` from `resources/views/components/shell/mark.blade.php`.
7. Add a `/ui` route (local env only) that renders every kit component with sample props, in light and dark. This is the visual check page for the rest of the project.

**Acceptance**
- [ ] `/ui` renders every component without errors in light and dark (add `?theme=dark`).
- [ ] Every Lucide icon name used in the kit resolves (no missing-component errors).
- [ ] A Pest smoke test hits `/ui` and asserts 200.
- [ ] `npm run build` output CSS is under 60 KB gzipped.

## Phase 1 — Tenancy, auth, roles, locale, theme

- Migrations and models: schools, users, school_user, otp_codes, plus the spatie permission tables with **teams** enabled.
- `BelongsToSchool` trait and global scope; middleware that sets `session('school_id')` and the permission team id.
- **Staff login (S1 desktop):** email or phone + password, throttled.
- **Parent OTP login (S1 phone):** OTP via `SmsGateway`, with the rules in S1.
- Routes: `locale.switch`, `theme.switch`, `school.switch` (POST, redirect back), `logout` (also clears the IndexedDB offline queue via a small inline script on the logged-out page).
- View composer that shares `$school`, `$schools`, `$terms`, `$currentTerm`, `$navCounts`.
- Demo seed for both schools and the named users (DATA_MODEL.md).

**Acceptance**
- [ ] Staff and parent login work; the OTP is printed in `storage/logs/sms.log` locally.
- [ ] Tenancy test: a user of Mto Safi cannot read any Mlima Vista record (models, routes and Livewire actions).
- [ ] The language toggle switches every visible string on the login screens to Kiswahili.
- [ ] Throttling and OTP expiry are tested.

## Phase 2 — App shell and dashboard skeleton

- `<x-layouts.app>` wired to real data: role-filtered sidebar (`config/navigation.php`), bottom nav per role, term switcher (session-based), notifications page, search results page (`/search?q=`) across learners (name, adm, UPI), guardians (phone) and payments (M-Pesa code).
- **Principal dashboard (S2)** with lazy widgets. Use real queries where tables exist, and `<x-stat state="empty">` until later phases add data.
- **S15 dashboard states:** loading placeholder, the new-school setup checklist (shown when there are no learners), and per-widget error with retry and an `EC-####` ref.

**Acceptance**
- [ ] The sidebar shows only what each role may see (tests for principal, bursar, teacher, parent).
- [ ] At 360 px the shell shows the app bar and bottom nav with no horizontal scroll; from `lg` up it shows the sidebar.
- [ ] Switching term changes the dashboard's figures and persists across pages.
- [ ] Keyboard only: skip link, sidebar, menus (Esc closes and returns focus) and search (`/` focuses) all work.

## Phase 3 — Academic setup (S3)

- Years and terms CRUD (closed terms locked), levels, streams with class teacher, learning areas and strands, pathways and combinations with capacity, grading scales (seeded from `Rubric`).
- Mark streams without a class teacher.

**Acceptance**
- [ ] Editing a closed term is blocked server-side, not just in the UI.
- [ ] Date inputs accept `20112026` and reject `31/09/2026` with the message from the kit.
- [ ] Combination capacity is enforced on enrolment.

## Phase 4 — Learners (S4a, S4b, S5)

- Learners list: `#[Url]` filters, sort, bulk bar, tabs, cards on phones, "Load 25 more".
- Profile header and the five tabs (Fees and Attendance tabs show placeholders until phases 5 and 7).
- Excel import wizard with `maatwebsite/excel`: upload → map (header synonyms) → preview with errors and warnings → confirm → queued job with progress → error-rows download. Template download.
- S15 learners states: loading, empty, no-results with the "did you mean" hint, and error.

**Acceptance**
- [ ] Importing the seeded sample file creates learners, guardians and enrolments; duplicate UPIs, bad dates, unknown streams and short phones are reported exactly as in S5.
- [ ] Phone numbers in any of the forms `0712…`, `712…`, `+254…` are stored as E.164.
- [ ] A teacher sees only learners in their streams; a parent gets a 403 on any other learner.

## Phase 5 — Attendance (S6, S7)

- Teacher home (S6): today's lessons (stub data until Phase 9 builds the timetable; read from `timetable_slots` when present), register card and pending marks.
- Register (S7): P/A/L toggles (default present), counts, mark all present, absence SMS with a preview in the guardian's language, offline queue and sync (reuse `offline-queue.js`), one register per stream per day.
- Dashboard attendance widgets now show real data.

**Acceptance**
- [ ] Saving sends one SMS per absent learner's receiving guardians via the fake driver, in their language; re-saving doesn't send duplicates.
- [ ] With DevTools offline, saving shows the offline banner; going online syncs and fires a toast.
- [ ] Every toggle is a 44 px radio and the screen-reader label reads the learner's name.

## Phase 6 — Assessment, marks and reports (S8, S9)

- Assessments CRUD for HODs and teachers; the marks-entry Livewire component from the kit, wired to real models; paste-from-Excel; rubric-per-strand mode (`strand_ratings`); progress panel; submit → approval → lock.
- Report generation: CBE and KCSE A4 PDFs (S9), the screen version for parents, competencies, comments workflow (class teacher → principal), the `show_class_position` setting, and the verification code page.
- KCSE best-7 mean grade logic in a tested service.

**Acceptance**
- [ ] Unit tests: `Rubric::fromScore` boundaries (90 → EE1, 89 → EE2, 41 → ME2, 40 → AE1, 10 → BE2), KCSE grade boundaries, and the best-7 selection reproducing S9's example (8.86, B).
- [ ] Conflict test: two sessions edit the same mark; the second gets a conflict, not an overwrite.
- [ ] The PDF renders on A4, prints legibly in greyscale, and leaves out class position when disabled.

## Phase 7 — Fees and M-Pesa (S10, S11, S12 payment flow)

- Vote heads, fee structures with versions, draft → publish with an effect preview and principal approval for open terms (S11).
- Charges per learner per term; allocations by vote-head priority; waivers.
- `MpesaGateway` with the fake and Daraja (sandbox) drivers; STK push flow (S12 screens 1–4) with `wire:poll.3s.visible`, timeout at 90 s, and the failure variant; C2B validation and confirmation; idempotent callbacks.
- Auto-matching and the "Needs a match" queue (suggest, split between siblings, find learner).
- Bursar dashboard (S10); defaulters with the bulk SMS reminder (confirm modal); receipts as PDF and SMS.

**Acceptance**
- [ ] A fake STK success creates exactly one payment, allocates it, updates the balance and sends a receipt SMS; a replayed callback is ignored.
- [ ] Unmatched account text "chebet" suggests Chebet Kiprono.
- [ ] All money is stored as cents; no float arithmetic on money (assert in tests).
- [ ] The app never asks for an M-Pesa PIN anywhere (grep test on views for "PIN" input fields).

## Phase 8 — Parent portal (S12)

- Parent home: child switcher, fee card with Pay with M-Pesa, latest results, attendance grid, notices.
- Bottom nav pages: Fees (statement), Results (report list → S9 screen), Messages (notices inbox), More (language, theme, guardians, log out).
- Full Kiswahili coverage for every parent screen.

**Acceptance**
- [ ] At 360 px, in Kiswahili and dark mode: nothing truncates or overflows; every target is at least 44 px.
- [ ] A parent with two children switches between them, and every figure updates.
- [ ] Lighthouse mobile on parent home with throttled 3G: performance ≥ 85, accessibility ≥ 95, total transfer < 150 KB after the font is cached.

## Phase 8b — Learner app (S16, S17, S18)

Run with `/build-phase 8b`. Needs Phases 4, 6 and 9's timetable tables. If Phase 9 isn't done yet, build the `periods` and `timetable_slots` migrations here first.

- Learner login: admission number + password. The school issues the first password from the learner profile ("Issue learner login" prints a slip), and the learner must change it on first login. Throttle the same way as staff logins. Learner sessions expire after 30 minutes idle (shared phones).
- Assignments:
  - Tables: `assignments`, `assignment_stream`, `assignment_submissions`, `submission_files` (DATA_MODEL.md).
  - Teacher create/edit, submissions list, rubric marking with feedback, return, and "SMS guardians of non-submitters".
- **S16 Today:** current or next lesson card, day picker with timetable list, due-soon list, latest notice.
- **S17 Assignments:**
  - To do / Submitted / Marked tabs, subject chips, open assignment.
  - Photo or file submission resized in the browser, offline queue for submissions, edits until the deadline, late rules.
- **S18 Results:**
  - Released assessments only, overall band and distribution, core vs pathway groups.
  - Change since the previous assessment written in words, expandable teacher comments.
  - No fees and no class position anywhere in the learner app.
- Learner bottom nav from `config/navigation.php`.

**Acceptance**
- [ ] A learner sees only their own stream's timetable and assignments, and only their own results. A policy test covers another learner's ID.
- [ ] Results of an assessment that hasn't been approved are invisible to learners (and parents) until approval.
- [ ] Submitting a 4 MB phone photo uploads about 200 KB. Submitting offline queues it and sends it on reconnect. A submission after the deadline is flagged late or blocked according to `accepts_late_until`.
- [ ] No learner screen renders a fee amount or class position (a test greps the rendered HTML for "KES").
- [ ] At 360 px, in Kiswahili and dark mode: no overflow, and every target is at least 44 px.

## Phase 9 — Communication and timetable (S13, S14)

- SMS / notice composer (S13): audiences with live counts (dedupe, opt-outs), templates, EN/SW bodies, tokens, the counter (port `smsInfo` to PHP `App\Support\Sms` and test both agree), longest-value warning, schedule (no sends after 20:00), test send, confirm, batched jobs, delivery reports, and a "Sent" view.
- Timetable (S14): periods, slots, views by stream, teacher and room, clash detection (DB unique constraints + friendly errors), print A4, day picker on phones. Feed the teacher home lessons.

**Acceptance**
- [ ] The S13 example reproduces "214 learners → 241 guardian numbers" style counts from seed data and the 123-character English preview.
- [ ] A double-booked teacher can't be saved; the error names the other class.
- [ ] Credits are estimated before sending and recorded after.

## Phase 10 — Hardening

- Approvals inbox (marks, waivers, transfers, report comments, leave) feeding the dashboard card.
- Service worker: cache the shell, CSS, font and today's timetable only.
- `EC-####` error references with logging; 404/403/500 pages in the design language.
- Audit log for marks, fees and settings changes.
- Accessibility pass (axe on every page), performance pass, and the security review: policies on every Livewire action, mass assignment, webhook auth.

**Acceptance**
- [ ] axe reports no serious or critical issues on all S1–S18 pages.
- [ ] Every Livewire component has an authorization test.
- [ ] Every page passes at 360 px, 768 px and 1440 px with no horizontal scroll (Pest browser or Dusk screenshots).
