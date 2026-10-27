// Global Alpine stores: network status (offline banner) and toasts.

export function registerStores(Alpine) {
  Alpine.store('net', {
    online: navigator.onLine,
    // Number of changes saved on this device but not yet sent (marks, registers).
    pending: 0,
  });

  window.addEventListener('online', () => { Alpine.store('net').online = true; });
  window.addEventListener('offline', () => { Alpine.store('net').online = false; });

  Alpine.store('toasts', {
    items: [],
    nextId: 1,

    push({ message, tone = 'success' }) {
      const id = this.nextId++;
      this.items.push({ id, message, tone });
      // Errors stay until dismissed; everything else hides after 6 s.
      if (tone !== 'error') setTimeout(() => this.dismiss(id), 6000);
    },

    dismiss(id) {
      this.items = this.items.filter((t) => t.id !== id);
    },
  });

  // Livewire: $this->dispatch('toast', message: '…', tone: 'success')
  // Alpine:   $dispatch('toast', { message: '…', tone: 'error' })
  window.addEventListener('toast', (event) => Alpine.store('toasts').push(event.detail ?? {}));
}
