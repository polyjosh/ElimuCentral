// Tiny IndexedDB queue for edits made offline (marks, registers).
// Keyed by `${scope}:${id}` so only the latest edit per cell is kept.
// Cleared on logout (see README) so a shared phone never keeps another user's data.

const DB_NAME = 'elimucentral-offline';
const STORE = 'queue';

let dbPromise = null;

function db() {
  if (!dbPromise) {
    dbPromise = new Promise((resolve, reject) => {
      const request = indexedDB.open(DB_NAME, 1);
      request.onupgradeneeded = () => {
        const store = request.result.createObjectStore(STORE, { keyPath: 'key' });
        store.createIndex('scope', 'scope');
      };
      request.onsuccess = () => resolve(request.result);
      request.onerror = () => reject(request.error);
    });
  }
  return dbPromise;
}

function tx(mode, run) {
  return db().then((conn) => new Promise((resolve, reject) => {
    const transaction = conn.transaction(STORE, mode);
    const result = run(transaction.objectStore(STORE));
    transaction.oncomplete = () => resolve(result?.result ?? result);
    transaction.onerror = () => reject(transaction.error);
  }));
}

export const queue = {
  put(scope, id, data) {
    return tx('readwrite', (store) => store.put({ key: `${scope}:${id}`, scope, id, ...data, queuedAt: Date.now() }));
  },

  all(scope) {
    return tx('readonly', (store) => store.index('scope').getAll(scope));
  },

  remove(scope, ids) {
    return tx('readwrite', (store) => ids.forEach((id) => store.delete(`${scope}:${id}`)));
  },

  clearAll() {
    return tx('readwrite', (store) => store.clear());
  },
};
