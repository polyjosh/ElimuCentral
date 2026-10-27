// ElimuCentral — Tailwind CSS v3 theme.
// Semantic colours read CSS variables (space-separated RGB) from resources/css/app.css,
// so `class="dark"` on <html> swaps the whole theme without dark: variants.
const v = (name) => `rgb(var(--${name}) / <alpha-value>)`;

/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './app/Livewire/**/*.php',
    './app/View/**/*.php',
    './app/Support/**/*.php',
  ],
  theme: {
    screens: {
      xs: '360px',
      sm: '640px',
      md: '768px',  // tables stop collapsing into cards
      lg: '1024px', // sidebar appears, bottom nav hides
      xl: '1280px',
      '2xl': '1440px',
    },
    extend: {
      fontFamily: {
        sans: ['Inter', 'system-ui', '-apple-system', 'Segoe UI', 'Roboto', 'sans-serif'],
      },
      colors: {
        bg: v('bg'),
        surface: { DEFAULT: v('surface'), 2: v('surface-2'), 3: v('surface-3') },
        border: { DEFAULT: v('border'), strong: v('border-strong') },
        text: { DEFAULT: v('text'), 2: v('text-2') },
        muted: v('muted'),
        subtle: v('subtle'),
        ring: v('ring'),
        primary: {
          DEFAULT: v('primary'),
          hover: v('primary-hover'),
          fg: v('primary-fg'),
          soft: v('primary-soft'),
          'soft-fg': v('primary-soft-fg'),
          text: v('primary-text'),
        },
        accent: {
          DEFAULT: v('accent'),
          hover: v('accent-hover'),
          fg: v('accent-fg'),
          soft: v('accent-soft'),
          text: v('accent-text'),
        },
        success: { DEFAULT: v('success'), bg: v('success-bg'), bd: v('success-bd') },
        warning: { DEFAULT: v('warning'), bg: v('warning-bg'), bd: v('warning-bd') },
        danger: { DEFAULT: v('danger'), solid: v('danger-solid'), bg: v('danger-bg'), bd: v('danger-bd') },
        info: { DEFAULT: v('info'), bg: v('info-bg'), bd: v('info-bd') },
        plum: { DEFAULT: v('plum'), bg: v('plum-bg'), bd: v('plum-bd') },
        inverse: { DEFAULT: v('inverse-bg'), fg: v('inverse-fg') },

        // Raw brand ramps — for charts and the logo only. UI uses the semantic tokens above.
        green: {
          50: '#EEF7F1', 100: '#D5EDDD', 200: '#AADABB', 300: '#74C093', 400: '#3FA06B', 500: '#1F8550',
          600: '#146B3F', 700: '#0F5634', 800: '#0C442A', 900: '#0A3622', 950: '#051F13',
        },
        ochre: {
          50: '#FDF7EC', 100: '#FBEFD8', 200: '#F4D293', 300: '#EDB55A', 400: '#E39A2E', 500: '#C97F17',
          600: '#A35F00', 700: '#844C12', 800: '#6B3D14', 900: '#593314', 950: '#2F1B0A',
        },
        stone: {
          25: '#FAFAF8', 50: '#F5F5F2', 100: '#EBEBE6', 200: '#DAD9D3', 300: '#A8A79F', 400: '#8A8981',
          500: '#6E6D66', 600: '#5A5953', 700: '#45443F', 800: '#2E2D2A', 900: '#1C1B19',
        },
      },
      fontSize: {
        xs: ['12px', '16px'],
        sm: ['14px', '20px'],
        base: ['16px', '24px'],
        lg: ['18px', '28px'],
        xl: ['20px', '28px'],
        '2xl': ['24px', '32px'],
        '3xl': ['30px', '38px'],
        '4xl': ['36px', '44px'],
      },
      borderRadius: {
        sm: '4px',
        md: '6px',
        DEFAULT: '8px',
        lg: '12px',
        xl: '16px',
      },
      boxShadow: {
        sm: 'var(--elevation-sm)',
        md: 'var(--elevation-md)',
        lg: 'var(--elevation-lg)',
      },
      minHeight: { touch: '44px' },
      minWidth: { touch: '44px' },
      height: { touch: '44px' },
      width: { touch: '44px' },
      maxWidth: { content: '1360px' },
    },
  },
  plugins: [],
};
