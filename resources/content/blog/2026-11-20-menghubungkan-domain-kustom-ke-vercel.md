---
title: "Cara Menghubungkan Domain Kustom ke Vercel dan Alur Auto-Deploy"
slug: menghubungkan-domain-kustom-ke-vercel
excerpt: "Panduan memilih registrar domain, mengatur DNS record di Vercel, serta memahami bagaimana pembaruan kode dari VS Code otomatis tayang ke domain utama."
category: Tutorial
published_at: 2026-11-20 10:26
sort_order: 2
is_published: true
meta_description: "Tutorial lengkap cara menyambungkan domain kustom (.com, .id, .my.id) ke proyek Vercel dan alur update otomatis lewat GitHub."
---

## Pendahuluan

Saat pertama kali men-deploy website ke Vercel, proyek kita akan mendapatkan subdomain bawaan seperti `nama-proyek.vercel.app`. Untuk kebutuhan personal branding atau portofolio profesional, penggunaan domain kustom jauh lebih direkomendasikan.

Kabar baiknya, **semua ekstensi domain bisa disambungkan ke Vercel**, baik ekstensi global maupun lokal Indonesia.

## Rekomendasi Tempat Membeli Domain

Sebelum menghubungkannya ke Vercel, kamu perlu membeli domain terlebih dahulu di penyedia domain (*registrar*):

- **Penyedia Lokal (Indonesia):** Niagahoster, DomaiNesia, Rumahweb, atau IDCloudHost. Sangat cocok jika mengincar ekstensi `.id`, `.co.id`, atau `.my.id` dengan pembayaran mudah via QRIS maupun Virtual Account.
- **Penyedia Global:** Namecheap, Cloudflare Registrar, atau Porkbun. Pilihan pas untuk domain `.com`, `.dev`, atau `.io` dengan harga standar internasional.
- **Langsung di Vercel:** Tersedia di dashboard proyek, praktis tanpa setting DNS manual, namun pilihan ekstensinya terbatas (tidak mendukung domain `.id`).

## Langkah Menyambungkan Domain ke Vercel

Setelah domain aktif di registrar pilihanmu, ikuti tahapan berikut:

1. Buka dashboard proyek di **Vercel**, lalu masuk ke menu **Settings** > **Domains**.
2. Ketik nama domain yang sudah dibeli (contoh: `namakamu.com`), lalu klik **Add**.
3. Vercel akan menampilkan konfigurasi DNS yang diperlukan, biasanya berupa **A Record** dan **CNAME Record**.
4. Buka halaman **DNS Management** pada dashboard tempat kamu membeli domain.
5. Tambahkan **A Record** yang mengarah ke IP `76.76.21.21` dan **CNAME** yang mengarah ke `cname.vercel-dns.com` sesuai arahan di Vercel.
6. Tunggu proses propagasi DNS selesai (sekitar 5 menit hingga maksimal 24 jam). Jika status di Vercel sudah berubah menjadi centang hijau (*Valid Configuration*), domain resmi aktif beserta sertifikat SSL (HTTPS) gratis.

## Alur Update Kode Setelah Domain Terpasang

Pertanyaan yang sering muncul setelah memasang domain adalah: *"Apakah setiap update kode harus mengatur ulang domainnya?"*

Jawabannya **tidak perlu**. Domain kustom hanya berfungsi sebagai penunjuk alamat yang mengarah ke proyek Vercel milikmu. Alur kerjanya berjalan otomatis seperti berikut:

- **Edit Kode:** Lakukan perubahan proyek di **VS Code** seperti biasa.
- **Push ke GitHub:** Simpan perubahan dengan `git commit` dan `git push` ke repository GitHub.
- **Auto-Deploy Vercel:** Vercel otomatis mendeteksi *push* terbaru, melakukan proses *build*, dan menayangkannya ke lingkungan **Production**.
- **Tayang di Domain:** Begitu status deploy di Vercel menjadi *Ready*, domain kustom kamu langsung menampilkan versi website terbaru.

> **Tips:** Jika tampilan di browser belum berubah setelah proses deploy selesai, lakukan *hard refresh* dengan menekan `Ctrl + Shift + R` (Windows) atau `Cmd + Shift + R` (Mac) untuk membersihkan cache lama.
