---
title: "Blade Component: Cara Menjaga Tampilan Tetap Konsisten"
slug: blade-component-tampilan-konsisten
excerpt: "Memecah markup yang berulang menjadi komponen Blade supaya halaman lebih mudah dirawat."
category: Laravel
published_at: 2026-09-22 10:30
sort_order: 2
meta_description: "Manfaat Blade Component untuk menjaga konsistensi tampilan dan mengurangi markup berulang di Laravel."
is_published: true
---

Saat halaman bertambah, markup yang sama sering muncul di banyak tempat: tombol, kartu, label, dan input form. Kalau semuanya ditulis ulang, satu perubahan desain bisa berarti mengedit belasan file. Blade Component menyelesaikan masalah itu dengan cara yang sederhana.

## Mulai dari elemen yang paling sering dipakai

Tidak perlu langsung membuat komponen untuk semua hal. Lihat dulu elemen yang benar-benar berulang. Di proyek ini, tombol dan input form adalah kandidat pertama karena dipakai di hampir setiap halaman.

```blade
<x-ui.button :href="route('kontak')" icon="mail">
    Hubungi Saya
</x-ui.button>
```

Komponen menyembunyikan detail kelas Tailwind, sehingga halaman cukup menyebut varian yang diinginkan. Kalau nanti warna tombol berubah, cukup ubah satu file komponen.

## Bedakan komponen dan partial

Komponen cocok untuk potongan UI yang punya perilaku atau varian sendiri. Partial lebih cocok untuk bagian halaman yang hanya dibagi agar file tidak terlalu panjang. Menentukan batasnya sejak awal membuat struktur tetap mudah dipahami.

## Jangan membuat abstraksi terlalu dini

Komponen yang dibuat sebelum ada kebutuhan nyata biasanya malah menambah lapisan. Pola yang saya pakai: tulis dulu secara langsung, dan ketika potongan yang sama muncul untuk ketiga kalinya, baru jadikan komponen.

Dengan aturan sederhana itu, jumlah komponen tetap terkendali dan setiap komponen benar-benar punya alasan untuk ada.
