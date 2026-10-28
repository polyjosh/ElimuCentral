# ElimuCentral UI kit

Drop-in Tailwind theme, Blade components and Alpine helpers that implement the
ElimuCentral design system (canvas: https://claude.ai/artifact/FJjtFFpYfPaxppzYauAEVa).
It is built for Laravel Blade, Livewire 4 and Alpine.js, renders on the server and needs no SPA.

## Requirements

- Laravel 13, Livewire 4 (bundles Alpine and its focus plugin)
- Tailwind CSS 4. The theme is in `resources/css/theme.css` (`@theme`); there is no `tailwind.config.js`. The kit was first written for Tailwind 3.4 and ported in Phase 0.
- Icons: [Lucide](https://lucide.dev) as inline SVG components.
  ```bash
  composer require mallardduck/blade-lucide-icons
  ```
  Components call icons as `<x-lucide-wallet />`, so you don't need an icon font or an extra request.

## Install

1. Copy the folders into your app (the paths match a standard Laravel app):

   | From this kit | Purpose |
   |---|---|
   | `resources/css/app.css` | Token variables for light and `.dark`, base styles, `.ec-control` / `.ec-group` |
   | `resources/css/theme.css` | Theme (`@theme`): semantic colours, ramps, type scale, radius, elevation, breakpoints. The default palette is off |
   | `resources/js/app.js`, `resources/js/ui/*` | Starts Livewire + Alpine; Alpine data and stores |
   | `resources/views/components/**` | Blade components (below) |
   | `resources/views/livewire/marks-entry.blade.php`, `app/Livewire/MarksEntry.php` | Marks-entry grid |
   | `app/Support/Rubric.php`, `app/Support/Format.php` | Rubric levels, KCSE grades, KES / +254 / DD/MM/YYYY |
   | `config/navigation.php` | Sidebar and bottom-nav items per role |

2. `vite.config.js` inputs: `resources/css/app.css`, `resources/js/app.js`.
3. The layout uses `@livewireScriptConfig` (not `@livewireScripts`): `app.js` imports Livewire's ESM build so Alpine data registers before Alpine starts.
4. Share layout data with a view composer: `$school`, `$schools`, `$terms`, `$currentTerm`, `$navCounts` (`pendingMarks`, `unmatchedPayments`, `notifications`).
5. Routes the shell posts to: `term.switch`, `school.switch`, `locale.switch`, `theme.switch`, `search`, `notifications.index`, `logout`, `profile`.

## Tokens

Every colour is a CSS variable holding space-separated RGB, mapped 1:1 to a Tailwind key, so `bg-primary/10` works.
Put `class="dark"` on `<html>` (the layout does this from `users.theme`) and the whole theme swaps. You don't need `dark:` variants.

| Tailwind | Use |
|---|---|
| `bg` `surface` `surface-2` `surface-3` | Page, cards, table headers, skeletons |
| `text` `text-2` `muted` `subtle` | Body, secondary, labels (7:1), placeholders (5.1:1) |
| `border` `border-strong` | Dividers / input outlines (3:1) |
| `primary` `-hover` `-fg` `-soft` `-soft-fg` `-text` | Green actions, active nav, links |
| `accent` `-hover` `-fg` `-soft` `-text` | Ochre highlights. Use `text-accent-text` for text, never `text-accent` |
| `success` `warning` `danger` `info` `plum` (+ `-bg`, `-bd`) | Status; plum = Arts & Sports pathway |
| `inverse` `inverse-fg` | Toasts, offline banner |
| `ring` | Focus: `outline-[3px] outline-offset-2 outline-ring` (global `:focus-visible`) |

Type: `text-xs` 12/16 · `sm` 14/20 · `base` 16/24 (minimum for parent screens) · `lg` 18 · `xl` 20 · `2xl` 24 · `3xl` 30 · `4xl` 36.
Use `tabular-nums` on every mark, amount, date and count.
Touch: `min-h-touch`, `h-touch`, `w-touch` = 44 px.

## Components

| Component | Notes |
|---|---|
| `<x-button variant size icon href loading loading-text>` | `loading="save"` disables the button and swaps the label while the Livewire action `save` runs. Use one primary per view. Reserve `sm` (36 px) for desktop toolbars. |
| `<x-input>` `<x-select>` `<x-checkbox>` `<x-segmented>` | Visible label, hint and error from `$errors`, wired with `aria-describedby` / `aria-invalid`. 16 px text so Android doesn't zoom. Validate on `blur`. |
| `<x-phone-input>` | Fixed `+254`; Alpine tidies typing and pasting. Store `Format::phoneE164($v)`. |
| `<x-currency-input>` | `KES` prefix. Raw while focused, `18,750.00` on blur. Store `Format::parseKes($v)` (integer cents). |
| `<x-date-input>` | Accepts typed DD/MM/YYYY (`20112026`, `20-11-26`) and has a Monday-first calendar with arrow-key, PageUp/PageDown and Esc support. Phones get the native picker. `wire:model` receives ISO dates. |
| `<x-data-table>` + `<x-data-table.card>` | Sticky header table from `md` up, card list below it. Put the same loop in both slots. |
| `<x-bulk-bar :count clear>` | Sticks above the bottom nav on phones. |
| `<x-card>` `<x-stat>` | Stat has `state="loading\|empty\|error"`. Use the loading state as a lazy Livewire `placeholder()`. |
| `<x-badge tone icon>` `<x-tabs>` `<x-breadcrumbs>` `<x-avatar>` | Tabs are links (`?tab=fees`). Avatars show initials, tinted from a hash of the name. |
| `<x-modal name>` `<x-drawer name>` | Open with `$dispatch('open-modal', 'name')` or `wire:model`. Focus is trapped, Esc closes, and focus returns to the trigger. On phones the modal becomes a bottom sheet. |
| `<x-toast-stack>` | `$this->dispatch('toast', message: '…', tone: 'success')`. Errors stay until dismissed. |
| `<x-offline-banner>` | Shown automatically when offline, with a count of waiting changes. |
| `<x-file-upload wire:model>` | Native input stays accessible; drag-and-drop and progress are enhancements. |
| `<x-empty-state>` | Covers empty, no-results and error states for any region. |
| `<x-rubric level>` `<x-rubric-picker>` | Shows level code, signal bars (4/3/2/1), border style and colour. The picker is a radio group; keys 1–8 jump to a level. |
| `<x-curriculum-badge>` `<x-pathway-badge>` `<x-kcse-grade>` | CBE ● vs 8-4-4 ◆. Each pathway has its own icon. KCSE grades are neutral. |
| `<x-term-switcher>` | The selected term lives in the session, set by a plain POST. |
| `<x-layouts.app>` + `shell/*` | Sidebar from `lg` up. Below that: app bar, bottom nav (parent / teacher / learner) and a language toggle on every screen. |

## Marks entry

`<livewire:marks-entry :assessment="$assessment" />`

- Alpine owns the cells, and `saveBatch()` is `#[Renderless]`, so saving never re-renders the table.
- Keys: Enter / ↓ moves to the next learner, ↑ goes back, `AB` marks absent, Esc undoes the cell.
- Invalid scores (over the total, not a number) are shown in red with a message and never sent.
- Every change goes to IndexedDB first. Changes are flushed every 3 s, when focus leaves the grid, and when the connection returns. Each change carries the mark's `updated_at`, so the server never silently overwrites a newer edit; conflicts come back and show a toast.
- The level preview is computed in the browser from `Rubric::CBE_SCALE`; the server recomputes it.

## Offline and shared phones

- Clear the offline queue on logout (`queue.clearAll()` from `resources/js/ui/offline-queue.js`) so a shared phone never keeps another user's drafts.
- Cache only the app shell, CSS, font and today's timetable in a service worker. Don't cache learner data.

## Not included

- The pages themselves (dashboard, bursar, SMS composer and so on). They're compositions of these components; see the canvas for layouts.
- `sms-counter.js` provides the counting logic (GSM-7 / UCS-2, 160/153 and 70/67); build the composer view around `x-data="smsCounter({...})"`.
- Global search results page, notifications page and the controllers behind the shell's POST routes.
