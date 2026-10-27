// Alpine data for the marks-entry grid (resources/views/livewire/marks-entry.blade.php).
//
// - Enter / ↓ next learner, ↑ previous, Esc restores the saved value, "AB" = absent.
// - Validates against the total before anything is sent; invalid cells are never saved.
// - Every valid change goes to IndexedDB first, then is flushed to Livewire's saveBatch()
//   every 3 s, on blur of the grid, and when the connection comes back.
//   One request per batch, never per keystroke.

import { queue } from './offline-queue';

const FLUSH_MS = 3000;

export const marksGrid = ({ scope, outOf, scale, rows, messages = {} }) => ({
  outOf,
  scale,       // [{ code, min, max }] from App\Support\Rubric::CBE_SCALE (or the school's own)
  rows,        // { [learnerId]: { value, saved, version, level, error } }
  status: 'saved', // saved | saving | offline | error
  savedAt: null,
  pending: 0,
  timer: null,

  async init() {
    // Restore edits that never reached the server (e.g. phone went offline, tab closed).
    const queued = await queue.all(scope);
    queued.forEach((item) => {
      if (this.rows[item.id]) {
        this.rows[item.id].value = item.value;
        this.evaluate(item.id);
      }
    });
    this.setPending(queued.length);

    this.timer = setInterval(() => this.flush(), FLUSH_MS);
    window.addEventListener('online', () => this.flush());
    window.addEventListener('offline', () => { this.status = 'offline'; });
    if (!navigator.onLine) this.status = 'offline';
  },

  destroy() {
    clearInterval(this.timer);
  },

  // ---- keyboard ----------------------------------------------------------------------

  onKeydown(event, id) {
    if (event.key === 'Enter' || event.key === 'ArrowDown') {
      event.preventDefault();
      this.move(event.target, 1);
    } else if (event.key === 'ArrowUp') {
      event.preventDefault();
      this.move(event.target, -1);
    } else if (event.key === 'Escape') {
      this.rows[id].value = this.rows[id].saved;
      this.changed(id);
    }
  },

  move(from, step) {
    const inputs = [...this.$root.querySelectorAll('input[data-mark]')];
    const next = inputs[inputs.indexOf(from) + step];
    if (next) {
      next.focus();
      next.select();
    }
  },

  // ---- validation --------------------------------------------------------------------

  levelFor(score) {
    const pct = Math.round((score / this.outOf) * 100);
    return this.scale.find((band) => pct >= band.min && pct <= band.max)?.code ?? null;
  },

  evaluate(id) {
    const row = this.rows[id];
    const raw = String(row.value ?? '').trim().toUpperCase();
    row.error = '';
    row.level = null;

    if (raw === '' || raw === 'AB') return true;

    const score = Number(raw);
    if (!Number.isFinite(score) || score < 0) {
      row.error = messages.number ?? 'Enter a number or AB';
      return false;
    }
    if (score > this.outOf) {
      row.error = (messages.max ?? 'Max is :max').replace(':max', this.outOf);
      return false;
    }

    row.level = this.levelFor(score);
    return true;
  },

  async changed(id) {
    if (!this.evaluate(id)) return;
    const row = this.rows[id];
    const raw = String(row.value ?? '').trim().toUpperCase();
    await queue.put(scope, id, { value: raw, version: row.version });
    this.setPending((await queue.all(scope)).length);
  },

  // ---- saving ------------------------------------------------------------------------

  setPending(n) {
    this.pending = n;
    this.$store.net.pending = n;
  },

  async flush() {
    if (this.status === 'saving') return;
    if (!navigator.onLine) {
      this.status = 'offline';
      return;
    }

    const items = await queue.all(scope);
    if (items.length === 0) {
      if (this.status !== 'error') this.status = 'saved';
      return;
    }

    this.status = 'saving';
    try {
      const result = await this.$wire.saveBatch(items.map(({ id, value, version }) => ({ id, value, version })));

      Object.entries(result.versions ?? {}).forEach(([id, version]) => {
        if (this.rows[id]) {
          this.rows[id].version = version;
          this.rows[id].saved = this.rows[id].value;
        }
      });

      await queue.remove(scope, [...(result.saved ?? []), ...(result.conflicts ?? [])].map(String));

      (result.conflicts ?? []).forEach((id) => {
        const row = this.rows[id];
        if (!row) return;
        row.value = result.current?.[id] ?? row.saved;
        row.saved = row.value;
        this.evaluate(id);
      });
      if ((result.conflicts ?? []).length) {
        window.dispatchEvent(new CustomEvent('toast', {
          detail: { tone: 'error', message: messages.conflict ?? 'Someone else changed some marks. Their values are shown — check and re-enter if needed.' },
        }));
      }

      this.setPending((await queue.all(scope)).length);
      this.status = 'saved';
      this.savedAt = new Date().toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
    } catch (error) {
      this.status = navigator.onLine ? 'error' : 'offline';
    }
  },

  // ---- summary -----------------------------------------------------------------------

  get entered() {
    return Object.values(this.rows).filter((r) => String(r.value ?? '').trim() !== '' && !r.error).length;
  },

  get total() {
    return Object.keys(this.rows).length;
  },
});
