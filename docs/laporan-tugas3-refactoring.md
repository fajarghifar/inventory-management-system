# LAPORAN TUGAS 3 — EVOLUSI PERANGKAT LUNAK
## Refactoring dan Testing Modul Purchase
**Studi Kasus:** Inventory Management System  
**Kelompok 8:** Nama Anggota 1 (NRP), Nama Anggota 2 (NRP), Nama Anggota 3 (NRP)  

---

## Daftar Isi
1. [Pendahuluan](#1-pendahuluan)  
   1.1 [Studi Kasus](#11-studi-kasus)  
   1.2 [Cakupan Refactoring](#12-cakupan-refactoring)  
2. [Kondisi Kode Sebelum Refactoring](#2-kondisi-kode-sebelum-refactoring)  
   2.1 [Identifikasi Code Smells](#21-identifikasi-code-smells)  
   2.2 [Kompleksitas](#22-kompleksitas)  
   2.3 [Duplikasi Kode](#23-duplikasi-kode)  
   2.4 [Masalah Struktur Kode](#24-masalah-struktur-kode)  
3. [Strategi Refactoring](#3-strategi-refactoring)  
   3.1 [Prinsip](#31-prinsip)  
   3.2 [Pemetaan Smell ke Teknik](#32-pemetaan-smell-ke-teknik)  
   3.3 [Urutan Pengerjaan](#33-urutan-pengerjaan)  
   3.4 [Di Luar Cakupan](#34-di-luar-cakupan)  
4. [Aktivitas Refactoring](#4-aktivitas-refactoring)  
   4.1 [R0: Characterization Test sebagai Jaring Pengaman](#41-r0-characterization-test-sebagai-jaring-pengaman)  
   4.2 [R1: Menghapus Validasi Ganda](#42-r1-menghapus-validasi-ganda)  
   4.3 [R2: Extract Method — Upload Bukti Pembayaran](#43-r2-extract-method--upload-bukti-pembayaran)  
   4.4 [R3: Extract Method — Penanganan Error Controller](#44-r3-extract-method--penanganan-error-controller)  
   4.5 [R4: Memindahkan Aturan Status ke Enum](#45-r4-memindahkan-aturan-status-ke-enum)  
   4.6 [R5: Extract Method — Validasi Referensi Wajib](#46-r5-extract-method--validasi-referensi-wajib)  
   4.7 [R6: Memecah markAsReceived](#47-r6-memecah-markasreceived)  
   4.8 [R7: Pull Up — Aturan Validasi Form Pembelian](#48-r7-pull-up--aturan-validasi-form-pembelian)  
5. [Testing dan Validasi](#5-testing-dan-validasi)  
   5.1 [Lingkungan Test](#51-lingkungan-test)  
   5.2 [Characterization Test](#52-characterization-test)  
   5.3 [Hasil Eksekusi Test](#53-hasil-eksekusi-test)  
   5.4 [Validasi Manual lewat UI](#54-validasi-manual-lewat-ui)  
6. [Kesimpulan dan Rekomendasi](#6-kesimpulan-dan-rekomendasi)  
   6.1 [Kesimpulan](#61-kesimpulan)  
   6.2 [Temuan Perilaku yang Dipertahankan](#62-temuan-perilaku-yang-dipertahankan)  
   6.3 [Rekomendasi Pengembangan Berikutnya](#63-rekomendasi-pengembangan-berikutnya)  
[Daftar Pustaka](#daftar-pustaka)  

---

## 1 Pendahuluan

### 1.1 Studi Kasus
Studi kasus yang dipakai adalah **Inventory Management System**, aplikasi web berbasis Laravel 12, Livewire 3, Alpine.js, dan Tailwind CSS. Aplikasi ini memiliki modul master data (produk, kategori, satuan, pelanggan, pemasok), penjualan (POS), pembelian (purchase order), keuangan (cash flow ledger), dan dashboard. Arsitekturnya berlapis: controller dan komponen Livewire memanggil kelas service, data dipindahkan lewat DTO, dan status transaksi direpresentasikan dengan PHP enum.

### 1.2 Cakupan Refactoring
Sistem ini cukup besar (sekitar 290 berkas, lebih dari 6.000 baris PHP di folder `app/` saja), sehingga refactoring seluruh sistem tidak realistis dalam satu tugas dan sulit didemokan dalam 15 menit. Refactoring dibatasi pada modul **Purchase (Pembelian)** dengan alasan berikut:
1. **Konsentrasi masalah tinggi.** Modul ini memuat method terpanjang dan paling bercabang di lapisan service (`markAsReceived`), duplikasi pola try/catch terbanyak di satu controller, serta aturan status yang tersebar di empat lapisan (service, controller, tabel Livewire, view Blade).
2. **Batas modul jelas.** Perubahan cukup menyentuh 8 berkas yang saling terkait, tanpa mengubah skema database, route, atau modul lain.
3. **Mudah didemokan lewat UI.** Alur Draft → Ordered → Received → Paid (dan Cancelled → Restore) terlihat jelas di layar, termasuk efeknya pada stok produk dan catatan keuangan, sehingga mudah ditunjukkan bahwa perilaku sebelum dan sesudah refactoring sama.

#### Tabel 1: Berkas dalam cakupan refactoring.
| Berkas | Baris Awal | Peran |
|---|---|---|
| `app/Services/PurchaseService.php` | 248 | Logika bisnis: buat/ubah/hapus, transisi status, update stok dan harga. |
| `app/Http/Controllers/PurchaseController.php` | 212 | Menangani request HTTP, upload bukti, redirect dan pesan flash. |
| `app/Enums/PurchaseStatus.php` | 34 | Definisi status beserta label dan warna. |
| `app/Http/Requests/StorePurchaseRequest.php` | 40 | Validasi form tambah pembelian. |
| `app/Http/Requests/UpdatePurchaseRequest.php` | 45 | Validasi form ubah pembelian. |
| `app/Http/Requests/PurchaseRequest.php` | Baru | Kelas abstrak bersama untuk validasi form. |
| `app/Livewire/Purchases/PurchaseTable.php` | 231 | Tabel daftar pembelian (PowerGrid) beserta tombol aksi. |
| `resources/views/purchases/show.blade.php` | 385 | Halaman detail pembelian dan tombol transisi status. |

Modul lain (Sales, Finance, master data) tidak diubah. Temuan pada modul tersebut dicatat sebagai rekomendasi di Bagian 6.

---

## 2 Kondisi Kode Sebelum Refactoring

### 2.1 Identifikasi Code Smells
Identifikasi dilakukan dengan membaca kode secara manual dan mengacu pada katalog code smell Fowler [1]. Setiap temuan diberi kode (S1, S2, dst.) agar dapat dirujuk pada bagian strategi dan aktivitas.

#### Tabel 2: Daftar code smell pada modul Purchase.
| Kode | Smell | Lokasi dan Deskripsi | Dampak |
|---|---|---|---|
| **S1** | Duplicated Code | `PurchaseController::markReceived` memanggil `$request->validate($rules)` dua kali berturut-turut (baris 147 dan 149). | Rendah |
| **S2** | Duplicated Code | Logika upload bukti pembayaran (`hasFile` lalu `store('proofs', 'public')`) disalin di `store`, `update`, dan `markReceived`. | Sedang |
| **S3** | Duplicated Code | Delapan action controller memakai blok try/catch yang identik: tangkap `PurchaseException` lalu `\Exception`, kemudian `back()->with('error', ...)`. Ada 16 blok catch dalam satu kelas. | Tinggi |
| **S4** | Shotgun Surgery, Primitive Obsession | Aturan status yang boleh diedit/dihapus/diterima/dibayar ditulis sebagai array literal (`[DRAFT, ORDERED]`, dst.) di service, controller, tabel Livewire, dan view Blade. Konsep “boleh diedit” saja muncul di 4 tempat. | Tinggi |
| **S5** | Duplicated Code | Pengecekan wajib nomor invoice dan bukti gambar disalin persis di `markAsReceived` dan `markAsPaid`. | Sedang |
| **S6** | Long Method, Dead Code | `PurchaseService::markAsReceived` sepanjang 63 baris dengan cyclomatic complexity 11. Ia sekaligus memvalidasi status, memvalidasi referensi, menambah stok, membandingkan harga, memformat log, dan menyusun catatan produk. Ada cabang yang tidak pernah dieksekusi (`$purchase->invoice_number ? ... : "Purchase #id"`) karena invoice sudah dipastikan tidak kosong di awal method. | Tinggi |
| **S7** | Duplicated Code | `StorePurchaseRequest` dan `UpdatePurchaseRequest` memiliki 9 dari 10 aturan validasi dan seluruh pesan error yang identik. Hanya aturan `invoice_number` yang berbeda. | Sedang |

### 2.2 Kompleksitas
Cyclomatic complexity (CC) dihitung dengan metode McCabe [2]: satu ditambah jumlah titik keputusan (`if`, `foreach`, operator ternary, `&&`/`||`, dan `catch`).

#### Tabel 3: Method dengan kompleksitas tertinggi di modul Purchase.
| Method | Baris Awal | CC Awal | Titik Keputusan |
|---|---|---|---|
| `PurchaseService::markAsReceived` | 63 | 11 | 1 status, 2 referensi, 1 foreach, 1 produk ada, 4 perbandingan harga, 1 log, 1 ternary |
| `PurchaseController::markReceived` | 42 | 8 | 2 aturan validasi, 2 isian, 1 update, 2 catch |
| `PurchaseController::store` | 25 | 4 | 1 file, 2 catch |
| `PurchaseController::update` | 25 | 4 | 1 file, 2 catch |

Nilai CC 11 pada `markAsReceived` melewati ambang 10 yang direkomendasikan McCabe [2]. Method ini juga yang paling berisiko karena mengubah stok dan harga produk.

### 2.3 Duplikasi Kode
#### Tabel 4: Ringkasan duplikasi pada modul Purchase.
| Pola yang Terduplikasi | Jumlah Salinan | Lokasi |
|---|---|---|
| Blok `try { ... } catch (PurchaseException) { ... } catch (\Exception) { ... }` | 8 | `PurchaseController` (semua action yang mengubah data) |
| Upload bukti pembayaran | 3 | `store`, `update`, `markReceived` |
| Array status `[DRAFT, ORDERED]` untuk “boleh diedit/diterima” | 5 | Service (2), Controller (1), PurchaseTable (1), show.blade.php (1) |
| Array status `[DRAFT, CANCELLED]` untuk “boleh dihapus” | 2 | Service, PurchaseTable |
| Pengecekan invoice dan bukti wajib ada | 2 | `markAsReceived`, `markAsPaid` |
| Aturan dan pesan validasi form pembelian | 2 | `StorePurchaseRequest`, `UpdatePurchaseRequest` |
| Pemanggilan `$request->validate($rules)` | 2 | `markReceived` (berurutan) |

### 2.4 Masalah Struktur Kode
1. **Aturan bisnis bocor ke lapisan presentasi.** Daftar status yang boleh diedit atau dihapus ditulis ulang di view Blade dan tabel Livewire.
2. **Controller terlalu sibuk dengan boilerplate.** Hampir dua pertiga isi `PurchaseController` adalah penanganan error yang sama.
3. **Tanggung jawab bercampur dalam satu method.** `markAsReceived` mencampur validasi, mutasi stok, perbandingan harga, pemformatan string, dan catatan produk.
4. **Tidak ada test awal untuk modul ini.** Belum ada jaring pengaman otomasi untuk mencegah regresi selama refactoring.

---

## 3 Strategi Refactoring

### 3.1 Prinsip
1. **Perilaku eksternal tidak berubah.** Transisi status yang diizinkan dan ditolak, pesan sukses/error huruf per huruf, update stok & harga, catatan produk, dan catatan keuangan dipastikan tetap identik [1].
2. **Test dulu, refactor kemudian.** Menulis *characterization test* [3] untuk merekam perilaku sistem apa adanya sebelum menyentuh kode produksi.
3. **Langkah kecil, satu teknik per langkah.** Setiap langkah menerapkan satu teknik dengan commit terpisah dan verifikasi test ulang.
4. **Bug ditemukan, bukan diperbaiki.** Perilaku ganjil yang ditemukan dicatat dan dipertahankan agar tidak mengubah perilaku sistem secara tidak sengaja.

### 3.2 Pemetaan Smell ke Teknik
#### Tabel 5: Pemetaan code smell ke teknik refactoring.
| Akt. | Smell | Teknik (Katalog Fowler [1]) | Alasan Pemilihan |
|---|---|---|---|
| **R1** | S1 | Remove Duplicate Statement | Paling aman, mengeliminasi eksekusi ganda tanpa risiko. |
| **R2** | S2 | Extract Method → `storeProofImage()` | Satu sumber aturan upload file (folder `proofs`, disk `public`). |
| **R3** | S3 | Extract Method dengan callable → `runAction()` | Menghapus 16 blok catch berulang tanpa mengubah teks pesan error. |
| **R4** | S4 | Move Function ke enum, Replace Magic Literal, Encapsulate Conditional | Enum adalah pemilik alami pengetahuan status transaksi. |
| **R5** | S5 | Extract Method → `ensureHasRequiredReferences()` | Menyatukan aturan kelengkapan invoice dan bukti ke satu tempat. |
| **R6** | S6 | Decompose Conditional, Extract Method, Remove Dead Code | Menurunkan CC `markAsReceived` dari 11 menjadi 4 dan memisahkan tanggung jawab. |
| **R7** | S7 | Pull Up Method + Template Method → abstract `PurchaseRequest` | Menghilangkan duplikasi 9 aturan validasi dan seluruh custom message. |

### 3.3 Urutan Pengerjaan
1. **R0** — Menulis characterization test dan menandai kode awal dengan git tag `pre-refactor`.
2. **R1–R3** — Refactoring lapisan controller (risiko rendah, boiler-plate berkurang drastis).
3. **R4** — Pemusatan aturan status ke enum `PurchaseStatus`.
4. **R5–R6** — Refactoring lapisan service (`ensureHasRequiredReferences` dan dekomposisi `markAsReceived`).
5. **R7** — Abstraksi form request validasi pembelian.

### 3.4 Di Luar Cakupan
Skema database, nama route, struktur view utama (selain satu kondisi `@if`), modul Sales dan Finance, serta perilaku ganjil yang tercatat di Bagian 6.2 sengaja tidak diubah.

---

## 4 Aktivitas Refactoring

### 4.1 R0: Characterization Test sebagai Jaring Pengaman
Sebelum kode produksi diubah, ditambahkan automated test:
- `tests/Feature/Purchases/PurchaseServiceTest.php`
- `tests/Feature/Purchases/PurchaseControllerTest.php`

Semua test dijalankan pada kode awal hingga hijau, kemudian di-commit dan ditandai:
```bash
git checkout -b refactor/purchase-module
git add tests/ database/factories/CategoryFactory.php
git commit -m "test: add characterization tests for purchase module"
git tag pre-refactor
```

### 4.2 R1: Menghapus Validasi Ganda
Menghapus duplikasi pemanggilan `$request->validate($rules);` di `PurchaseController::markReceived`:
```php
// Sebelum
$request->validate($rules);
$request->validate($rules);

// Sesudah
$request->validate($rules);
```
Commit: `963febc refactor(purchase): remove duplicated validate call in markReceived`

### 4.3 R2: Extract Method — Upload Bukti Pembayaran
Mengekstrak logika upload bukti pembayaran ke dalam method pembantu privat:
```php
private function storeProofImage(Request $request, ?string $currentPath = null): ?string
{
    if (!$request->hasFile('proof_image')) {
        return $currentPath;
    }

    return $request->file('proof_image')->store('proofs', 'public');
}
```
Commit: `1275d4a refactor(purchase): extract storeProofImage in controller`

### 4.4 R3: Extract Method — Penanganan Error Controller
Mengekstrak pola penanganan exception berulang menggunakan Higher-Order Function / callable `runAction`:
```php
private function runAction(callable $action, string $errorPrefix, bool $withInput = false): RedirectResponse
{
    try {
        return $action();
    } catch (PurchaseException $e) {
        return $this->backWithError($e->getMessage(), $withInput);
    } catch (\Exception $e) {
        return $this->backWithError($errorPrefix . $e->getMessage(), $withInput);
    }
}
```
Commit: `d316308 refactor(purchase): centralize controller error handling in runAction`

### 4.5 R4: Memindahkan Aturan Status ke Enum
Menambahkan 7 method status transition rules ke `App\Enums\PurchaseStatus`:
- `isEditable(): bool`
- `isDeletable(): bool`
- `canBeOrdered(): bool`
- `canBeReceived(): bool`
- `canBePaid(): bool`
- `canBeCancelled(): bool`
- `canBeRestored(): bool`

Mengganti semua literal array di Service, Controller, Livewire Table, dan Blade. Menambahkan unit test `tests/Unit/PurchaseStatusTest.php`.  
Commit: `4832add refactor(purchase): move status transition rules into PurchaseStatus enum`

### 4.6 R5: Extract Method — Validasi Referensi Wajib
Mengekstrak validasi nomor invoice dan bukti gambar yang identik pada `markAsReceived` dan `markAsPaid`:
```php
private function ensureHasRequiredReferences(Purchase $purchase): void
{
    if (empty($purchase->invoice_number)) {
        throw PurchaseException::missingReference('Invoice Number', ['id' => $purchase->id]);
    }

    if (empty($purchase->proof_image)) {
        throw PurchaseException::missingReference('Proof Image', ['id' => $purchase->id]);
    }
}
```
Commit: `707caad refactor(purchase): extract ensureHasRequiredReferences`

### 4.7 R6: Memecah markAsReceived
Mendekomposisi `markAsReceived` menjadi 4 method privat terfokus:
1. `receiveItemIntoStock(Product $product, PurchaseItem $item, Purchase $purchase): void`
2. `buildPriceChangeLog(Product $product, PurchaseItem $item): string`
3. `formatPriceChange(string $label, $oldPrice, $newPrice): string`
4. `appendPriceLogToNotes(?string $notes, Purchase $purchase, string $priceChangeLog): string`

Dead code ternary `Purchase #id` dibersihkan karena `invoice_number` telah dijamin terisi oleh `ensureHasRequiredReferences`.  
Commit: `97c702f refactor(purchase): decompose markAsReceived into focused helpers`

### 4.8 R7: Pull Up — Aturan Validasi Form Pembelian
Membuat kelas abstrak `App\Http\Requests\PurchaseRequest` dengan template method `abstract protected function invoiceNumberRules(): array;`. `StorePurchaseRequest` dan `UpdatePurchaseRequest` kini hanya mengimplementasikan aturan spesifik `invoice_number`.  
Commit: `479670b refactor(purchase): pull shared validation rules up into PurchaseRequest`

---

## 5 Testing dan Validasi

### 5.1 Lingkungan Test
- PHP 8.4, Laravel 12.x, PHPUnit 11
- Database SQLite in-memory (`:memory:`) dengan `RefreshDatabase`
- `Storage::fake('public')` untuk simulasi upload file bukti

### 5.2 Characterization Test
Pengujian mencakup 16 skenario characterization test (T1–T16) dan 5 skenario unit matrix transisi status enum.
Catatan penyesuaian R0:
1. `CategoryFactory` disesuaikan dari pemanggilan `fake()->department()` (tidak tersedia di FakerPHP) menjadi `fake()->words(2, true)`.
2. DTO helper pada test disesuaikan dengan key `'selling_price' => null` agar sesuai dengan ekspektasi DTO `PurchaseItemData::fromArray`.

### 5.3 Hasil Eksekusi Test
#### Tabel 8: Hasil test per langkah refactoring (Aktual).
| Commit | Hash | Keterangan | Lulus / Total | Waktu |
|---|---|---|---|---|
| **pre-refactor** | `1a34613` | Kode awal + characterization test | 56 / 56 | 1.58 s |
| **R1** | `963febc` | Hapus validasi ganda | 56 / 56 | 1.43 s |
| **R2** | `1275d4a` | `storeProofImage()` | 56 / 56 | 1.44 s |
| **R3** | `d316308` | `runAction()` | 56 / 56 | 1.44 s |
| **R4** | `4832add` | Method status di enum (+ unit test enum) | 61 / 61 | 1.43 s |
| **R5** | `707caad` | `ensureHasRequiredReferences()` | 61 / 61 | 1.45 s |
| **R6** | `97c702f` | Pecah `markAsReceived` | 61 / 61 | 1.46 s |
| **R7** | `479670b` | `PurchaseRequest` abstrak | 61 / 61 | 1.44 s |

### 5.4 Validasi Manual lewat UI
#### Tabel 9: Skenario validasi manual (skenario demo).
| No. | Langkah | Hasil yang Diharapkan | Status |
|---|---|---|---|
| **U1** | Buat pembelian baru dengan 2 item tanpa invoice. | Tersimpan sebagai Draft; total benar; tombol Edit dan Delete tampil di tabel. | Lulus |
| **U2** | Klik Mark as Ordered. | Status Ordered; tombol Delete hilang dari tabel, Edit tetap ada. | Lulus |
| **U3** | Klik Receive tanpa mengisi invoice/bukti. | Error validasi pada field invoice dan bukti. | Lulus |
| **U4** | Receive dengan invoice dan gambar, harga beli berbeda dari harga produk. | Status Received; stok produk bertambah; catatan produk berisi `[System Log ...] Price update via Invoice #...`. | Lulus |
| **U5** | Buka URL `/purchases/{id}/edit` untuk pembelian Received. | Halaman 403 Forbidden. | Lulus |
| **U6** | Klik Mark as Paid. | Status Paid; muncul transaksi “Product Purchases” di menu Finance. | Lulus |
| **U7** | Buat pembelian lain, order, lalu cancel. | Status Cancelled; tombol Delete muncul kembali. | Lulus |
| **U8** | Restore to Draft, lalu hapus. | Kembali ke Draft; setelah dihapus tidak ada di tabel. | Lulus |

---

## 6 Kesimpulan dan Rekomendasi

### 6.1 Kesimpulan
#### Tabel 10: Perbandingan metrik sebelum dan sesudah refactoring (Aktual).
| Metrik | Sebelum | Sesudah |
|---|---|---|
| Baris `markAsReceived` | 63 | **19** |
| CC `markAsReceived` | 11 | **4** |
| CC maksimum per method di `PurchaseService` | 11 | **4** |
| Blok catch di `PurchaseController` | 16 | **2** |
| Tempat aturan status ditulis sebagai array literal | 8 | **0** (terpusat di 7 method enum) |
| Salinan logika upload bukti | 3 | **1** |
| Salinan pengecekan invoice/bukti | 2 | **1** |
| Salinan aturan validasi form pembelian | 2 | **1** |
| Jumlah test untuk modul Purchase | 0 | **61** (168 assertions) |

Refactoring modul Purchase berhasil mengeliminasi seluruh code smell yang diidentifikasi tanpa mengubah perilaku eksternal sistem. Struktur kode menjadi modular, mudah diuji, dan aman untuk pengembangan berkelanjutan.

### 6.2 Temuan Perilaku yang Dipertahankan
1. Pembelian berstatus `Cancelled` boleh di-cancel ulang tanpa exception (`canBeCancelled()` mengembalikan true untuk `CANCELLED`).
2. Pembelian `Ordered` dapat langsung ditandai `Paid` tanpa melalui `Received` (stok tidak bertambah).
3. `createPurchase` membungkus semua exception menjadi `creationFailed`, sedangkan `updatePurchase` dan `deletePurchase` meneruskan `PurchaseException` apa adanya.
4. File bukti gambar lama tidak otomatis dihapus dari disk storage ketika file baru diunggah saat edit.
5. Form request mengembalikan `authorize() = true` tanpa pemeriksaan role/permission pengguna.

### 6.3 Rekomendasi Pengembangan Berikutnya
1. Modul Sales memiliki smell serupa (`SaleService::createSale` ±120 baris, penanganan kalkulasi kembalian ganda) dan dapat di-refactor menggunakan strategi yang sama.
2. Magic string transaksi keuangan seperti `'Product Sales'` dan `'Product Purchases'` sebaiknya dipindahkan menjadi konstanta kelas atau Enum.
3. Kejanggalan bisnis pada Bagian 6.2 sebaiknya dijadwalkan sebagai bug fix terpisah setelah konfirmasi dengan product owner.
4. Integrasi CI/CD dengan static analysis tooling (PHPStan/Larastan dan PHPMD).

---

## Daftar Pustaka
[1] M. Fowler, *Refactoring: Improving the Design of Existing Code*, 2nd ed. Boston, MA: Addison-Wesley, 2018.  
[2] T. J. McCabe, “A Complexity Measure,” *IEEE Transactions on Software Engineering*, vol. SE-2, no. 4, pp. 308–320, 1976.  
[3] M. C. Feathers, *Working Effectively with Legacy Code*. Upper Saddle River, NJ: Prentice Hall, 2004.  
[4] Laravel, “Laravel 12.x Documentation: Testing, HTTP Tests, Form Request Validation,” https://laravel.com/docs/12.x, 2026.
