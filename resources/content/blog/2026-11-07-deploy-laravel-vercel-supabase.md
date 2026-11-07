---
title: "Deploy Laravel ke Vercel dengan Database Supabase"
slug: deploy-laravel-ke-vercel-dengan-supabase
excerpt: "Menjalankan panel admin dan halaman publik Laravel secara serverless di Vercel, dengan PostgreSQL Supabase sebagai database, sesi, dan cache."
category: Deployment
published_at: 2026-11-07 10:00
sort_order: 7
meta_description: "Panduan deploy Laravel ke Vercel memakai container FrankenPHP dan database PostgreSQL Supabase: konfigurasi env, migrasi, seeder admin, serta jebakan serverless."
is_published: true
---

Situs ini berjalan di Vercel dengan Supabase sebagai database. Vercel menjalankan aplikasi lewat container, sedangkan Supabase menyediakan PostgreSQL managed. Kombinasi ini cocok untuk proyek personal karena tidak perlu menyewa VPS, tetapi ada beberapa jebakan khas lingkungan serverless yang harus disiapkan sejak awal.

Artikel ini mencatat alur deploy yang dipakai repository ini: dari menyiapkan database Supabase, mengatur variabel lingkungan di Vercel, sampai menjalankan migrasi dan membuat akun admin.

## Kenapa Vercel dan Supabase

Singkatnya, keduanya membagi tugas sesuai kekuatannya:

- Vercel menangani build aset, image container, domain, HTTPS, dan scaling.
- Supabase menangani data, sesi login, dan cache agar aplikasi tetap stateless.
- File Markdown blog tetap di repository, lalu disinkronkan ke database.

Penting untuk dipahami bahwa filesystem container Vercel bersifat sementara. Apa pun yang ditulis ke `storage/` bisa hilang saat deploy ulang atau instance baru dibuat. Karena itu semua state aplikasi dititipkan ke Supabase.

## Menyiapkan database Supabase

Buat project baru di dashboard Supabase, lalu buka bagian **Project Settings → Database** dan catat empat nilai berikut:

- **Host** — hostname database, biasanya mirip `db.<project-ref>.supabase.co`.
- **Port** — `5432` untuk koneksi langsung.
- **Database** — defaultnya `postgres`.
- **Username dan password** — password dibuat saat project pertama kali dibuat.

Untuk koneksi serverless, Supabase juga menyediakan **connection pooler** pada port `6543`. Pooler berguna kalau jumlah instance Vercel bertambah, karena PostgreSQL punya batas jumlah koneksi. Di Vercel, satu instance container bisa membuka beberapa koneksi, jadi putuskan sesuai beban:

| Mode | Port | Cocok untuk |
| --- | --- | --- |
| Direct connection | `5432` | Migrasi, seeder, dan trafik kecil |
| Transaction pooler | `6543` | Trafik serverless dengan banyak instance |

Kalau memakai pooler, koneksi bersifat mode transaksi sehingga prepared statement harus dimatikan. Tambahkan `?sslmode=require` pada URL, dan pastikan `DB_PORT` diisi `6543`.

Supabase mewajibkan SSL untuk koneksi publik. Aplikasi ini membaca `DB_SSLMODE` dengan default `prefer`, jadi set nilainya ke `require` di produksi.

## Menyiapkan repository untuk Vercel

Vercel tidak menjalankan PHP secara native, sehingga aplikasi dibungkus container memakai FrankenPHP. Dua file yang harus ada di root repository:

### `Dockerfile.vercel`

Dockerfile ini multi-stage. Tahap pertama membangun aset Vite, tahap kedua menyiapkan dependency Composer, dan tahap terakhir menjalankan FrankenPHP sebagai pengguna `www-data`.

```bash
FROM node:22-bookworm AS assets
WORKDIR /app
COPY package.json package-lock.json .npmrc ./
RUN npm ci
COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY resources ./resources
RUN npm run build
```

Tahap dependency memasang ekstensi yang dibutuhkan PostgreSQL, yaitu `pdo_pgsql` dan `pgsql`, lalu menjalankan cache artisan:

```bash
composer install --no-dev --no-scripts --prefer-dist
composer dump-autoload --no-dev --classmap-authoritative
php artisan event:cache
php artisan route:cache
php artisan view:cache
```

Cache di build ini sengaja dilakukan karena container tidak punya `storage/` yang persisten. Kalau tidak, setiap request menulis ulang cache view ke filesystem yang sementara.

### `vercel.json`

File ini mendaftarkan Dockerfile sebagai service dan meneruskan semua request ke container tersebut:

```json
{
    "$schema": "https://openapi.vercel.sh/vercel.json",
    "regions": ["icn1"],
    "services": {
        "app": {
            "root": ".",
            "runtime": "container",
            "entrypoint": "Dockerfile.vercel"
        }
    },
    "rewrites": [
        {
            "source": "/(.*)",
            "destination": {
                "service": "app"
            }
        }
    ]
}
```

Field `regions` sebaiknya diletakkan dekat dengan database Supabase. Contoh di atas memakai Seoul (`icn1`) karena region Supabase yang dipakai juga di Asia. Jarak antara container dan database sangat memengaruhi latensi query, jadi pilih region yang berdekatan.

## Variabel lingkungan di Vercel

Buka **Project Settings → Environment Variables** dan isi variabel berikut pada scope Production. Nilai rahasia seperti password database dan API key sebaiknya tidak pernah masuk ke repository.

| Variabel | Nilai di produksi |
| --- | --- |
| `APP_NAME` | Nama situs |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | Hasil `php artisan key:generate --show` |
| `APP_URL` | Domain produksi, misalnya `https://contoh.vercel.app` |
| `DB_CONNECTION` | `pgsql` |
| `DB_HOST` | Host database atau pooler Supabase |
| `DB_PORT` | `5432` atau `6543` untuk pooler |
| `DB_DATABASE` | `postgres` |
| `DB_USERNAME` | Username database Supabase |
| `DB_PASSWORD` | Password database Supabase |
| `DB_SSLMODE` | `require` |
| `SESSION_DRIVER` | `database` |
| `CACHE_STORE` | `database` |
| `QUEUE_CONNECTION` | `sync` |
| `ADMIN_NAME` | Nama akun admin |
| `ADMIN_EMAIL` | Email login admin |
| `ADMIN_PASSWORD` | Password login admin |

`APP_KEY` hanya dibuat sekali. Jangan membuat ulang key setiap deploy, karena key lama dipakai untuk membuka data terenkripsi seperti sesi yang sudah ada.

Baris default Supabase berbentuk `postgresql://postgres:[YOUR-PASSWORD]@db.<project-ref>.supabase.co:5432/postgres`. Laravel bisa membacanya langsung lewat `DB_URL`, tetapi memecahnya ke `DB_HOST`, `DB_PORT`, `DB_USERNAME`, dan `DB_PASSWORD` biasanya lebih mudah saat troubleshooting.

## Menjalankan migrasi dan seeder

Container Vercel tidak punya shell interaktif, jadi migrasi dijalankan dari mesin lokal yang mengarah ke database Supabase. Cara paling aman adalah membuat salinan `.env` khusus deploy yang tidak ikut di-commit, misalnya `.env.deploy` (sudah masuk `.gitignore`).

Isi `.env.deploy` dengan nilai yang sama seperti variabel Vercel, lalu jalankan:

```bash
php artisan migrate --force --env=deploy
php artisan db:seed --class=AdminSeeder --force --env=deploy
```

Opsi `--force` wajib karena Laravel menolak perintah destruktif di environment non-lokal tanpa konfirmasi. Setelah akun admin ada, login lewat `/login` memakai `ADMIN_EMAIL` dan `ADMIN_PASSWORD`.

Kalau memakai database baru, jangan lupa mengisi konten awal:

```bash
php artisan blog:sync --env=deploy
php artisan portfolio:import-github --publish --include-forks --env=deploy
```

Perintah `blog:sync` membaca front matter setiap file di `resources/content/blog` lalu menulisnya ke tabel `posts`. Artikel yang slug-nya sama akan diperbarui, bukan diduplikasi.

## Deploy dan deploy ulang

Cara tercepat adalah menghubungkan repository GitHub ke Vercel, sehingga setiap push ke branch `main` memicu build baru. Untuk deploy manual dari mesin lokal, cukup jalankan:

```bash
npx vercel --prod
```

Setelah build selesai, uji beberapa halaman penting:

- `/` menampilkan beranda tanpa error 500.
- `/blog` membaca artikel dari database Supabase.
- `/login` bisa menampilkan form dan menerima login admin.
- `/admin` bisa dibuka setelah login.

Jika muncul error 500, buka **Deployments → Logs** di Vercel. Log aplikasi dikirim ke stderr berkat `LOG_CHANNEL=stderr` di Dockerfile, jadi pesan Laravel langsung terlihat di sana.

## Jebakan yang perlu diwaspadai

Beberapa hal berikut paling sering bikin deploy pertama gagal:

- **Sesi hilang setelah login.** Biasanya karena `SESSION_DRIVER` masih `file`. Pastikan tabel `sessions` sudah ada dan driver diubah ke `database`.
- **Error koneksi database saat build.** Cache artisan di Dockerfile tidak boleh menyentuh database. Karena itu `QUEUE_CONNECTION` di production diset `sync` dan seeder tidak dijalankan otomatis saat build.
- **Thumbnail atau file unggahan hilang.** Filesystem container sementara, jadi file yang diunggah lewat panel admin akan hilang saat redeploy. Untuk produksi, arahkan disk publik ke object storage.
- **`APP_KEY` berbeda tiap deploy.** Jangan pernah generate key saat build. Simpan satu nilai di Environment Variables Vercel.
- **Timeout saat migrasi.** Pooler mode transaksi bisa memutus statement panjang. Jalankan migrasi lewat koneksi langsung port `5432`, bukan pooler.

## Ringkasan alur

Urutan yang paling aman untuk deploy pertama:

1. Buat project Supabase, catat host, port, user, dan password.
2. Tambahkan `Dockerfile.vercel` dan `vercel.json` ke repository.
3. Isi Environment Variables di Vercel, termasuk `APP_KEY` dan kredensial Supabase.
4. Push ke `main` atau jalankan `npx vercel --prod`.
5. Jalankan `migrate`, seeder admin, dan `blog:sync` dari lokal via `.env.deploy`.
6. Uji beranda, blog, login, dan panel admin.

Setelah alur ini berjalan, setiap perubahan kode cukup lewat push ke `main`. Artikel blog tetap dikelola sebagai file Markdown lalu disinkronkan dengan `php artisan blog:sync --env=deploy`.
