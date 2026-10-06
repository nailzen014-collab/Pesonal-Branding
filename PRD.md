# PRD — Website Personal Branding & Portofolio

> **Catatan untuk AI Agent (VS Code / Copilot / Claude Code):** Dokumen ini adalah sumber kebenaran (source of truth) untuk membangun website. Baca juga `docs/SDLC.md` untuk urutan kerja dan backlog. Kerjakan sesuai Sprint Backlog, jangan menambah fitur di luar dokumen tanpa konfirmasi. Jelaskan setiap konsep/kode yang dibuat secara singkat karena pemilik project sedang belajar.

| Item | Isi |
|---|---|
| Nama Produk | Portfolio — Abbad Nailun Nabhan |
| Jenis | Website personal branding / portofolio bergaya company profile |
| Pemilik | Abbad Nailun Nabhan |
| Versi Dokumen | 1.0 |
| Tanggal | 06 Oktober 2026 |
| Status | Draft siap dikerjakan |

---

## 1. Ringkasan Produk

Website personal branding yang tampil seperti company profile untuk memperkenalkan **Abbad Nailun Nabhan** sebagai **Web Developer & Fullstack Engineer**. Pengunjung (rekruter, klien, guru, sesama developer) dapat melihat profil, skill, daftar proyek lengkap dengan **link repository GitHub**, lalu menghubungi pemilik lewat WhatsApp, Instagram, atau form pesan.

## 2. Latar Belakang & Masalah

| Pertanyaan | Jawaban |
|---|---|
| Masalah apa yang diselesaikan? | Karya dan skill tersebar di GitHub tanpa presentasi yang rapi, sehingga sulit dinilai orang yang baru mengenal pemilik. |
| Siapa yang mengalami? | Pemilik (sulit membangun citra profesional) dan pengunjung (sulit menilai kemampuan dengan cepat). |
| Kenapa perlu software? | Satu website terpusat bisa menampilkan identitas, bukti karya, dan kontak dalam satu link yang mudah dibagikan. |

## 3. Tujuan & Indikator Keberhasilan

| Tujuan | Indikator |
|---|---|
| Membangun citra profesional | Semua halaman utama tampil konsisten dengan tema merah-hitam |
| Memamerkan karya | Minimal 6 proyek tampil dengan link repo GitHub yang valid |
| Memudahkan orang menghubungi | Tombol WA, IG, dan form pesan berfungsi |
| Pengalaman baik di semua perangkat | Tampil rapi di mobile, tablet, desktop |
| Performa baik | Lighthouse Performance, Accessibility, SEO masing-masing ≥ 85 |

## 4. Target Pengguna (Persona)

| Persona | Kebutuhan | Tujuan di website |
|---|---|---|
| Rekruter / HRD / Pembimbing PKL | Menilai skill dan karya dengan cepat | Lihat skill, proyek, CV, kontak |
| Klien potensial | Mencari developer untuk dibayar | Lihat portofolio, hubungi via WA |
| Developer / Rekan | Melihat kode | Buka repo GitHub |
| Admin (pemilik) | Mengelola konten tanpa ubah kode | Login, CRUD proyek/skill, baca pesan |

## 5. Data Pemilik (dipakai di konten website)

| Field | Nilai |
|---|---|
| Nama | Abbad Nailun Nabhan |
| Jenjang | Pelajar (isi **SMA** atau **SMK** sesuai kondisi sebenarnya, dikelola dari panel admin/settings) |
| Bidang | Web Developer, Fullstack Engineer, dan sejenisnya |
| GitHub | https://github.com/nailzen014-collab |
| WhatsApp | +62 856-9459-5270 (link: `https://wa.me/6285694595270`) |
| Instagram | @abdnbhn (link: `https://instagram.com/abdnbhn`) |
| Foto | `public/images/profile.jpg` (file: `assets/profile.jpg`, foto setengah badan, latar abu-abu polos) |
| Skill | Bahasa/teknologi terkait web: HTML, CSS, JavaScript, PHP, Laravel, Blade, Tailwind CSS, MySQL, Git/GitHub (pemilik boleh menyesuaikan) |

## 6. Daftar Proyek Awal (Repository GitHub)

Profil GitHub memiliki **34 repository**. Berikut yang tampil sebagai *Popular repositories* dan menjadi data awal (seeder):

| No | Repo | Link | Bahasa utama | Deskripsi |
|---|---|---|---|---|
| 1 | schoolhub | https://github.com/nailzen014-collab/schoolhub | Blade | *(isi deskripsi oleh pemilik)* |
| 2 | web-berita | https://github.com/nailzen014-collab/web-berita | PHP | *(isi deskripsi oleh pemilik)* |
| 3 | wesite_portofolio | https://github.com/nailzen014-collab/wesite_portofolio | CSS | *(isi deskripsi oleh pemilik)* |
| 4 | personal_porto | https://github.com/nailzen014-collab/personal_porto | — | *(isi deskripsi oleh pemilik)* |
| 5 | data_klien | https://github.com/nailzen014-collab/data_klien | Blade | *(isi deskripsi oleh pemilik)* |
| 6 | Webprogram_sertikom | https://github.com/nailzen014-collab/Webprogram_sertikom | Blade | *(isi deskripsi oleh pemilik)* |

**Catatan untuk agent:**
- Deskripsi di atas sengaja dikosongkan agar tidak mengarang isi proyek. Minta pemilik melengkapi atau ambil dari README tiap repo.
- 28 repo lainnya tidak tercantum di sini. Gunakan fitur **Sinkronisasi GitHub** (FR-14) untuk menariknya lewat GitHub REST API (`GET https://api.github.com/users/nailzen014-collab/repos`), lalu admin memilih mana yang ditampilkan.
- Nama repo `wesite_portofolio` memang ditulis begitu di GitHub (typo). Pakai URL apa adanya, judul tampilan boleh dirapikan menjadi "Website Portofolio".

## 7. Tech Stack

| Lapisan | Pilihan | Alasan singkat |
|---|---|---|
| Framework | **Laravel** (versi stabil terbaru) | Sudah dipakai di LKPD, ekosistem lengkap |
| Auth & scaffolding | **Laravel Breeze** (stack **Blade**) | Login admin siap pakai, sudah termasuk Tailwind + Alpine.js |
| Templating | Blade + komponen Blade (`<x-...>`) | Reuse tampilan, konsisten |
| Styling | Tailwind CSS (bawaan Breeze) | Utility-first, mudah atur tema merah-hitam |
| Interaksi | Alpine.js (bawaan Breeze) | Ringan untuk menu mobile, filter, modal |
| Database | MySQL / MariaDB | Standar untuk Laravel |
| Build | Vite | Bawaan Laravel |
| Testing | PHPUnit / Pest (Feature test) | Menguji route dan CRUD |
| Hosting | Hosting PHP/VPS atau platform yang mendukung Laravel | Dipilih saat tahap Deployment |

> **Aturan Breeze:** Route `/login` dan area `/admin/*` dilindungi middleware `auth`. **Matikan registrasi publik** (hapus route register) karena hanya ada satu admin, yaitu pemilik. Buat akun admin lewat seeder.

## 8. Arsitektur Halaman (Sitemap)

| Route | Halaman | Akses |
|---|---|---|
| `/` | Beranda (Hero, ringkasan, proyek unggulan, CTA) | Publik |
| `/tentang` | Tentang Saya + timeline pendidikan/pengalaman | Publik |
| `/skills` | Skill & teknologi | Publik |
| `/portofolio` | Daftar proyek + filter | Publik |
| `/portofolio/{slug}` | Detail proyek (link repo & demo) | Publik |
| `/sertifikat` | Sertifikat & pencapaian | Publik |
| `/kontak` | Kontak (WA, IG, GitHub) + form pesan | Publik |
| `/login` | Login admin (Breeze) | Tamu |
| `/admin` | Dashboard admin | Admin |
| `/admin/projects` | CRUD proyek + sinkron GitHub | Admin |
| `/admin/skills` | CRUD skill | Admin |
| `/admin/experiences` | CRUD timeline | Admin |
| `/admin/certificates` | CRUD sertifikat | Admin |
| `/admin/messages` | Kotak masuk pesan | Admin |
| `/admin/settings` | Data profil, link sosial, upload foto & CV | Admin |

## 9. Kebutuhan Fungsional (Functional Requirements)

### 9.1 Sisi Pengunjung

| ID | Fitur | Deskripsi | Prioritas |
|---|---|---|---|
| FR-01 | Hero section | Foto, nama, role ("Web Developer & Fullstack Engineer"), tagline, tombol "Lihat Portofolio" dan "Hubungi Saya" | Tinggi |
| FR-02 | Navbar & footer | Navbar sticky, responsif (menu hamburger di mobile), footer berisi sosial media | Tinggi |
| FR-03 | Tentang saya | Bio singkat, foto, data diri ringkas | Tinggi |
| FR-04 | Skills | Daftar skill dikelompokkan (Frontend, Backend, Database, Tools) dengan ikon/level | Tinggi |
| FR-05 | Daftar portofolio | Kartu proyek: thumbnail, judul, deskripsi singkat, tag teknologi, tombol **Lihat Repo** (GitHub) dan **Demo** (jika ada) | Tinggi |
| FR-06 | Detail proyek | Deskripsi lengkap, fitur, teknologi, galeri/screenshot, link repo & demo | Sedang |
| FR-07 | Filter & pencarian proyek | Filter kategori/teknologi dan cari judul (query disaring di server, pagination) | Sedang |
| FR-08 | Kontak cepat | Tombol WhatsApp (`wa.me`), Instagram, GitHub | Tinggi |
| FR-09 | Form pesan | Nama, email, subjek, pesan. Validasi, proteksi spam (honeypot + throttle), simpan ke DB | Sedang |
| FR-10 | Timeline | Pendidikan, pengalaman, organisasi, PKL | Sedang |
| FR-11 | Sertifikat | Daftar sertifikat/pencapaian | Rendah |
| FR-12 | Unduh CV | Tombol download CV (PDF) | Sedang |
| FR-13 | Testimoni (opsional) | Kutipan dari guru/klien/rekan | Rendah |

### 9.2 Sisi Admin

| ID | Fitur | Deskripsi | Prioritas |
|---|---|---|---|
| FR-14 | Sinkronisasi GitHub | Tarik daftar repo dari GitHub API, simpan sebagai draft, admin pilih yang ditampilkan. Hasil API di-cache | Sedang |
| FR-15 | Login admin | Pakai Laravel Breeze, tanpa registrasi publik | Tinggi |
| FR-16 | CRUD proyek | Tambah/ubah/hapus proyek, upload thumbnail, tandai *featured*, atur urutan | Tinggi |
| FR-17 | CRUD skill | Kelola skill, kategori, level | Sedang |
| FR-18 | CRUD timeline & sertifikat | Kelola data pendidikan/pengalaman/sertifikat | Rendah |
| FR-19 | Kotak masuk | Lihat pesan, tandai sudah dibaca, hapus | Sedang |
| FR-20 | Pengaturan situs | Ubah nama, tagline, bio, link sosial, foto profil, file CV | Sedang |
| FR-21 | Statistik sederhana | Jumlah kunjungan per halaman, klik tombol repo | Rendah |

## 10. Kebutuhan Non-Fungsional

| ID | Kategori | Persyaratan |
|---|---|---|
| NFR-01 | Performa | Hindari **N+1 query** (gunakan eager loading `with()`), gambar dikompres/lazy-load, aset di-bundle Vite |
| NFR-02 | Responsif | Mobile-first, breakpoint Tailwind `sm`, `md`, `lg`, `xl` |
| NFR-03 | Keamanan | CSRF aktif, validasi via Form Request, escape output Blade `{{ }}`, rate limit form kontak & login, upload dibatasi tipe/ukuran, `.env` tidak masuk Git |
| NFR-04 | SEO | Meta title/description per halaman, Open Graph (gunakan foto profil), sitemap.xml, robots.txt, URL berbasis slug |
| NFR-05 | Aksesibilitas | Kontras teks memenuhi WCAG AA, `alt` pada gambar, navigasi keyboard, fokus terlihat |
| NFR-06 | Maintainability | Struktur MVC, komponen Blade reusable, penamaan konsisten, kode diberi komentar seperlunya |
| NFR-07 | Kompatibilitas | Chrome, Edge, Firefox, Safari versi terbaru |
| NFR-08 | Ketersediaan GitHub API | Jika API gagal/rate limit, tampilkan data tersimpan di DB (tidak error) |

## 11. Desain & UI/UX

**Gaya:** modern, bersih, bergaya company profile, nuansa premium-teknologi. Tema gelap sebagai default dengan aksen merah.

### 11.1 Palet Warna (daftarkan di `tailwind.config.js`)

| Token | Hex | Penggunaan |
|---|---|---|
| `primary` | `#E11D2E` | Tombol utama, link aktif, aksen, ikon |
| `primary-dark` | `#B3121F` | Hover/active tombol |
| `primary-soft` | `#FF4D5E` | Highlight, gradien, glow |
| `ink` | `#0A0A0A` | Latar utama (hitam) |
| `surface` | `#141414` | Kartu, navbar, section selang-seling |
| `surface-2` | `#1E1E1E` | Elemen terangkat, input |
| `line` | `#2A2A2A` | Border/garis pemisah |
| `text` | `#F5F5F5` | Teks utama |
| `muted` | `#A3A3A3` | Teks sekunder |

### 11.2 Tipografi & Komponen

- Judul: **Poppins** atau **Space Grotesk** (tebal). Isi: **Inter**. Kode/tag: **JetBrains Mono**.
- Sudut membulat (`rounded-xl` / `rounded-2xl`), bayangan halus dengan *glow* merah tipis saat hover.
- Tombol utama: latar merah, teks putih. Tombol sekunder: outline merah.
- Kartu proyek: thumbnail rasio 16:9, tag teknologi berupa *pill*, tombol repo.
- Ikon: Heroicons atau Lucide (SVG inline).

### 11.3 Prinsip UX

1. **CTA jelas** di atas lipatan: "Lihat Portofolio" dan "Hubungi Saya".
2. Navigasi maksimal 6 menu, selalu terlihat (sticky).
3. Animasi halus (fade-up saat scroll, hover scale kecil), hormati `prefers-reduced-motion`.
4. Kontak dapat dijangkau dari semua halaman (footer + tombol WhatsApp mengambang).
5. Beri state kosong, loading, dan error yang ramah.
6. Foto profil ditampilkan dengan bingkai/gradien merah-hitam agar menyatu dengan tema, latar abu-abu polos pada foto bisa disamarkan dengan overlay gradien.

### 11.4 Struktur Beranda

1. Navbar
2. Hero (foto + nama + role + CTA)
3. Statistik singkat (jumlah proyek, teknologi, repo)
4. Tentang singkat
5. Skills ringkas
6. Proyek unggulan (3 kartu *featured*)
7. Timeline singkat
8. CTA kontak
9. Footer

## 12. Model Data (ERD Ringkas)

| Tabel | Kolom utama |
|---|---|
| `users` | id, name, email, password (bawaan Breeze, hanya admin) |
| `projects` | id, title, slug, summary, description, category, repo_url, demo_url, thumbnail, source (`manual`/`github`), github_id, is_featured, is_published, sort_order, timestamps |
| `technologies` | id, name, icon |
| `project_technology` | project_id, technology_id (pivot) |
| `skills` | id, name, category, level (0-100), icon, sort_order |
| `experiences` | id, type (`education`/`experience`/`organization`), title, organization, start_date, end_date, description |
| `certificates` | id, title, issuer, issued_at, file/image, url |
| `messages` | id, name, email, subject, body, read_at, timestamps |
| `settings` | key, value (nama, tagline, bio, sosial, foto, CV) |
| `page_views` *(opsional)* | id, path, created_at |

Relasi: `projects` ⟷ `technologies` (many-to-many). Saat menampilkan daftar proyek selalu pakai `Project::with('technologies')` untuk mencegah N+1.

## 13. Struktur Folder yang Diharapkan

```
app/
  Http/Controllers/
    HomeController.php
    PageController.php          # tentang, skills, sertifikat
    ProjectController.php       # publik: index, show
    ContactController.php
    Admin/
      DashboardController.php
      ProjectController.php
      SkillController.php
      ExperienceController.php
      CertificateController.php
      MessageController.php
      SettingController.php
      GithubSyncController.php
  Http/Requests/                # Form Request validasi
  Models/                       # Project, Technology, Skill, Experience, Certificate, Message, Setting
  Services/GithubService.php    # panggil GitHub API + cache
database/
  migrations/
  seeders/                      # AdminSeeder, ProjectSeeder (6 repo), SkillSeeder
resources/views/
  layouts/ (public.blade.php, admin.blade.php)
  components/                   # navbar, footer, project-card, section-title, button
  pages/ (home, about, skills, projects/index, projects/show, certificates, contact)
  admin/
public/images/profile.jpg
tests/Feature/
docs/ (PRD.md, SDLC.md)
```

## 14. Di Luar Cakupan (Out of Scope) Versi 1.0

- Registrasi pengguna publik, komentar, dan sistem login pengunjung
- Blog/artikel lengkap
- Multi-bahasa
- Pembayaran/e-commerce

## 15. Asumsi, Risiko & Pertanyaan Terbuka

| Jenis | Isi | Penanganan |
|---|---|---|
| Asumsi | Skill ditampilkan sesuai daftar bagian 5 | Pemilik boleh mengubah lewat admin |
| Risiko | Rate limit GitHub API | Cache 1 jam + fallback ke data DB |
| Risiko | Deskripsi proyek belum tersedia | Pemilik melengkapi sebelum rilis |
| Risiko | Foto berlatar abu-abu bisa terasa "tempelan" di tema gelap | Pakai overlay gradien/bingkai, atau minta versi tanpa latar |
| Terbuka | Jenjang sekolah (SMA/SMK) dan nama sekolah mau ditampilkan atau tidak | Tentukan pemilik |
| Terbuka | Domain & hosting | Tentukan di tahap Deployment |
| Terbuka | File CV | Pemilik menyiapkan PDF |
| Terbuka | Pakai nomor WA & IG di halaman publik | Sudah diberikan pemilik, pastikan nyaman ditampilkan terbuka |

## 16. Kriteria Penerimaan (Definition of Done)

- [ ] Semua fitur prioritas **Tinggi** berfungsi dan lolos uji manual
- [ ] Enam repo awal tampil dengan link GitHub yang valid dan bisa dibuka
- [ ] Tema merah-hitam konsisten di semua halaman dan responsif
- [ ] Login admin berfungsi, registrasi publik dinonaktifkan
- [ ] Tidak ada N+1 query pada halaman daftar proyek
- [ ] Form kontak tervalidasi dan tersimpan
- [ ] Lighthouse ≥ 85 untuk Performance, Accessibility, SEO
- [ ] Feature test inti lolos (`php artisan test`)
- [ ] Website ter-deploy dan dapat diakses publik
- [ ] README berisi cara instalasi dan akun demo admin (tanpa kredensial asli)
