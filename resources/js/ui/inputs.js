// Formatting helpers for <x-phone-input> and <x-currency-input>.
// They only tidy what the user sees; the server re-parses with App\Support\Format.

/** Groups a Kenyan mobile number as "712 000 481", dropping +254 / 254 / a leading 0. */
export function formatPhone(raw) {
  let digits = String(raw).replace(/\D/g, '');
  if (digits.startsWith('254')) digits = digits.slice(3);
  digits = digits.replace(/^0+/, '').slice(0, 9);
  return [digits.slice(0, 3), digits.slice(3, 6), digits.slice(6)].filter(Boolean).join(' ');
}

export const phoneInput = () => ({
  format(el) {
    // Only reformat while typing at the end, so editing the middle never moves the caret.
    const atEnd = el.selectionStart === el.value.length;
    const next = formatPhone(el.value);
    if (atEnd && next !== el.value) el.value = next;
  },
});

const kes = new Intl.NumberFormat('en-KE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

/** "18750" => "18,750.00"; returns null when the text isn't a number. */
export function formatKes(raw) {
  const clean = String(raw).replace(/[^0-9.]/g, '');
  if (clean === '' || (clean.match(/\./g) || []).length > 1) return null;
  return kes.format(Number(clean));
}

export const currencyInput = () => ({
  unformat(el) {
    el.value = el.value.replace(/,/g, '').replace(/\.00$/, '');
  },

  formatValue(el) {
    const formatted = formatKes(el.value);
    if (formatted !== null && formatted !== el.value) {
      el.value = formatted;
      el.dispatchEvent(new Event('input', { bubbles: true }));
    }
  },
});
