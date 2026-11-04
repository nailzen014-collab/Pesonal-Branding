---
title: "Mengenali N+1 Query di Eloquent Sebelum Jadi Masalah"
slug: mengenali-n-plus-one-eloquent
excerpt: "Pola query yang sering tidak terlihat saat data masih sedikit, tapi memperlambat halaman saat data bertambah."
category: Laravel
published_at: 2026-08-12 09:45
sort_order: 5
meta_description: "Cara mengenali dan memperbaiki masalah N+1 query di Eloquent dengan eager loading."
is_published: true
---

Masalah N+1 query adalah salah satu penyebab halaman Laravel terasa lambat yang paling sering luput. Saat data masih sedikit, semuanya terasa cepat. Begitu jumlah data bertambah, jumlah query ikut bertambah dan waktu muat melonjak.

## Bagaimana pola ini muncul

Misalnya kita menampilkan daftar proyek beserta teknologinya:

```php
$projects = Project::published()->get();

foreach ($projects as $project) {
    echo $project->technologies->count();
}
```

Kode di atas menjalankan satu query untuk mengambil proyek, lalu satu query tambahan untuk setiap proyek saat relasi diakses. Jika ada 30 proyek, totalnya bisa 31 query.

## Perbaikan dengan eager loading

Gunakan `with()` untuk mengambil relasi sekaligus:

```php
$projects = Project::published()
    ->with('technologies')
    ->get();
```

Jumlah query turun menjadi dua, berapa pun jumlah proyeknya.

## Cara memeriksa

Saat development, aktifkan pencatatan query atau gunakan debugbar. Lihat apakah jumlah query bertambah seiring jumlah baris yang ditampilkan. Kalau bertambah mengikuti data, kemungkinan besar ada relasi yang diakses di dalam loop.

## Jangan berlebihan

Eager loading juga bisa berlebihan jika relasi yang diambil tidak dipakai. Ambil relasi yang benar-benar ditampilkan, dan batasi kolom bila datanya besar. Tujuannya bukan menghilangkan semua query, tetapi memastikan jumlahnya tidak tumbuh tanpa kendali.
