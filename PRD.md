# PRD — Dapur Cerdas (Smart Kitchen untuk Gizi Anak)

| Field | Keterangan |
|---|---|
| Produk | Dapur Cerdas API |
| Jenis | REST API Backend (Laravel 13 + Sanctum) |
| Platform konsumen | Mobile app (iOS / Android) |
| Status | v0.1 — Autentikasi selesai; modul bisnis belum dikembangkan |
| Tanggal | 17 September 2026 |

---

## 1. Latar Belakang

Mengelola kebutuhan masak dan gizi anak secara manual sering kali
tidak efisien: orang tua kesulitan mengetahui stok bahan di dapur,
ada bahan hampir kedaluwarsa, tidak tahu resep yang bisa dibuat dari
stok yang ada, dan tidak tahu apakah asupan harian anak sudah
memenuhi kebutuhan nutrisi (kalori, protein, zat besi, zinc, vitamin).

**Dapur Cerdas** hadir sebagai solusi: aplikasi yang membantu orang tua
mencatat stok bahan makanan, menemukan resep yang cocok dengan stok
yang tersedia, mencatat kegiatan memasak, dan memantau pemenuhan
nutrisi anak.

## 2. Tujuan Produk

1. Memberikan cara mudah mencatat dan memantau stok bahan makanan
   di dapur beserta tanggal kedaluwarsanya.
2. Merekomendasikan resep yang dapat dimasak berdasarkan bahan yang
   tersedia di stok user.
3. Membantu memantau pemenuhan kebutuhan nutrisi anak melalui catatan
   masakan yang dikonsumsi (dasar data nutrisi per 100g).
4. Menyediakan pengalaman autentikasi yang aman (email + OTP dan
   Google/Firebase).

## 3. Persona Pengguna

### Orang tua / wali (User Utama)
- Ingin tahu "hari ini di rumah punya bahan apa".
- Ingin ide masakan dari stok yang ada.
- Ingin memastikan anak mendapatkan gizi cukup.
- Teknologi: aktif menggunakan smartphone, cenderung menyukai aplikasi
  yang sederhana dan cepat.

## 4. Ruang Lingkup

### 4.1. In Scope
- Sistem autentikasi & profil (sudah dibangun).
- Master data bahan & kategori.
- Manajemen stok dapur user (CRUD + notifikasi hampir kedaluwarsa).
- Katalog resep (manual & AI-generated).
- Pencocokan resep berdasarkan stok ("masak apa hari ini?").
- Cooking log & perhitungan nutrisi harian anak.

### 4.2. Out of Scope (untuk saat ini)
- E-commerce / belanja bahan.
- Sinkronisasi perangkat / IoT dapur.
- Multi-profil anak (dapat dijadikan iterasi berikutnya).
- Notifikasi push real-time.

## 5. Persyaratan Fungsional

### 5.1. Autentikasi & Akun (✅ Sudah Diimplementasikan)
| ID | Requirement | Status |
|---|---|---|
| FR-01 | User dapat mendaftar dengan nama, email, dan password (min 8 karakter). | ✅ |
| FR-02 | Sistem mengirimkan OTP 6 digit via email yang berlaku 10 menit. | ✅ |
| FR-03 | User dapat memverifikasi email dengan kode OTP; berhasil = token Sanctum diterbitkan. | ✅ |
| FR-04 | User dapat meminta ulang OTP, dengan batasan email yang belum terverifikasi saja. | ✅ |
| FR-05 | User dapat login email/password; jika email belum terverifikasi, OTP baru dikirim dan login ditolak (403). | ✅ |
| FR-06 | User dapat login dengan Google melalui verifikasi ID token Firebase (issuer, audience, signature via x509 cert). | ✅ |
| FR-07 | User dapat logout (mencabut token) dan mengambil data profil (`/me`). | ✅ |
| FR-08 | Seluruh akses selain register/login/verify dilindungi middleware `auth:sanctum`. | ✅ |

### 5.2. Master Data Bahan (⚠️ DB ada, API belum)
| ID | Requirement |
|---|---|
| FR-09 | User dapat melihat daftar kategori bahan (Sayur, Protein, Karbohidrat, Buah, Bumbu, Dairy). |
| FR-10 | User dapat mencari bahan pada master data (`ingredients_master`) berdasarkan nama/kategori. |
| FR-11 | Setiap bahan menampilkan data nutrisi per 100g: kalori, protein, lemak, karbohidrat, zat besi, zinc, vitamin A, vitamin C. |
| FR-12 | (Admin) Menambah/mengubah/menghapus master bahan & kategori secara internal (seeder/artisan). |

### 5.3. Manajemen Stok Dapur (⚠️ DB ada, API belum)
| ID | Requirement |
|---|---|
| FR-13 | User dapat menambahkan bahan ke stok dapur (ingredient, quantity, unit, expiry_date). |
| FR-14 | User dapat melihat seluruh stok miliknya, opsional difilter hampir kedaluwarsa (berdasarkan indeks `user_id, expiry_date`). |
| FR-15 | User dapat mengubah jumlah/unit/tanggal kedaluwarsa stok. |
| FR-16 | User dapat menghapus stok (misal bahan sudah habis/dibuang). |
| FR-17 | Sistem menandai stok yang hampir kedaluwarsa (mis. ≤ 3 hari) sebagai status "hampir kadaluarsa". |
| FR-18 | Stok bersifat private per user (hanya pemilik yang dapat akses). |

### 5.4. Resep (⚠️ DB ada, API belum)
| ID | Requirement |
|---|---|
| FR-19 | User dapat melihat katalog resep (nama, deskripsi, instruksi, waktu masak, sumber manual/ai_generated). |
| FR-20 | User dapat melihat detail resep termasuk daftar bahan dan kebutuhannya (`recipe_ingredients`). |
| FR-21 | User dapat membuat resep manual (khusus untuk dirinya sendiri / dapat ditandai `created_by`). |
| FR-22 | Sistem dapat menunjukkan resep yang bisa dimasak berdasarkan stok user yang tersedia (full match atau partial match). |
| FR-23 | Fitur resep AI-generasi dicadangkan (kolom `source = ai_generated`) — integrasi nanti. |

### 5.5. Cooking Log & Nutrisi (⚠️ DB ada, API belum)
| ID | Requirement |
|---|---|
| FR-24 | User dapat mencatat resep yang telah dimasak (servings, cooked_at) ke `cooking_logs`. |
| FR-25 | Sistem menghitung porsi kebutuhan bahan = bahan resep × servings. |
| FR-26 | Saat memasak dicatat, stok bahan yang digunakan dapat berkurang sesuai kebutuhan resep (deduksi stok). |
| FR-27 | Sistem menghitung asupan nutrisi harian dari riwayat masak (berbasis data nutrisi per 100g). |
| FR-28 | User dapat melihat ringkasan riwayat masakan dan estimasi nutrisi (harian/mingguan). |

## 6. Persyaratan Non-Fungsional

| ID | Requirement |
|---|---|
| NFR-01 | **Keamanan:** password di-hash (bcrypt rounds 12), OTP disimpan hashed-or-plain namun validasi ketat, semua endpoint selain publik dilindungi Sanctum. |
| NFR-02 | **Keamanan auth Google:** verifikasi token wajib dilakukan secara kriptografis (issuer policy = `https://securetoken.google.com/{project_id}`, signature SHA256 via x509 Google, cache cert 1 jam). |
| NFR-03 | **Privasi:** data stok & cooking log hanya dapat diakses oleh pemiliknya. |
| NFR-04 | **Performa:** indeks pada kolom yang sering difilter (`user_id, expiry_date`, `name`); query hampir kedaluwarsa dioptimalkan. |
| NFR-05 | **Integritas data:** FK dengan aturan penghapusan yang aman (cascade untuk data user, restrict untuk master data). |
| NFR-06 | **API Design:** format respons konsisten (`success`, `message`, `data`, `errors`), HTTP status code sesuai standar. |
| NFR-07 | **Testing:** setiap modul baru dilengkapi test otomatis (unit + feature, PHPUnit). |
| NFR-08 | **Dokumentasi:** endpoint terdokumentasi (OpenAPI diperbolehkan pada iterasi berikutnya). |

## 7. Arsitektur Teknis

- **Framework:** Laravel 13, PHP ≥ 8.3
- **Auth token:** Laravel Sanctum (personal access tokens, token name `mobile_app`)
- **Auth Google:** Firebase ID Token → verifikasi manual `FirebaseTokenVerifier`
  (JWT decode → issuer/audience/exp check → signature check with Google x509 cert via `openssl_verify`, cert cache-driver default 1 jam)
- **Database:** SQLite (dev) → siap dimigrasi MySQL/PostgreSQL (konfigurasi via `.env`)
- **Struktur:**
  - `app/Http/Controllers/Api/` — controller per modul
  - `app/Http/Requests/` — validasi (FormRequest) *(belum ada, diusulkan)*
  - `app/Http/Resources/` — transformasi JSON *(belum ada, diusulkan)*
  - `app/Services/` — logika bisnis terpisah (contoh: `FirebaseTokenVerifier`)
  - `app/Models/` — model Eloquent (hanya `User` saat ini)
- **Pola:** Controller tipis, Service untuk logika kompleks (rekomendasi resep, kalkulasi nutrisi, deduksi stok).

## 8. Alur Kerja Sistem

### 8.1. Onboarding
```
Register → kirim OTP via email → User masukkan OTP → verifikasi sukses
        → token Sanctum diterbitkan → user masuk aplikasi
     (alternatif) Google Login → token Firebase diverifikasi → user dibuat/ditemukan
        → token Sanctum diterbitkan
     (login ulang) Login → cek password → jika email belum diverifikasi: kirim OTP lagi & tolak login
```

### 8.2. Alur Harian Pengguna
```
1. User mencatat bahan baru yang dibeli → masuk ke stok dapur (FR-13..18)
2. User membuka "Masak Apa Hari Ini?" → sistem cocokkan stok vs resep
      → tampilkan resep full/partial match sesuai stok (FR-22)
3. User memilih resep → melihat detail bahan & langkah (FR-19..20)
4. User memasak → konfirmasi → cooking_log disimpan + stok bahan terpakai
      dikurangi (FR-24..26)
5. Nutrisi harian anak dihitung dari riwayat masak (FR-27..28)
6. Stok hampir kedaluwarsa ditandai agar segera dipakai/dibuang (FR-17)
```

### 8.3. Alur Rekomendasi Resep (usulan)
```
Input: daftar user_stocks (bahan aktif)
Proses: untuk tiap resep, bandingkan kebutuhan recipe_ingredients vs stok
   - FULL: semua bahan cukup → "Bisa dimasak sekarang"
   - PARTIAL: sebagian cukup → tampilkan bahan yang kurang
Output: ranking resep (full dulu, lalu partial by coverage %)
```

## 9. Skema Basis Data (Kondisi Saat Ini)

```
users (id, name, email[U], email_verified_at, password, otp_code, otp_expires_at,
       remember_token, timestamps)
  └─ has_many user_stocks, cooking_logs

ingredient_categories (id, name[U])

ingredients_master (id, category_id→ingredient_categories, name[I], default_unit,
                    calories_per_100g, protein_g, fat_g, carbs_g, iron_mg, zinc_mg,
                    vitamin_a_mcg, vitamin_c_mg, created_at)

user_stocks (id, user_id→users[C], ingredient_id→ingredients_master[R], quantity,
             unit, expiry_date, timestamps)  [I(user_id, expiry_date)]

recipes (id, name, description, instructions, cook_time_minutes,
         source ENUM[manual|ai_generated], created_by→users[NULL], created_at)

recipe_ingredients (id, recipe_id→recipes[C], ingredient_id→ingredients_master[R],
                    quantity_needed, unit)  [U(recipe_id, ingredient_id)]

cooking_logs (id, user_id→users[C], recipe_id→recipes[R], servings, cooked_at)
```

Legenda: `[U]` unique, `[I]` index, `[C]` cascade on delete, `[R]` restrict on delete.

## 10. Peta Endpoint API (Diusulkan)

### Saat ini (sudah ada)
| Method | Endpoint | Fungsi |
|---|---|---|
| POST | `/api/register` | Registrasi email + kirim OTP |
| POST | `/api/login` | Login email/password |
| POST | `/api/verify-otp` | Verifikasi OTP → token |
| POST | `/api/resend-otp` | Kirim ulang OTP |
| POST | `/api/auth/google` | Login Google (ID token) |
| POST | `/api/logout` | Logout (auth) |
| GET | `/api/me` | Profil user (auth) |

### Diusulkan (modul bisnis)
| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/api/categories` | Daftar kategori (FR-09) |
| GET | `/api/ingredients?search=&category=` | Cari master bahan (FR-10) |
| GET | `/api/stocks` | List stok user + filter hampir kedaluwarsa (FR-14) |
| POST | `/api/stocks` | Tambah stok (FR-13) |
| PUT/PATCH | `/api/stocks/{id}` | Ubah stok (FR-15) |
| DELETE | `/api/stocks/{id}` | Hapus stok (FR-16) |
| GET | `/api/recipes` | List resep (FR-19) |
| GET | `/api/recipes/{id}` | Detail resep (FR-20) |
| POST | `/api/recipes` | Buat resep manual (FR-21) |
| GET | `/api/recipes/cookable` | Resep bisa dimasak dari stok (FR-22) |
| POST | `/api/cooking-logs` | Catat masakan (FR-24) |
| GET | `/api/nutrition/summary` | Ringkasan nutrisi harian/mingguan (FR-28) |

## 11. Rencana Pengembangan (Milestone)

| Fase | Isi | Prioritas |
|---|---|---|
| **M0 (selesai)** | Autentikasi lengkap (email+OTP, Google, Sanctum) | — |
| **M1** | Master bahan & kategori API + model Eloquent untuk semua tabel | Tinggi |
| **M2** | Manajemen stok dapur (CRUD + deteksi hampir kedaluwarsa) | Tinggi |
| **M3** | Katalog & detail resep + resep manual | Tinggi |
| **M4** | Rekomendasi resep berdasarkan stok (full/partial match) | Sedang |
| **M5** | Cooking log + deduksi stok + kalkulasi nutrisi harian | Sedang |
| **M6** | Integrasi AI-generated recipe (source `ai_generated`) | Rendah |
| **M7** | Notifikasi push & penerbitan API (production hardening) | Rendah |

## 12. Metrik Keberhasilan

- 100% endpoint modul bisnis memiliki test otomatis (unit + feature).
- Rekomendasi resep: ≥ 90% resep yang ditandai "Bisa dimasak" benar-benar
  dapat dimasak dengan stok (precision).
- Deduksi stok pada cooking log selalu akurat (tidak ada jumlah negatif,
  konsisten antara cooking_logs dan user_stocks).
- Waktu respons < 500ms untuk list stok/resep dengan data ≥ 1000 baris.

## 13. Risiko & Mitigasi

| Risiko | Mitigasi |
|---|---|
| Data nutrisi master tidak lengkap/akurat | Seeder diperluas + referensi data nutrisi terverifikasi |
| OTP disimpan plain di DB | Pertimbangkan hashing OTP; tambahkan rate-limit resend & cooldown |
| Verifikasi Firebase tergantung jaringan (fetch x509) | Cache cert 1 jam + timeout 10s + fallback |
| Deduksi stok bisa minus (salah hitung) | Sertakan logika cek kecukupan stok sebelum cooking; rollback/validasi transaksi DB |
| Satu resep dipakai bersama (multi-user) | `recipes` bersifat global + `created_by` untuk resep personal |
| Skalabilitas SQLite | Siap migrasi ke MySQL/PostgreSQL via konfigurasi `.env` |