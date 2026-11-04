---
title: "Tema Gelap dengan Tailwind CSS yang Tetap Konsisten"
slug: tema-gelap-tailwind-konsisten
excerpt: "Memakai design token agar warna gelap tidak tersebar sebagai nilai acak di setiap komponen."
category: Tailwind
published_at: 2026-09-10 08:15
sort_order: 3
meta_description: "Cara mengelola tema gelap Tailwind CSS dengan design token agar warna tetap konsisten dan mudah diganti."
is_published: true
---

Tema gelap bukan sekadar mengganti latar menjadi hitam. Kalau setiap komponen memakai nilai warna sendiri, hasilnya cepat terasa tidak konsisten: ada abu-abu yang terlalu terang, ada garis yang terlalu samar, dan ada tombol yang warnanya berbeda dari halaman lain.

## Pakai nama warna, bukan nilai hex

Di Tailwind, warna bisa diberi nama yang mewakili perannya. Contohnya `ink` untuk latar halaman, `surface` untuk kartu, `line` untuk garis, dan `muted` untuk teks sekunder. Nama seperti ini membuat kode lebih mudah dibaca karena menjelaskan fungsi, bukan hanya nilai.

```html
<div class="rounded-2xl border border-line bg-surface text-text">
    <p class="text-muted">Teks sekunder</p>
</div>
```

## Batasi jumlah tingkat warna

Terlalu banyak tingkat abu-abu membuat desain sulit dijaga. Saya membatasi menjadi beberapa peran saja:

- `ink` untuk latar paling belakang.
- `surface` untuk kartu dan panel.
- `line` untuk batas.
- `text` untuk teks utama.
- `muted` untuk teks pendukung.

Dengan pembatasan itu, setiap elemen baru tinggal memilih peran yang sesuai, tanpa menambah warna baru.

## Perhatikan kontras

Tema gelap sering tergoda memakai teks abu-abu gelap agar terlihat elegan. Padahal teks tetap harus terbaca. Periksa kontras teks utama dan teks sekunder terhadap latarnya, terutama pada ukuran kecil seperti label dan metadata.

## Siapkan jalan untuk mode terang

Kalau warna ditulis sebagai design token, menambahkan mode terang nanti hanya perlu mengganti nilai token, bukan membongkar setiap halaman. Itu alasan utama saya menahan diri dari nilai warna acak sejak awal.
