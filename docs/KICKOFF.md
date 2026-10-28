# Starting the build with Claude Code in VS Code

## 1. Prerequisites

Docker (PHP 8.4, Composer, MySQL 8.4 and Redis run in containers through Laravel Sail), Node 20+, Git, VS Code, and Claude Code:

```bash
npm install -g @anthropic-ai/claude-code
```

## 2. Create the Laravel app and add this handoff

```bash
composer create-project laravel/laravel elimucentral
```

Then copy **everything in this handoff folder** into `elimucentral/`, including the hidden `.claude/` folder, and let it overwrite Laravel's `resources/css/app.css` and `resources/js/app.js`.

```bash
cd elimucentral
git init && git add -A && git commit -m "Laravel 13 + ElimuCentral handoff"
code .
```

## 3. Run Claude Code in the VS Code terminal

Open the integrated terminal (Ctrl+`) and start it:

```bash
claude
```

Claude Code reads `CLAUDE.md` automatically. Then run the phases in order, one per session, committing after each:

```
/build-phase 0
```

Check the result (`./vendor/bin/sail up -d` and `npm run dev`, then open http://localhost:8000/ui), commit, and continue with `/build-phase 1` up to `/build-phase 10` (the learner app is `/build-phase 8b`, between 8 and 9). Run `/clear` between phases to start each with a fresh context.

To review a finished screen against the design:

```
/check-screen S12
```

## 4. If you'd rather paste a prompt than use the slash commands

> Read CLAUDE.md and docs/BUILD_PLAN.md. Build Phase 0 exactly as described, run the tests, Pint and `npm run build`, and finish with each acceptance check marked done or not verified.

## Files in this handoff

| File | For |
|---|---|
| `CLAUDE.md` | Rules Claude Code follows on every task (stack, UI, language, formats, safety) |
| `docs/BUILD_PLAN.md` | 12 phases (0–10, plus 8b for the learner app) with acceptance checks |
| `docs/SCREENS.md` | Written spec of every screen in the design canvas (S1–S18) |
| `docs/DATA_MODEL.md` | Tables, M-Pesa and SMS interfaces, fictional demo seed |
| `README.md` | The UI kit: tokens and components |
| `.claude/commands/*` | `/build-phase N`, `/check-screen S#` |
| `.claude/settings.json` | Pre-approves tests, Pint, migrations and builds; blocks reading `.env` |
| `resources/**` (theme in `resources/css/theme.css`), `app/**`, `config/navigation.php` | The UI kit code |
