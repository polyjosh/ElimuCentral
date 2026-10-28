// Livewire 4 ships Alpine (with the focus plugin). Import its ESM build so our Alpine data
// and stores register before Alpine starts; the layout uses @livewireScriptConfig.
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

import { registerStores } from './ui/stores';
import { phoneInput, currencyInput } from './ui/inputs';
import { datePicker } from './ui/date-picker';
import { marksGrid } from './ui/marks-grid';
import { smsCounter } from './ui/sms-counter';

registerStores(Alpine);

Alpine.data('phoneInput', phoneInput);
Alpine.data('currencyInput', currencyInput);
Alpine.data('datePicker', datePicker);
Alpine.data('marksGrid', marksGrid);
Alpine.data('smsCounter', smsCounter);

Livewire.start();
