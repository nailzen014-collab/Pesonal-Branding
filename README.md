# Personal Branding — Portfolio

Website personal branding bertema gelap dengan aksen merah. Berisi halaman profil publik, portofolio proyek, skill, sertifikat, formulir kontak, dan panel admin untuk mengelola seluruh konten.

Spesifikasi kebutuhan ada di [`PRD.md`](PRD.md), rencana pengerjaan di [`SDLC.md`](SDLC.md).

## Fitur

**Publik**
- Beranda dengan hero, statistik, marquee teknologi, dan CTA.
- Halaman Tentang: profil, pendidikan, dan timeline pengalaman.
- Halaman Skill: kategori frontend, backend, database, dan tools.
- Portofolio: pencarian, filter kategori dan teknologi, pagination, detail proyek.
- Sertifikat: daftar sertifikat dan pencapaian.
- Kontak: WhatsApp, Instagram, GitHub, LinkedIn, email, dan formulir pesan.
- `sitemap.xml` dan `robots.txt` untuk SEO.

**Panel Admin**
- Dashboard berisi statistik konten dan aktivitas terbaru.
- CRUD proyek, skill, pengalaman, sertifikat, pesan, dan pengaturan situs.
- Sinkronisasi repository GitHub (opsional, memakai token bila tersedia).

Publikasi tidak menyediakan pendaftaran. Akun admin tunggal dibuat lewat seeder.

## Kebutuhan Sistem

- PHP 8.2 atau lebih baru
- Composer
- Node.js 20 atau lebih baru
- SQLite (default) atau MySQL

## Instalasi

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link

npm run build
```

Kalau memakai MySQL, ubah `DB_CONNECTION` di `.env` lalu jalankan `php artisan migrate --seed`.

## Konfigurasi

 Selain `.env` bawaan Laravel, aplikasi ini memakai variabel berikut:

| Variabel | Fungsi |
| --- | --- |
| `ADMIN_NAME` | Nama akun admin tunggal |
| `ADMIN_EMAIL` | Email login admin |
| `ADMIN_PASSWORD` | Password login admin |
| `GITHUB_USERNAME` | Username GitHub yang repo-nya ditampilkan |
| `GITHUB_TOKEN` | Token GitHub, opsional untuk menaikkan rate limit |
| `GITHUB_CACHE_MINUTES` | Lama cache respons GitHub, default 60 menit |
| `GITHUB_URL` | Tautan profil GitHub untuk ditampilkan di footer |

Ganti `ADMIN_PASSWORD` sebelum dipakai di server produksi.

## Sinkronisasi Portofolio dengan GitHub

Daftar proyek pada halaman `/portofolio` diambil langsung dari repository publik di
GitHub, bukan data contoh. Setelah `php artisan migrate:fresh --seed`, jalankan:

```bash
php artisan portfolio:import-github --publish --include-forks
```

Opsi `--include-forks` dipakai karena salah satu repository (`n1_problem`) adalah
fork dari `rapleeee/n1_problem`. Tanpa opsi itu, fork otomatis dilewati.

| Opsi | Fungsi |
| --- | --- |
| `--publish` | Langsung menerbitkan semua proyek hasil impor |
| `--dry-run` | Tampilkan rencana tanpa menulis ke database |
| `--user=` | Import dari username lain, tanpa mengubah `.env` |
| `--include-forks` | Sertakan repository yang merupakan fork, misalnya `n1_problem` |
| `--featured=` | Jumlah proyek unggulan otomatis di beranda, default 3 |

Perintah ini aman dijalankan berkali-kali. Repository yang sudah ada diperbarui,
bukan diduplikasi, karena pencocokan memakai `github_id` lalu `repo_url`. Tambahkan
`GITHUB_TOKEN` di `.env` bila repository publik melewati batas 60 permintaan per jam.

### Sinkronisasi Otomatis

Perintah yang sama dijadwalkan setiap jam lewat scheduler (`routes/console.php`),
sehingga repository yang baru dibuat otomatis ikut tampil di `/portofolio`.

```bash
php artisan schedule:work   # pengembangan: jalankan terus di terminal kedua
php artisan schedule:list   # lihat jadwal dan waktu eksekusi berikutnya
```

Di produksi, arahkan cron hosting ke `php artisan schedule:run` setiap menit.
Alternatifnya, tekan tombol **Sinkronkan Semua** di halaman admin
`/admin/github` untuk menjalankan sinkronisasi langsung.

## Menjalankan di Mode Pengembangan

```bash
php artisan serve
npm run dev
```

Jalankan `npm run dev` untuk hot reload Tailwind saat mengubah tampilan. Untuk produksi, jalankan `npm run build`.

## Login Admin

```bash
php artisan db:seed --class=AdminSeeder
```

Buka `/login`, lalu masuk memakai `ADMIN_EMAIL` dan `ADMIN_PASSWORD`.

## Pengujian

```bash
php artisan test
vendor\bin\pint
```

## Struktur Direktori

```
app/
  Http/Controllers/     Controller publik dan admin
  Http/Requests/        Validasi input form
  Models/               Model Eloquent
  Services/             GithubService
  Support/              Helper
config/
  site.php              Nilai bawaan untuk pengaturan situs
database/
  migrations/           Struktur tabel
  seeders/              Data awal
resources/
  css/app.css           Design system dan utility
  views/
    layouts/            Layout publik, admin, dan guest
    components/         Komponen Blade
    pages/              Halaman publik
    admin/              Halaman panel admin
    errors/             Halaman 404 dan 503
    seo/                Tampilan sitemap
routes/
  web.php               Route publik dan admin
tests/Feature/          Test halaman publik, kontak, dan CRUD admin
```

## Design System

Token warna, font, shadow, gradient, dan animasi berada di `tailwind.config.js`. Utility tambahan seperti `.card`, `.pill`, `.eyebrow`, `.border-gradient`, `.bg-aurora`, dan `.text-shine` ditulis di `resources/css/app.css`.

Ganti tema cukup dari `tailwind.config.js`, bukan mengedit tiap halaman.# Pesonal-Branding
