// Alpine data for <x-date-input>: DD/MM/YYYY text entry plus a Monday-first calendar.
// The ISO value (YYYY-MM-DD) is written to a hidden input that carries wire:model.

const pad = (n) => String(n).padStart(2, '0');
const toIso = (y, m, d) => `${y}-${pad(m)}-${pad(d)}`;
const fromIso = (iso) => {
  const [y, m, d] = iso.split('-').map(Number);
  return new Date(y, m - 1, d);
};
const isoOf = (date) => toIso(date.getFullYear(), date.getMonth() + 1, date.getDate());
const display = (iso) => {
  const [y, m, d] = iso.split('-');
  return `${d}/${m}/${y}`;
};

/** "20112026", "20/11/2026", "20-11-26" => "2026-11-20"; null if unreadable; false if not a real date. */
export function parseDisplayDate(text) {
  let digits = String(text).replace(/\D/g, '');
  if (digits.length === 6) digits = digits.slice(0, 4) + '20' + digits.slice(4);
  if (digits.length !== 8) return null;

  const d = Number(digits.slice(0, 2));
  const m = Number(digits.slice(2, 4));
  const y = Number(digits.slice(4));
  const date = new Date(y, m - 1, d);

  if (date.getFullYear() !== y || date.getMonth() !== m - 1 || date.getDate() !== d) return false;
  return toIso(y, m, d);
}

export const datePicker = ({ min = null, max = null } = {}) => ({
  open: false,
  invalid: false,
  invalidMessage: '',
  selectedIso: null,
  focusIso: null,
  view: null, // first day of the visible month
  weekdays: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],

  init() {
    const iso = this.$refs.iso.value;
    if (iso) this.setIso(iso, false);
    const base = this.selectedIso ? fromIso(this.selectedIso) : new Date();
    this.view = new Date(base.getFullYear(), base.getMonth(), 1);
    if (min) this.$refs.native.min = min;
    if (max) this.$refs.native.max = max;
  },

  get locale() {
    return document.documentElement.lang || 'en';
  },

  get monthLabel() {
    return this.view ? this.view.toLocaleDateString(this.locale, { month: 'long', year: 'numeric' }) : '';
  },

  get cells() {
    if (!this.view) return [];
    const todayIso = isoOf(new Date());
    const first = this.view;
    const offset = (first.getDay() + 6) % 7; // Monday = 0
    const start = new Date(first.getFullYear(), first.getMonth(), 1 - offset);

    return Array.from({ length: 42 }, (_, i) => {
      const date = new Date(start.getFullYear(), start.getMonth(), start.getDate() + i);
      const iso = isoOf(date);
      return {
        iso,
        day: date.getDate(),
        inMonth: date.getMonth() === first.getMonth(),
        isToday: iso === todayIso,
        isSelected: iso === this.selectedIso,
        disabled: (min && iso < min) || (max && iso > max),
        label: date.toLocaleDateString(this.locale, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }),
      };
    });
  },

  toggle() {
    // Phones: the native picker is easier with a thumb.
    const native = this.$refs.native;
    if (window.matchMedia('(max-width: 767px)').matches && typeof native.showPicker === 'function') {
      native.value = this.selectedIso || '';
      native.showPicker();
      return;
    }
    this.open ? this.close() : this.openCalendar();
  },

  openCalendar() {
    this.focusIso = this.selectedIso || isoOf(new Date());
    const f = fromIso(this.focusIso);
    this.view = new Date(f.getFullYear(), f.getMonth(), 1);
    this.open = true;
    this.$nextTick(() => this.focusCell());
  },

  close() {
    this.open = false;
    this.$refs.text.focus();
  },

  shiftMonth(n) {
    this.view = new Date(this.view.getFullYear(), this.view.getMonth() + n, 1);
    const f = fromIso(this.focusIso || isoOf(this.view));
    const lastDay = new Date(this.view.getFullYear(), this.view.getMonth() + 1, 0).getDate();
    this.focusIso = toIso(this.view.getFullYear(), this.view.getMonth() + 1, Math.min(f.getDate(), lastDay));
  },

  focusCell() {
    this.$root.querySelector(`[data-iso="${this.focusIso}"]`)?.focus();
  },

  onGridKey(event) {
    const steps = { ArrowLeft: -1, ArrowRight: 1, ArrowUp: -7, ArrowDown: 7 };
    if (event.key in steps) {
      event.preventDefault();
      const f = fromIso(this.focusIso);
      const next = new Date(f.getFullYear(), f.getMonth(), f.getDate() + steps[event.key]);
      this.focusIso = isoOf(next);
      if (next.getMonth() !== this.view.getMonth() || next.getFullYear() !== this.view.getFullYear()) {
        this.view = new Date(next.getFullYear(), next.getMonth(), 1);
      }
      this.$nextTick(() => this.focusCell());
    } else if (event.key === 'PageUp' || event.key === 'PageDown') {
      event.preventDefault();
      this.shiftMonth(event.key === 'PageUp' ? -1 : 1);
      this.$nextTick(() => this.focusCell());
    }
  },

  pick(iso) {
    this.setIso(iso, true);
    this.close();
  },

  pickNative(value) {
    if (value) this.setIso(value, true);
  },

  commitText() {
    const text = this.$refs.text.value.trim();

    if (text === '') {
      if (this.selectedIso !== null) {
        this.selectedIso = null;
        this.$refs.iso.value = '';
        this.$refs.iso.dispatchEvent(new Event('input', { bubbles: true }));
      }
      this.invalid = false;
      return;
    }

    const iso = parseDisplayDate(text);
    if (iso === null) return this.fail('Use DD/MM/YYYY, for example 20/11/2026.');
    if (iso === false) return this.fail("That date doesn't exist.");
    if ((min && iso < min) || (max && iso > max)) {
      return this.fail(`Choose a date between ${min ? display(min) : '…'} and ${max ? display(max) : '…'}.`);
    }

    this.setIso(iso, iso !== this.selectedIso);
  },

  fail(message) {
    this.invalid = true;
    this.invalidMessage = message;
  },

  setIso(iso, notify) {
    this.selectedIso = iso;
    this.invalid = false;
    this.$refs.text.value = display(iso);
    this.$refs.iso.value = iso;
    const d = fromIso(iso);
    this.view = new Date(d.getFullYear(), d.getMonth(), 1);
    if (notify) this.$refs.iso.dispatchEvent(new Event('input', { bubbles: true }));
  },
});
