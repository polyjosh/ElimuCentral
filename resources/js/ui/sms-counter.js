// Live SMS length and segment counter for the "Send SMS / notice" composer.
// GSM-7: 160 chars in one SMS, 153 per part when split. Extension chars (€ [ ] { } ~ ^ | \) count double.
// Anything else (curly quotes, emoji) switches to UCS-2: 70 in one SMS, 67 per part.

const GSM_BASIC =
  '@£$¥èéùìòÇ\nØø\rÅåΔ_ΦΓΛΩΠΨΣΘΞÆæßÉ !"#¤%&\'()*+,-./0123456789:;<=>?' +
  '¡ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÑÜ§¿abcdefghijklmnopqrstuvwxyzäöñüà';
const GSM_EXTENDED = '^{}\\[~]|€\f';

export function smsInfo(text) {
  let units = 0;
  let unicode = false;

  for (const ch of text) {
    if (GSM_BASIC.includes(ch)) units += 1;
    else if (GSM_EXTENDED.includes(ch)) units += 2;
    else { unicode = true; break; }
  }

  if (unicode) {
    const length = text.length; // UTF-16 code units, as the network counts them
    const limit = length <= 70 ? 70 : 67;
    return { length, unicode: true, limit: 70, segments: Math.max(1, Math.ceil(length / limit)) };
  }

  return { length: units, unicode: false, limit: 160, segments: units <= 160 ? 1 : Math.ceil(units / 153) };
}

/** Replaces {tokens} with values: fill('Dear {name}', { name: 'Daniel' }). */
export function fill(template, values) {
  return template.replace(/\{(\w+)\}/g, (match, key) => (key in values ? String(values[key]) : match));
}

// x-data="smsCounter({ text: @js($template), longest: @js($longestValues), recipients: 241 })"
// `longest` holds the longest real value for each token, computed once on the server,
// so the warning reflects the worst case without sending every recipient to the browser.
export const smsCounter = ({ text = '', longest = {}, recipients = 0 } = {}) => ({
  text,
  longest,
  recipients,

  get info() {
    return smsInfo(fill(this.text, this.longest));
  },

  get percent() {
    return Math.min(100, Math.round((this.info.length / this.info.limit) * 100));
  },

  get overOne() {
    return this.info.segments > 1;
  },
});
