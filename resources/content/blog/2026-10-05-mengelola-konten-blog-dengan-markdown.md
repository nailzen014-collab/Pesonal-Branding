---
title: "Mengelola Konten Blog dengan Markdown di Laravel"
slug: mengelola-konten-blog-dengan-markdown
excerpt: "Menyimpan artikel sebagai file Markdown di repository, lalu menyinkronkannya ke database Laravel."
category: Tutorial
published_at: 2026-10-05 09:00
sort_order: 1
meta_description: "Cara mengelola artikel blog Laravel dari file Markdown: struktur front matter, perintah sinkronisasi, dan alur publikasinya."
is_published: true
---

Blog ini tidak saya isi lewat form admin setiap kali ingin menulis. Artikelnya disimpan sebagai file Markdown di dalam repository, lalu disinkronkan ke database dengan satu perintah. Cara ini membuat konten ikut tercatat di Git, mudah di-review, dan tetap bisa tampil cepat karena halaman publik membaca database.

## Kenapa memakai file Markdown

Ada beberapa alasan yang membuat alur ini nyaman untuk proyek personal:

- Riwayat perubahan artikel tersimpan rapi di Git.
- Menulis terasa ringan karena tidak perlu membuka browser.
- Artikel bisa dipulihkan kapan saja dengan `php artisan migrate --seed`.
- Tampilan publik tetap memakai database, jadi pencarian dan pagination tetap bekerja.

## Struktur file

Setiap artikel berada di `resources/content/blog`. Bagian paling atas berisi front matter, yaitu metadata sederhana yang dipisahkan dari isi artikel dengan `---`.

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

## Menyinkronkan ke database

Setelah menambah atau mengubah file, jalankan:

```bash
php artisan blog:sync
```

Perintah itu akan membuat artikel baru atau memperbarui artikel yang slug-nya sama. File yang tidak punya front matter lengkap akan dilewati supaya tidak menghasilkan artikel setengah jadi.

## Kapan memakai panel admin

Panel admin tetap berguna untuk mengunggah thumbnail atau memperbaiki artikel secara cepat. Perlu diingat, artikel yang berasal dari file Markdown akan kembali ke isi file saat sinkronisasi berikutnya. Jadi untuk artikel yang dikelola lewat file, jadikan file sebagai sumber utama.
