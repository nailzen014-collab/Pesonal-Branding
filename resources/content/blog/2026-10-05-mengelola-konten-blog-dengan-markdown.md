---
title: "Mengelola Konten Blog dengan Markdown di Laravel"
slug: mengelola-konten-blog-dengan-markdown
excerpt: "Alur lengkap menulis artikel sebagai file Markdown, menyinkronkannya ke database, dan mempublikasikannya lewat panel admin maupun scheduler."
category: Tutorial
published_at: 2026-10-05 09:00
sort_order: 1
meta_description: "Alur lengkap mengelola artikel blog Laravel dari file Markdown: sumber konten, struktur front matter, perintah sinkronisasi, scheduler, tombol admin, dan solusi saat sinkronisasi gagal."
is_published: true
---

Blog ini tidak saya isi lewat form admin setiap kali ingin menulis. Artikelnya disimpan sebagai file Markdown di dalam repository, lalu disinkronkan ke database dengan satu perintah. Cara ini membuat konten ikut tercatat di Git, mudah di-review, dan tetap bisa tampil cepat karena halaman publik membaca database.

Catatan ini sengaja ditulis lengkap dari ujung ke ujung, supaya saat lupa alurnya tinggal dibaca ulang tanpa perlu membongkar kode.

## Kenapa memakai file Markdown

Ada beberapa alasan yang membuat alur ini nyaman untuk proyek personal:

- Riwayat perubahan artikel tersimpan rapi di Git.
- Menulis terasa ringan karena tidak perlu membuka browser.
- Artikel bisa dipulihkan kapan saja dengan `php artisan migrate --seed`.
- Tampilan publik tetap memakai database, jadi pencarian dan pagination tetap bekerja.

## Dua sumber artikel

Artikel bisa datang dari dua tempat, dan keduanya memakai format front matter yang sama karena diproses oleh `MarkdownPostImporter` yang sama.

| Sumber | Lokasi file | Cara sinkron |
| --- | --- | --- |
| Lokal | `resources/content/blog/*.md` di proyek ini | `php artisan blog:sync` |
| GitHub | folder `blog/` di repository publik | `php artisan blog:import-github` atau tombol admin |

Untuk sumber GitHub, yang dibaca adalah **semua repository publik milik akun `GITHUB_USERNAME`**, ditambah repo lain yang didaftarkan di `GITHUB_BLOG_REPOS` (format `owner/repo`, dipisah koma). Repo itu tidak harus proyek Laravel — asal punya folder bernama `blog` berisi file `.md`, artikelnya akan diambil.

## Alur lengkap dari menulis sampai tampil

1. **Tulis artikel sebagai file `.md`** di salah satu sumber di atas. Nama file bebas, tapi konvensi yang saya pakai: `YYYY-MM-DD-slug-artikel.md`.
2. **Isi front matter** di bagian paling atas file (lihat bagian berikutnya).
3. **Uji di lokal** dengan menjalankan `php artisan blog:sync` (lokal) atau `php artisan blog:import-github` (GitHub).
4. **Commit dan push** kalau sumbernya repo lokal/GitHub.
5. **Jalankan sinkronisasi** ke database produksi: klik **Sinkronkan Semua** di `/admin/github`, atau tunggu scheduler berjalan.
6. **Cek hasilnya** di halaman `/blog`. Artikel dengan `is_published: true` langsung tampil; kalau `false`, disimpan sebagai draf di panel admin.

Ringkasnya: file Markdown adalah sumber kebenaran, tabel `posts` adalah salinan yang dipakai halaman publik, dan sinkronisasi adalah jembatan di antara keduanya.

## Struktur file dan front matter

Front matter adalah blok metadata paling atas file, dipisahkan dari isi artikel dengan `---`.

```text
---
title: "Judul Artikel"
slug: judul-artikel
excerpt: "Ringkasan singkat."
category: Tutorial
published_at: 2026-10-05 09:00
sort_order: 1
is_published: true
---
```

Setelah front matter, tulis isi artikel memakai sintaks Markdown biasa. Judul bagian memakai `##`, daftar memakai tanda `-`, dan kode memakai backtick.

Format pembacaannya sederhana: satu pasangan `kunci: nilai` per baris. Tanda kutip boleh dipakai boleh tidak, dan baris yang diawali `#` diabaikan. Field yang dikenali:

| Field | Wajib | Keterangan |
| --- | --- | --- |
| `title` | Ya | Tanpa `title`, file dilewati dan tidak menjadi artikel. |
| `slug` | Tidak | Kalau kosong, dibuat otomatis dari `title`. |
| `excerpt` | Tidak | Ringkasan untuk kartu artikel dan meta description. |
| `category` | Tidak | Misalnya `Tutorial` atau `Catatan`. |
| `thumbnail` | Tidak | Path atau URL gambar sampul. |
| `published_at` | Tidak | Format `YYYY-MM-DD HH:MM`. Kosong berarti tanpa tanggal tayang. |
| `sort_order` | Tidak | Angka urutan di daftar blog, default `0`. |
| `is_published` | Tidak | Default `true`. Set `false` untuk draf. |
| `meta_description` | Tidak | Deskripsi untuk SEO. |

## Sinkronisasi dari folder lokal

Setelah menambah atau mengubah file di `resources/content/blog`, jalankan:

```bash
php artisan blog:sync
```

Perintah ini membaca semua file `.md` di folder tersebut, lalu:

- Membuat artikel baru bila slug belum ada di database.
- Memperbarui artikel yang sudah ada bila slug-nya sama (tidak menduplikasi).
- Melewati file yang front matter-nya tidak valid, misalnya tanpa `title`.

File yang filenya sudah dihapus **tidak** ikut menghapus artikel di database. Ini disengaja supaya artikel yang dibuat lewat panel admin tidak ikut hilang.

## Sinkronisasi dari GitHub

Kalau sumbernya repository GitHub, perintahnya:

```bash
php artisan blog:import-github
```

Opsi yang sering dipakai:

- `--fresh` — abaikan cache dan ambil ulang daftar repo serta daftar file dari GitHub.
- `--user=namaakun` — menimpa `GITHUB_USERNAME` untuk sekali jalan.

Yang dilakukan perintah ini, berurutan:

1. Mengambil daftar repository publik milik akun dari GitHub API.
2. Menambah repo dari `GITHUB_BLOG_REPOS` bila ada.
3. Mencari semua file `.md` di folder `blog` tiap repository.
4. Mengunduh isi file, membaca front matter, lalu menyimpan seperti impor lokal.
5. Menghitung hasil per repo: berapa file, berapa baru, diperbarui, dilewati, dan konflik.

Folder `blog` dikenali di kedalaman mana pun, jadi `blog/artikel.md`, `content/blog/artikel.md`, dan `resources/content/blog/artikel.md` sama-sama terbaca.

**Soal slug yang sama di dua repo:** repo yang diproses lebih dulu menang. Repo berikutnya yang punya slug sama akan dicatat sebagai konflik dan tidak menimpa tulisan pertama. Kalau mau dua artikel serupa tetap tayang, beri slug berbeda.

## Sinkron otomatis lewat scheduler

Sinkronisasi juga dijadwalkan di `routes/console.php`:

- `portfolio:import-github --publish --include-forks` setiap jam (menit ke-0) — memperbarui daftar proyek dari repo.
- `blog:import-github` setiap jam (menit ke-5) — mengambil artikel dari folder `blog`.

Supaya berjalan, server harus memanggil `php artisan schedule:run` setiap menit lewat cron. Di Vercel hal ini tidak otomatis; untuk sekarang tombol **Sinkronkan Semua** adalah cara paling praktis, atau pasang cron yang memanggil endpoint sinkronisasi.

Ada juga webhook `POST /github/webhook` yang dijalankan GitHub saat sebuah repository baru dibuat, dan memicu impor proyek. Webhook ini diverifikasi memakai `GITHUB_WEBHOOK_SECRET`.

## Tombol di panel admin

Halaman `/admin/github` punya tombol **Sinkronkan Semua** yang menjalankan dua hal sekaligus:

1. Impor seluruh repository menjadi proyek portofolio.
2. Impor artikel dari folder `blog` semua repository.

Setelah selesai, halaman menampilkan **Hasil analisis artikel** per repository: jumlah file, artikel baru, pembaruan, yang dilewati, dan konflik slug. Baris yang gagal dibaca akan menampilkan pesan error beserta alasannya.

## Aturan yang perlu diingat

- **Slug adalah kunci.** Satu slug sama dengan satu artikel. Ganti slug berarti artikel baru; pertahankan slug untuk memperbarui.
- **Sumber utama adalah file.** Artikel yang berasal dari Markdown akan kembali ke isi file saat sinkronisasi berikutnya, termasuk menimpa perubahan kecil yang dibuat lewat panel admin.
- **Panel admin tetap berguna** untuk mengunggah thumbnail, menulis artikel cepat, atau menyembunyikan artikel tanpa mengubah file.
- **File tanpa `title` dilewati** dengan aman, jadi README atau catatan biasa di folder `blog` tidak akan ikut masuk.
- **Menghapus file tidak menghapus artikel**, baik dari folder lokal maupun dari GitHub.

## Kalau sinkronisasi gagal

- **Hasil "0 artikel, 0 repository yang dibaca"** — hampir selalu berarti daftar repository gagal diambil dari GitHub, bukan berarti repo-nya tidak ada. Cek pesan error di halaman admin.
- **HTTP 401** — `GITHUB_TOKEN` tidak valid atau sudah kedaluwarsa. Perbarui tokennya.
- **HTTP 403** — kuota request GitHub habis. Tanpa token batasnya hanya 60 request per jam, padahal sekali sinkronisasi bisa memakai puluhan request. Isi `GITHUB_TOKEN` agar batasnya naik ke 5.000 per jam.
- **HTTP 404** — `GITHUB_USERNAME` salah, biasanya karena ikut terbawa tanda kutip saat dipasang di dashboard hosting.
- **Artikel tidak muncul padahal file ada** — pastikan file berekstensi `.md`, berada di folder bernama `blog`, punya `title`, dan `is_published` tidak `false`.

## Checklist menulis artikel baru

1. Buat file `YYYY-MM-DD-slug.md` dengan front matter berisi minimal `title`.
2. Jalankan `php artisan blog:sync` (lokal) atau `php artisan blog:import-github --fresh` (GitHub).
3. Push ke repository bila perlu.
4. Klik **Sinkronkan Semua** di `/admin/github` untuk lingkungan produksi.
5. Buka `/blog` dan pastikan artikelnya tampil.
