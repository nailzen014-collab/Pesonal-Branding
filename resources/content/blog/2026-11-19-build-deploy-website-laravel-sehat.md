---
title: "Build dan Deploy Website Laravel yang Sehat"
slug: build-deploy-website-laravel-sehat
excerpt: "Checklist praktis agar proses build bisa diulang, deploy aman untuk data, dan website tetap mudah dipantau setelah rilis."
category: Deployment
published_at: 2026-11-19 18:00
sort_order: 9
meta_description: "Panduan build dan deploy Laravel yang sehat: tes sebelum rilis, pisahkan build dari migrasi database, kelola environment, cek endpoint, dan siapkan pemulihan."
is_published: true
---

Website yang berhasil di-deploy belum tentu siap dipakai. Halaman utama mungkin terbuka, tetapi login bisa gagal, aset CSS belum ikut ter-build, atau aplikasi tidak bisa menyimpan data setelah container diganti.

Karena itu, proses rilis sebaiknya tidak berhenti saat platform menampilkan status **Ready**. Build perlu bisa diulang, perubahan database harus aman, dan fitur penting harus diperiksa setelah rilis.

Di proyek ini, Laravel dijalankan di container FrankenPHP pada Vercel, aset frontend dibangun dengan Vite, dan data produksi disimpan di PostgreSQL Supabase. Prinsip di bawah berangkat dari alur tersebut, tetapi tetap relevan untuk banyak aplikasi Laravel lain.

## Bedakan build, deploy, dan pemeriksaan

Tiga tahap ini punya tanggung jawab berbeda:

| Tahap | Tugas utama | Contoh pemeriksaan |
| --- | --- | --- |
| Build | Menyiapkan kode dan aset menjadi artefak yang siap dijalankan | Dependency terpasang, Vite berhasil, cache Laravel bisa dibuat |
| Deploy | Menjalankan versi baru dengan konfigurasi lingkungan yang benar | Container aktif, kredensial tersedia, migrasi kompatibel |
| Verifikasi | Memastikan fitur penting tetap bekerja setelah rilis | Halaman publik, login, data, dan log aplikasi |

Memisahkan tahap-tahap ini membuat sumber masalah lebih mudah ditemukan. Jika `npm run build` gagal, tidak perlu menyelidiki koneksi database produksi. Jika build berhasil tetapi login gagal, perhatian bisa diarahkan ke sesi, konfigurasi, atau database.

## Buat build yang konsisten dan bisa diulang

Gunakan lockfile dan perintah instalasi yang mengikutinya. Di repository ini, build frontend memakai `npm ci`, lalu menjalankan `npm run build`. `npm ci` memasang versi dependency sesuai `package-lock.json`; hasilnya lebih konsisten dibanding membiarkan versi dependency berubah sendiri.

Sebelum mendorong perubahan, jalankan pemeriksaan dasar:

```bash
php artisan test
vendor/bin/pint --test
npm run build
```

Tes Laravel membantu menangkap regresi pada aplikasi, Pint memeriksa format kode PHP, dan build Vite memastikan aset produksi dapat dibuat. Ketiganya memeriksa hal yang berbeda, jadi build yang sukses saja belum menggantikan tes.

Di Dockerfile proyek ini, Node.js digunakan pada tahap build aset. Hasil `public/build` kemudian disalin ke image runtime bersama aplikasi. Dependency Composer untuk development tidak dipasang pada image produksi. Pola multi-stage ini menjaga image akhir berisi kebutuhan runtime, bukan seluruh perkakas development.

Yang penting, build sebaiknya tidak membutuhkan database produksi. Jangan menjalankan seeder atau migrasi produksi sebagai bagian dari proses membuat image: proses build harus bisa dijalankan ulang tanpa mengubah data dan tanpa bergantung pada ketersediaan layanan produksi.

## Pisahkan konfigurasi dan rahasia dari kode

Konfigurasi berbeda antara lokal dan produksi. Simpan nilai produksi di Environment Variables milik platform hosting, bukan di repository. Jangan memasukkan `.env`, password database, token API, atau kunci aplikasi ke image maupun file yang di-commit.

Untuk produksi Laravel, periksa setidaknya hal berikut:

- `APP_ENV=production` dan `APP_DEBUG=false`.
- `APP_KEY` tetap sama pada setiap deploy. Mengganti key dapat membuat data terenkripsi atau sesi lama tidak bisa dibaca.
- `APP_URL` menunjuk ke domain yang benar.
- Kredensial database mengarah ke database produksi dan koneksinya memakai SSL sesuai ketentuan penyedia database.
- Driver sesi dan cache sesuai dengan lingkungan. Container serverless tidak boleh bergantung pada file lokal yang hanya tersedia di satu instance.

Saat nilai konfigurasi berubah, buat deploy baru bila platform menerapkan Environment Variables saat build atau startup. Periksa deployment log untuk memastikan aplikasi memang membaca konfigurasi terbaru, tanpa mencetak nilai rahasianya.

## Rilis perubahan database dengan aman

Build aplikasi dan perubahan skema database adalah dua hal yang berkaitan, tetapi tidak harus dijalankan di tahap yang sama. Untuk produksi, jalankan migrasi secara eksplisit setelah memastikan versi aplikasi baru kompatibel dengan skema yang sedang aktif.

Contoh jika proyek memakai file `.env.deploy` untuk menunjuk ke database deploy:

```bash
php artisan migrate --force --env=deploy
```

Jangan gunakan `migrate:fresh` pada database produksi karena perintah itu menghapus tabel sebelum membuat ulang skema. Untuk perubahan berisiko atau data penting, siapkan backup dan prosedur pemulihan sebelum migrasi.

Pada perubahan yang perlu diluncurkan bertahap, utamakan migrasi yang kompatibel ke belakang:

1. Tambahkan kolom atau tabel baru tanpa langsung menghapus struktur lama.
2. Deploy kode yang bisa bekerja dengan struktur lama maupun baru.
3. Isi atau pindahkan data jika diperlukan.
4. Hapus struktur lama pada rilis berikutnya setelah dipastikan tidak dipakai.

Urutan ini mengurangi kemungkinan versi aplikasi yang sedang berjalan gagal ketika perubahan skema diterapkan.

## Pahami batas container

Container produksi dapat diganti saat deploy atau saat platform membuat instance baru. Karena itu, filesystem lokal container tidak cocok sebagai tempat penyimpanan permanen untuk unggahan pengguna, sesi, atau data penting.

Gunakan layanan yang memang tahan terhadap pergantian instance:

- Database terkelola untuk data aplikasi.
- Object storage untuk foto dan dokumen yang diunggah.
- Cache atau session store bersama bila aplikasi memerlukannya.

Proyek ini memakai Supabase untuk PostgreSQL dan mengatur log Laravel ke `stderr`, sehingga log aplikasi dapat dikumpulkan oleh platform. Jangan menganggap file di `storage/` otomatis bertahan setelah container diganti.

## Verifikasi setelah deploy

Sesudah rilis, lakukan smoke test terhadap alur utama—tes singkat yang memeriksa perilaku nyata tanpa mengulang seluruh test suite.

Untuk proyek ini, urutannya bisa seperti berikut:

1. Buka `/up` untuk memastikan aplikasi merespons.
2. Buka halaman utama dan pastikan aset tampil dengan benar.
3. Buka `/blog` dan satu artikel, lalu pastikan data terbaca dari database.
4. Buka `/login`, masuk dengan akun admin yang sah, lalu periksa dashboard.
5. Uji satu operasi tulis yang aman di admin bila perubahan rilis menyentuh penyimpanan data.
6. Periksa log deployment untuk error 500, koneksi database, atau exception baru.

Endpoint `/up` berguna sebagai pemeriksaan dasar bahwa aplikasi hidup. Namun, respons sukses dari endpoint itu sendiri belum membuktikan bahwa database, login, dan semua fitur bisnis berfungsi. Karena itu, tetap cek halaman dan alur yang paling penting bagi pengguna.

Hindari smoke test yang mengirim pesan nyata, email, atau transaksi sungguhan. Gunakan data uji yang aman dan punya cara pembersihan yang jelas.

## Siapkan cara menangani deploy bermasalah

Kalau pemeriksaan setelah rilis gagal, kumpulkan bukti sebelum mengubah banyak hal sekaligus:

- Catat deployment yang bermasalah dan waktu kejadiannya.
- Periksa log aplikasi dan platform untuk mendapatkan exception pertama.
- Bedakan error aplikasi dari masalah konfigurasi atau konektivitas database.
- Jika layanan benar-benar terganggu, gunakan fitur rollback platform ke versi aplikasi sebelumnya.
- Pastikan versi lama tetap kompatibel dengan migrasi yang sudah dijalankan.

Rollback kode tidak otomatis mengembalikan database. Karena itu, migrasi destruktif perlu rencana tersendiri; jangan mengasumsikan memulihkan container akan membatalkan perubahan data.

## Checklist sebelum dan sesudah rilis

### Sebelum deploy

- [ ] Tes Laravel berhasil.
- [ ] Pemeriksaan format dan build aset berhasil.
- [ ] Tidak ada rahasia atau file `.env` produksi di commit.
- [ ] Migrasi kompatibel dengan data yang sudah ada.
- [ ] Backup dan cara pemulihan dipahami untuk perubahan berisiko.
- [ ] Environment Variables produksi sudah benar.

### Setelah deploy

- [ ] Deployment berstatus siap dan endpoint `/up` merespons.
- [ ] Halaman publik, blog, login, dan dashboard bisa digunakan.
- [ ] Aset CSS dan JavaScript termuat.
- [ ] Tidak ada error baru yang berulang di log.
- [ ] Ada keputusan yang jelas untuk melanjutkan, memperbaiki, atau rollback.

Deploy yang sehat bukan deploy yang tidak pernah gagal. Yang lebih penting adalah kegagalan bisa dideteksi dengan cepat, dampaknya dibatasi, dan proses kembali ke kondisi aman sudah diketahui sebelum masalah terjadi.
