---
description: Build one phase of docs/BUILD_PLAN.md end to end
argument-hint: <phase, e.g. 0 or 8b>
---

Build **Phase $ARGUMENTS** of `docs/BUILD_PLAN.md`.

1. Read `CLAUDE.md`, the Phase $ARGUMENTS section of `docs/BUILD_PLAN.md`, and every screen (S-number) and table it mentions in `docs/SCREENS.md` and `docs/DATA_MODEL.md`.
2. Check that earlier phases are in place (migrations, routes, tests). If something the phase depends on is missing, stop and tell me what it is.
3. Post a short plan: the files you'll create or change, migrations, routes, Livewire components, and the tests you'll write. Then build it without waiting for approval, unless the plan needs a new package or a stack change.
4. Build screens from the kit's Blade components and tokens only (no raw colours, no `dark:` variants). Put every string through `__()` and add the Kiswahili to `lang/sw.json` in the same change.
5. Write the Pest tests the acceptance checks need, plus authorization tests for every new Livewire component and route.
6. Finish by running `php artisan migrate:fresh --seed`, `php artisan test`, `vendor/bin/pint` and `npm run build`. Fix anything that fails.
7. Summarise:
   - each acceptance check as ✅ or ⚠️ not verified (with the reason);
   - anything you changed in `docs/` or `CLAUDE.md`;
   - the exact URLs and demo logins to try it.
