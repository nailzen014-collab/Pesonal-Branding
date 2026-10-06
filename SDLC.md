# SDLC — Website Personal Branding & Portofolio

> **Catatan untuk AI Agent (VS Code / Copilot / Claude Code):** Ikuti dokumen ini berurutan dari fase 1 sampai 7. Detail fitur ada di `docs/PRD.md` (ID `FR-xx` dan `NFR-xx` merujuk ke PRD). Kerjakan sesuai **Sprint Backlog**, ubah kolom *Status* setelah selesai. Pemilik project sedang belajar, jadi jelaskan konsep dan alasan di balik kode secara singkat.

| Item | Isi |
|---|---|
| Project | Website Personal Branding / Portofolio — Abbad Nailun Nabhan |
| Metodologi | Agile (Scrum sederhana, 4 sprint @ 1 minggu, 1 orang) |
| Stack | Laravel + Laravel Breeze (Blade) + Tailwind CSS + Alpine.js + MySQL + Vite |
| Tema | Modern, UI/UX bagus, **primary merah & hitam** |
| Penanggung Jawab | Abbad Nailun Nabhan (dibantu AI Agent) |

---

## Fase 1 — Problem / Identifikasi Masalah

| Pertanyaan | Jawaban |
|---|---|
| **Masalah apa yang diselesaikan?** | Karya dan skill pemilik tersebar di GitHub tanpa presentasi yang rapi, sehingga sulit dinilai dengan cepat. |
| **Siapa yang mengalami?** | Pemilik (sulit membangun citra profesional) dan pengunjung seperti rekruter, klien, pembimbing, rekan developer (sulit menilai kemampuan). |
| **Kenapa software dibutuhkan?** | Satu website terpusat menampilkan identitas, skill, proyek beserta link repo, dan kontak dalam satu link yang mudah dibagikan. Konten bisa diperbarui lewat panel admin tanpa ubah kode. |

**Ruang lingkup:** halaman publik (Beranda, Tentang, Skills, Portofolio, Detail Proyek, Sertifikat, Kontak) dan panel admin. Detail di PRD bagian 8-9.

---

## Fase 2 — Planning

### 2.1 Product Backlog

Format User Story: *"Sebagai [siapa], saya ingin [apa], sehingga [tujuan]."* Prioritas: Tinggi, Sedang, Rendah. Estimasi dalam jam kerja (jam).

| ID | User Story | Prioritas | Estimasi Waktu | Status | Catatan |
|---|---|---|---|---|---|
| US-01 | Sebagai pengunjung, saya ingin melihat hero berisi foto, nama, dan role pemilik, sehingga langsung paham siapa pemilik website | Tinggi | 4 jam | Belum | FR-01, foto `public/images/profile.jpg` |
| US-02 | Sebagai pengunjung, saya ingin navbar dan footer responsif, sehingga mudah berpindah halaman di perangkat apa pun | Tinggi | 4 jam | Belum | FR-02, navbar sticky + hamburger (Alpine.js) |
| US-03 | Sebagai pengunjung, saya ingin membaca profil singkat pemilik, sehingga mengenal latar belakangnya | Tinggi | 3 jam | Belum | FR-03 |
| US-04 | Sebagai pengunjung, saya ingin melihat daftar skill terkelompok, sehingga tahu teknologi yang dikuasai | Tinggi | 4 jam | Belum | FR-04 |
| US-05 | Sebagai pengunjung, saya ingin melihat daftar proyek dengan link repo GitHub, sehingga bisa menilai karya dan membuka kodenya | Tinggi | 8 jam | Belum | FR-05, 6 repo awal (lihat bagian 2.3) |
| US-06 | Sebagai pengunjung, saya ingin tombol kontak WhatsApp, Instagram, dan GitHub, sehingga mudah menghubungi pemilik | Tinggi | 2 jam | Belum | FR-08 |
| US-07 | Sebagai admin, saya ingin login yang aman, sehingga hanya saya yang bisa mengelola konten | Tinggi | 3 jam | Belum | FR-15, Breeze, registrasi publik dinonaktifkan |
| US-08 | Sebagai admin, saya ingin menambah, mengubah, menghapus proyek, sehingga portofolio selalu terbaru | Tinggi | 8 jam | Belum | FR-16, upload thumbnail, eager loading (NFR-01) |
| US-09 | Sebagai pengunjung, saya ingin melihat detail satu proyek, sehingga paham fitur dan teknologinya | Sedang | 5 jam | Belum | FR-06, route by slug |
| US-10 | Sebagai pengunjung, saya ingin memfilter dan mencari proyek, sehingga cepat menemukan yang relevan | Sedang | 5 jam | Belum | FR-07, filter di server + pagination |
| US-11 | Sebagai pengunjung, saya ingin mengirim pesan lewat form, sehingga bisa menghubungi tanpa membuka aplikasi lain | Sedang | 5 jam | Belum | FR-09, validasi + honeypot + throttle |
| US-12 | Sebagai admin, saya ingin membaca pesan masuk, sehingga bisa membalas pengunjung | Sedang | 4 jam | Belum | FR-19 |
| US-13 | Sebagai admin, saya ingin mengelola skill, sehingga daftar skill selalu akurat | Sedang | 4 jam | Belum | FR-17 |
| US-14 | Sebagai admin, saya ingin menarik daftar repo dari GitHub otomatis, sehingga tidak input manual satu per satu | Sedang | 8 jam | Belum | FR-14, `GithubService` + cache, fallback ke DB |
| US-15 | Sebagai pengunjung, saya ingin melihat timeline pendidikan dan pengalaman, sehingga paham perjalanan pemilik | Sedang | 4 jam | Belum | FR-10 |
| US-16 | Sebagai pengunjung, saya ingin mengunduh CV, sehingga bisa menyimpannya | Sedang | 2 jam | Belum | FR-12 |
| US-17 | Sebagai admin, saya ingin mengatur data profil dan link sosial dari satu halaman, sehingga tidak edit kode | Sedang | 4 jam | Belum | FR-20, tabel `settings` |
| US-18 | Sebagai pemilik, saya ingin website mudah ditemukan dan menarik saat dibagikan, sehingga lebih banyak yang melihat | Sedang | 4 jam | Belum | NFR-04, meta, Open Graph, sitemap |
| US-19 | Sebagai pemilik, saya ingin website online, sehingga bisa dibagikan ke siapa saja | Tinggi | 6 jam | Belum | Deployment, domain + hosting dipilih nanti |
| US-20 | Sebagai pengunjung, saya ingin melihat sertifikat dan pencapaian, sehingga yakin dengan kompetensinya | Rendah | 4 jam | Belum | FR-11, FR-18 |
| US-21 | Sebagai pengunjung, saya ingin animasi halus saat scroll, sehingga tampilan terasa modern | Rendah | 4 jam | Belum | Hormati `prefers-reduced-motion` |
| US-22 | Sebagai pemilik, saya ingin melihat statistik kunjungan dan klik repo, sehingga tahu konten mana yang diminati | Rendah | 6 jam | Belum | FR-21 |
| US-23 | Sebagai pengunjung, saya ingin membaca testimoni, sehingga lebih percaya pada pemilik | Rendah | 3 jam | Belum | FR-13, opsional |

### 2.2 Sprint Backlog

Item diambil dari **prioritas tertinggi ke terendah**. Satu sprint = 1 minggu, kapasitas sekitar 25-30 jam.

#### Sprint 1 — Fondasi & Halaman Publik Inti (prioritas Tinggi)

**Sprint Goal:** project berjalan, tema merah-hitam terpasang, halaman publik inti tampil.

| Task | Penanggung Jawab | Estimasi | Status | Catatan |
|---|---|---|---|---|
| Install Laravel + Breeze (Blade), konfigurasi DB `.env` | Abbad (dibantu Agent) | 2 jam | Belum | Rapikan `.env.example` |
| Setup Tailwind: token warna `primary`, `ink`, `surface`, font | Abbad (dibantu Agent) | 2 jam | Belum | PRD bagian 11.1 |
| Buat layout `public.blade.php` + komponen navbar & footer (US-02) | Abbad (dibantu Agent) | 4 jam | Belum | Alpine.js untuk menu mobile |
| Taruh foto di `public/images/profile.jpg`, buat hero (US-01) | Abbad (dibantu Agent) | 4 jam | Belum | Tambahkan `alt` dan overlay gradien |
| Halaman Tentang (US-03) | Abbad (dibantu Agent) | 3 jam | Belum | |
| Halaman Skills + seeder (US-04) | Abbad (dibantu Agent) | 4 jam | Belum | Tabel `skills` |
| Tombol kontak WA/IG/GitHub (US-06) | Abbad (dibantu Agent) | 2 jam | Belum | Link `wa.me/6285694595270` |
| Migration + model `projects`, `technologies`, seeder 6 repo | Abbad (dibantu Agent) | 4 jam | Belum | PRD bagian 6 dan 12 |

#### Sprint 2 — Portofolio & Admin (prioritas Tinggi, lanjut Sedang)

**Sprint Goal:** daftar proyek dengan link repo tampil, admin bisa login dan kelola proyek.

| Task | Penanggung Jawab | Estimasi | Status | Catatan |
|---|---|---|---|---|
| Halaman daftar portofolio + komponen `project-card` (US-05) | Abbad (dibantu Agent) | 8 jam | Belum | Pakai `with('technologies')` |
| Login admin: nonaktifkan register, `AdminSeeder`, middleware `auth` (US-07) | Abbad (dibantu Agent) | 3 jam | Belum | Password dari `.env`, jangan hardcode |
| CRUD proyek admin + upload thumbnail (US-08) | Abbad (dibantu Agent) | 8 jam | Belum | Form Request validasi |
| Halaman detail proyek by slug (US-09) | Abbad (dibantu Agent) | 5 jam | Belum | |
| Filter & pencarian proyek (US-10) | Abbad (dibantu Agent) | 5 jam | Belum | Server-side + pagination |

#### Sprint 3 — Interaksi & Konten Tambahan (prioritas Sedang)

**Sprint Goal:** pengunjung bisa mengirim pesan, konten dinamis lengkap.

| Task | Penanggung Jawab | Estimasi | Status | Catatan |
|---|---|---|---|---|
| Form kontak + simpan pesan (US-11) | Abbad (dibantu Agent) | 5 jam | Belum | Honeypot + throttle |
| Kotak masuk admin (US-12) | Abbad (dibantu Agent) | 4 jam | Belum | Tandai dibaca |
| CRUD skill admin (US-13) | Abbad (dibantu Agent) | 4 jam | Belum | |
| Sinkronisasi GitHub API (US-14) | Abbad (dibantu Agent) | 8 jam | Belum | Cache 1 jam, tangani rate limit |
| Timeline pendidikan/pengalaman (US-15) | Abbad (dibantu Agent) | 4 jam | Belum | |
| Unduh CV (US-16) | Abbad (dibantu Agent) | 2 jam | Belum | Pemilik siapkan PDF |
| Pengaturan situs (US-17) | Abbad (dibantu Agent) | 4 jam | Belum | |

#### Sprint 4 — Polish, SEO, Testing & Deployment (Sedang, Rendah, Deployment)

**Sprint Goal:** website rapi, teruji, dan online.

| Task | Penanggung Jawab | Estimasi | Status | Catatan |
|---|---|---|---|---|
| SEO: meta, Open Graph, sitemap.xml, robots.txt (US-18) | Abbad (dibantu Agent) | 4 jam | Belum | |
| Sertifikat (US-20) | Abbad (dibantu Agent) | 4 jam | Belum | Rendah |
| Animasi scroll (US-21) | Abbad (dibantu Agent) | 4 jam | Belum | Rendah |
| Testimoni (US-23) | Abbad (dibantu Agent) | 3 jam | Belum | Opsional |
| Statistik kunjungan (US-22) | Abbad (dibantu Agent) | 6 jam | Belum | Opsional, kerjakan jika waktu cukup |
| Testing: feature test, uji responsif, Lighthouse | Abbad (dibantu Agent) | 6 jam | Belum | Lihat Fase 5 |
| Deployment + README (US-19) | Abbad (dibantu Agent) | 6 jam | Belum | Lihat Fase 6 |

### 2.3 Daftar Repository untuk Seeder Proyek

Sumber: https://github.com/nailzen014-collab (34 repo; berikut 6 repo unggulan).

| No | Nama Repo | Link | Bahasa | Deskripsi |
|---|---|---|---|---|
| 1 | schoolhub | https://github.com/nailzen014-collab/schoolhub | Blade | *(diisi pemilik)* |
| 2 | web-berita | https://github.com/nailzen014-collab/web-berita | PHP | *(diisi pemilik)* |
| 3 | wesite_portofolio | https://github.com/nailzen014-collab/wesite_portofolio | CSS | *(diisi pemilik)* |
| 4 | personal_porto | https://github.com/nailzen014-collab/personal_porto | — | *(diisi pemilik)* |
| 5 | data_klien | https://github.com/nailzen014-collab/data_klien | Blade | *(diisi pemilik)* |
| 6 | Webprogram_sertikom | https://github.com/nailzen014-collab/Webprogram_sertikom | Blade | *(diisi pemilik)* |

> Jangan mengarang deskripsi. Jika kosong, baca README tiap repo atau tanya pemilik. 28 repo lain ditarik lewat fitur sinkronisasi GitHub (US-14).

### 2.4 Risiko Utama

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Rate limit GitHub API | Daftar repo gagal dimuat | Cache + fallback data DB |
| Deskripsi proyek belum ada | Kartu proyek terlihat kosong | Pemilik melengkapi di Sprint 2 |
| Waktu belajar bertambah | Sprint molor | Fitur Rendah boleh digeser ke versi 1.1 |

---

## Fase 3 — Design

| Aspek | Keputusan |
|---|---|
| Arsitektur | MVC Laravel, controller tipis, logika GitHub di `Services/GithubService.php` |
| Database | Lihat PRD bagian 12 (ERD ringkas). Pivot `project_technology` |
| Routing | Publik di `routes/web.php`, admin di grup `Route::middleware('auth')->prefix('admin')` |
| UI/UX | Modern, tema gelap, **primary merah (#E11D2E) dan hitam (#0A0A0A)**, detail token di PRD bagian 11 |
| Wireframe | Buat sketsa sederhana (kertas/Figma) untuk Beranda, Portofolio, Detail Proyek, Kontak sebelum koding tiap halaman |
| Komponen Blade | `x-navbar`, `x-footer`, `x-project-card`, `x-section-title`, `x-button` |
| Responsif | Mobile-first (`sm`, `md`, `lg`, `xl`) |

**Konsep yang perlu dipahami:** *mobile-first* berarti menulis gaya untuk layar kecil dulu lalu menambah aturan untuk layar lebih besar, sehingga tampilan ponsel (yang paling sering dipakai pengunjung) selalu benar.

---

## Fase 4 — Development

**Urutan pengerjaan:**

1. Setup: `composer create-project laravel/laravel`, `composer require laravel/breeze --dev`, `php artisan breeze:install blade`, `npm install && npm run dev`, `php artisan migrate`.
2. Nonaktifkan registrasi publik (hapus route/link register).
3. Tema Tailwind (warna, font) lalu layout dan komponen.
4. Migration, model, relasi, seeder.
5. Halaman publik, lalu admin, lalu fitur tambahan.

**Aturan kode (untuk agent):**

- Validasi lewat **Form Request**, jangan di controller.
- Gunakan **eager loading** (`with()`) di semua query daftar (cegah N+1).
- Jangan menaruh rahasia (password admin, token) di kode. Pakai `.env`.
- Tampilkan data dengan `{{ }}` (otomatis di-escape) dan hindari `{!! !!}` untuk input pengguna.
- Satu commit per fitur dengan pesan jelas, contoh: `feat: tambah halaman daftar portofolio`.
- Gunakan branch per sprint/fitur, merge ke `main` setelah lolos uji.
- Setelah tiap fitur, jelaskan singkat konsep yang dipakai (misal: relasi many-to-many, middleware, Form Request).

---

## Fase 5 — Testing

| Jenis | Cakupan | Alat |
|---|---|---|
| Feature test | Halaman publik tampil (status 200), login admin, route admin menolak tamu, CRUD proyek, validasi form kontak | PHPUnit / Pest |
| Uji manual | Semua link (repo, WA, IG), form, upload gambar | Browser |
| Responsif | Ponsel, tablet, desktop | DevTools device toolbar |
| Performa & SEO | Lighthouse ≥ 85 | Chrome Lighthouse |
| Query | Tidak ada N+1 pada daftar proyek | Laravel Debugbar atau `DB::listen` |
| Keamanan dasar | Akses `/admin` tanpa login ditolak, throttle form kontak aktif | Manual |

**Contoh test case:**

| ID | Skenario | Hasil yang diharapkan |
|---|---|---|
| TC-01 | Buka `/` | Status 200, nama pemilik tampil |
| TC-02 | Buka `/portofolio` | 6 proyek tampil, tombol Lihat Repo mengarah ke URL GitHub yang benar |
| TC-03 | Buka `/admin` tanpa login | Redirect ke `/login` |
| TC-04 | Login dengan password salah | Pesan error, tidak masuk |
| TC-05 | Kirim form kontak kosong | Pesan validasi tampil |
| TC-06 | Kirim form kontak valid | Tersimpan di `messages`, muncul di admin |
| TC-07 | Akses `/register` | Tidak tersedia (404/redirect) |

---

## Fase 6 — Deployment

| Langkah | Detail |
|---|---|
| Persiapan | `APP_ENV=production`, `APP_DEBUG=false`, `APP_KEY` baru, kredensial DB produksi |
| Build | `npm run build`, `composer install --no-dev --optimize-autoloader` |
| Optimasi | `php artisan config:cache`, `route:cache`, `view:cache`, `storage:link` |
| Database | `php artisan migrate --force`, jalankan `AdminSeeder` sekali lalu ganti password |
| Hosting | Dipilih pemilik (hosting PHP/VPS/platform Laravel). Document root mengarah ke folder `public/` |
| Domain & HTTPS | Pasang domain dan sertifikat SSL |
| Pasca-deploy | Cek semua halaman, link, form, dan Lighthouse versi online |

---

## Fase 7 — Maintenance

- Tambah proyek baru lewat panel admin atau sinkronisasi GitHub.
- Backup database berkala dan update dependency (`composer update`) secara terjadwal.
- Pantau pesan masuk dan statistik.
- Backlog versi 1.1: blog/artikel, mode terang/gelap, multi-bahasa.
- Catat bug dan ide di GitHub Issues repo project ini.

---

## Checklist Ringkas untuk Agent

- [x] Fase 1: masalah dipahami
- [x] Fase 2: Product Backlog dan Sprint Backlog dibaca, kerjakan Sprint 1 dulu
- [x] Fase 3: warna, font, komponen sesuai PRD bagian 11
- [x] Fase 4: Breeze terpasang, register dinonaktifkan, eager loading dipakai
- [x] Fase 5: semua test case lolos
- [ ] Fase 6: deployment sukses
- [x] Fase 7: rencana maintenance tercatat

---

## Status Implementasi

Semua halaman publik dan panel admin sudah selesai beserta datanya.
Status per-PRD:

| Area | Status |
|---|---|
| Halaman publik (Beranda, Tentang, Skill, Portofolio, Sertifikat, Kontak) | Selesai |
| Panel admin (dashboard, proyek, skill, pengalaman, sertifikat, pesan, pengaturan) | Selesai |
| Integrasi GitHub dengan cache dan fallback database | Selesai |
| SEO (`sitemap.xml`, `robots.txt`, meta tag) | Selesai |
| Halaman error 404 dan 503 | Selesai |
| Test feature (publik, kontak, CRUD admin, autentikasi) | 29 test, 90 assertions |
| Dokumentasi README | Selesai |

Belum dikerjakan:

- Instalasi Laravel Boost sesuai `AGENTS.md`.
- Halaman 500 dan halaman error selain 404/503.
- Optimasi Lighthouse di server produksi.
- Migrasi dari SQLite ke MySQL bila SQLite tidak lagi cukup.
- Backlog versi 1.1: blog/artikel, mode terang/gelap, multi-bahasa.
