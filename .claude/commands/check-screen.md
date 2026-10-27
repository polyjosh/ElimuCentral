---
description: Review one screen against its spec in docs/SCREENS.md
argument-hint: <screen id, e.g. S12 or S17>
---

Review screen **$ARGUMENTS** against its section in `docs/SCREENS.md` and the UI rules in `CLAUDE.md`. Don't change code yet.

Check and report, citing `file:line` for each issue:
- Missing or different content, actions, states (loading, empty, error, offline) or copy compared with the spec.
- Raw colours, `dark:` variants or one-off markup where a kit component exists.
- Strings not wrapped in `__()`, or missing from `lang/sw.json`.
- Touch targets under 44 px, missing labels or `aria-*`, colour used as the only cue.
- Layout at 360 px: fixed widths, overflow, text that would truncate in Kiswahili.
- Money, phone and date values not going through `App\Support\Format`.
- Missing policy checks on routes or Livewire actions.
- Learner screens (S16–S18): any fee amount or class position shown.

End with a prioritised fix list and ask whether to apply it.
