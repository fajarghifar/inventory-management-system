/**
 * IndexedDB Storage & Synchronization Engine for Inventory Management PWA
 * Database: InventoryPwaDB
 */

(function (window) {
    'use strict';

    const DB_NAME = 'InventoryPwaDB';
    const DB_VERSION = 1;
    const SALES_STORE = 'offline_sales';
    const CATALOG_STORE = 'product_catalog';

    let dbInstance = null;

    function openDatabase() {
        if (dbInstance) {
            return Promise.resolve(dbInstance);
        }

        return new Promise((resolve, reject) => {
            const request = indexedDB.open(DB_NAME, DB_VERSION);

            request.onupgradeneeded = (event) => {
                const db = event.target.result;

                // Store for offline sales transactions
                if (!db.objectStoreNames.contains(SALES_STORE)) {
                    const salesStore = db.createObjectStore(SALES_STORE, { keyPath: 'uuid' });
                    salesStore.createIndex('status', 'status', { unique: false });
                    salesStore.createIndex('created_at', 'created_at', { unique: false });
                }

                // Store for product catalog
                if (!db.objectStoreNames.contains(CATALOG_STORE)) {
                    const catalogStore = db.createObjectStore(CATALOG_STORE, { keyPath: 'id' });
                    catalogStore.createIndex('sku', 'sku', { unique: false });
                    catalogStore.createIndex('name', 'name', { unique: false });
                }
            };

            request.onsuccess = (event) => {
                dbInstance = event.target.result;
                resolve(dbInstance);
            };

            request.onerror = (event) => {
                console.error('[PWA DB] Error opening IndexedDB:', event.target.error);
                reject(event.target.error);
            };
        });
    }

    const PwaDB = {
        /**
         * Cache product catalog for offline search
         */
        async cacheProducts(products) {
            if (!Array.isArray(products) || products.length === 0) return;
            const db = await openDatabase();
            return new Promise((resolve, reject) => {
                const tx = db.transaction(CATALOG_STORE, 'readwrite');
                const store = tx.objectStore(CATALOG_STORE);
                products.forEach(item => store.put(item));
                tx.oncomplete = () => resolve(true);
                tx.onerror = () => reject(tx.error);
            });
        },

        /**
         * Search products offline from cached catalog
         */
        async searchProducts(query) {
            const db = await openDatabase();
            return new Promise((resolve, reject) => {
                const tx = db.transaction(CATALOG_STORE, 'readonly');
                const store = tx.objectStore(CATALOG_STORE);
                const req = store.getAll();

                req.onsuccess = () => {
                    const all = req.result || [];
                    if (!query || query.trim() === '') {
                        return resolve(all.slice(0, 50));
                    }
                    const q = query.toLowerCase().trim();
                    const filtered = all.filter(p => {
                        const nameMatch = p.name && p.name.toLowerCase().includes(q);
                        const skuMatch = p.sku && p.sku.toLowerCase().includes(q);
                        return nameMatch || skuMatch;
                    });
                    resolve(filtered.slice(0, 50));
                };
                req.onerror = () => reject(req.error);
            });
        },

        /**
         * Save a sale offline with generated UUID and 'pending' status
         */
        async saveOfflineSale(payload) {
            const db = await openDatabase();
            const uuid = payload.offline_uuid || (crypto.randomUUID ? crypto.randomUUID() : 'offline-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9));

            const record = {
                uuid: uuid,
                payload: { ...payload, offline_uuid: uuid },
                status: 'pending', // 'pending' | 'synchronizing' | 'synced' | 'requires_review'
                created_at: new Date().toISOString(),
                attempts: 0,
                last_error: null,
                invoice_number: null,
                synced_at: null
            };

            return new Promise((resolve, reject) => {
                const tx = db.transaction(SALES_STORE, 'readwrite');
                const store = tx.objectStore(SALES_STORE);
                store.put(record);
                tx.oncomplete = () => {
                    window.dispatchEvent(new CustomEvent('pwa-sales-updated', { detail: { uuid } }));
                    resolve(record);
                };
                tx.onerror = () => reject(tx.error);
            });
        },

        /**
         * Get all pending or review-required sales
         */
        async getPendingSales() {
            const db = await openDatabase();
            return new Promise((resolve, reject) => {
                const tx = db.transaction(SALES_STORE, 'readonly');
                const store = tx.objectStore(SALES_STORE);
                const req = store.getAll();

                req.onsuccess = () => {
                    const all = req.result || [];
                    const pending = all.filter(s => s.status === 'pending' || s.status === 'requires_review' || s.status === 'synchronizing');
                    resolve(pending);
                };
                req.onerror = () => reject(req.error);
            });
        },

        /**
         * Get all recorded offline sales (audit trail)
         */
        async getAllSales() {
            const db = await openDatabase();
            return new Promise((resolve, reject) => {
                const tx = db.transaction(SALES_STORE, 'readonly');
                const store = tx.objectStore(SALES_STORE);
                const req = store.getAll();

                req.onsuccess = () => {
                    const all = req.result || [];
                    // Sort descending by created_at
                    all.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                    resolve(all);
                };
                req.onerror = () => reject(req.error);
            });
        },

        /**
         * Update an offline sale record
         */
        async updateSale(uuid, updates) {
            const db = await openDatabase();
            return new Promise((resolve, reject) => {
                const tx = db.transaction(SALES_STORE, 'readwrite');
                const store = tx.objectStore(SALES_STORE);
                const req = store.get(uuid);

                req.onsuccess = () => {
                    if (!req.result) return resolve(null);
                    const updated = { ...req.result, ...updates };
                    store.put(updated);
                };

                tx.oncomplete = () => {
                    window.dispatchEvent(new CustomEvent('pwa-sales-updated', { detail: { uuid } }));
                    resolve(true);
                };
                tx.onerror = () => reject(tx.error);
            });
        },

        /**
         * Delete an offline sale record (if cancelled or discarded by user)
         */
        async deleteSale(uuid) {
            const db = await openDatabase();
            return new Promise((resolve, reject) => {
                const tx = db.transaction(SALES_STORE, 'readwrite');
                const store = tx.objectStore(SALES_STORE);
                store.delete(uuid);

                tx.oncomplete = () => {
                    window.dispatchEvent(new CustomEvent('pwa-sales-updated', { detail: { uuid } }));
                    resolve(true);
                };
                tx.onerror = () => reject(tx.error);
            });
        },

        /**
         * Synchronize pending offline transactions with the Laravel server
         */
        async syncPendingSales(submitUrl, csrfToken) {
            if (!navigator.onLine) {
                console.log('[PWA DB] Device is offline, skipping sync.');
                return { synced: 0, conflicts: 0, pending: 0 };
            }

            const pending = await this.getPendingSales();
            if (pending.length === 0) {
                return { synced: 0, conflicts: 0, pending: 0 };
            }

            let syncedCount = 0;
            let conflictCount = 0;

            for (const item of pending) {
                // If it already requires user review, do not auto-retry without manual prompt
                if (item.status === 'requires_review') {
                    conflictCount++;
                    continue;
                }

                await this.updateSale(item.uuid, { status: 'synchronizing', attempts: (item.attempts || 0) + 1 });

                try {
                    const token = csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const res = await fetch(submitUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify(item.payload)
                    });

                    const data = await res.json().catch(() => ({}));

                    if (res.ok && (data.success || data.data)) {
                        // Successfully recorded by server
                        await this.updateSale(item.uuid, {
                            status: 'synced',
                            invoice_number: data.data?.invoice_number || data.invoice_number,
                            synced_at: new Date().toISOString(),
                            last_error: null
                        });
                        syncedCount++;
                        window.dispatchEvent(new CustomEvent('pwa-sale-synced', {
                            detail: { uuid: item.uuid, invoice: data.data?.invoice_number }
                        }));
                    } else if (res.status === 409 || data.conflict || (data.message && data.message.toLowerCase().includes('stock'))) {
                        // Conflict occurred: insufficient stock or business rule violation
                        const errorMsg = data.message || 'Stock conflict or validation error';
                        await this.updateSale(item.uuid, {
                            status: 'requires_review',
                            last_error: errorMsg
                        });
                        conflictCount++;
                        window.dispatchEvent(new CustomEvent('pwa-sale-conflict', {
                            detail: { uuid: item.uuid, message: errorMsg }
                        }));
                    } else {
                        // General server error, retry later
                        await this.updateSale(item.uuid, {
                            status: 'pending',
                            last_error: data.message || `Server returned ${res.status}`
                        });
                    }
                } catch (networkError) {
                    // Still offline or connection dropped during sync
                    console.warn('[PWA DB] Network error during sale sync:', networkError);
                    await this.updateSale(item.uuid, {
                        status: 'pending',
                        last_error: 'Network connection dropped during sync'
                    });
                    break; // Stop loop if network dropped
                }
            }

            return { synced: syncedCount, conflicts: conflictCount, pending: pending.length - syncedCount };
        }
    };

    window.PwaDB = PwaDB;
})(window);

