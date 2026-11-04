---
title: "Kebiasaan Git yang Membuat Proyek Lebih Rapi"
slug: kebiasaan-git-proyek-rapi
excerpt: "Commit kecil, pesan yang jelas, dan branch sederhana membuat proyek lebih mudah dilacak."
category: Tips
published_at: 2026-08-28 19:00
sort_order: 4
meta_description: "Kebiasaan Git sederhana untuk menjaga riwayat proyek tetap rapi: commit kecil, pesan jelas, dan review sebelum push."
is_published: true
---

Git bukan hanya alat untuk menyimpan cadangan. Kalau dipakai dengan kebiasaan yang tepat, riwayat proyek bisa menjadi catatan kerja yang berguna saat mencari penyebab bug atau ingin mengembalikan perubahan.

## Commit kecil dan fokus

Satu commit sebaiknya menyelesaikan satu hal. Commit yang mencampur perbaikan bug, penambahan fitur, dan perubahan format sulit ditinjau dan sulit dibatalkan sebagian. Kalau perubahan mulai melebar, lebih baik dipecah.

## Pesan commit yang menjelaskan alasan

Pesan seperti `update` atau `fix` tidak memberi informasi apa pun. Lebih baik menuliskan apa yang berubah dan mengapa:

```text
perf: memoize pengaturan per request agar tidak query cache berulang
feat: login Google untuk pengunjung yang mengirim pesan kontak
```

Awalan seperti `feat`, `fix`, dan `perf` membantu membaca riwayat dengan cepat.

## Periksa sebelum push

Sebelum push, biasakan melihat `git status` dan `git diff`. Langkah singkat ini sering menyelamatkan dari ikut ter-commit-nya file sementara, kredensial, atau perubahan yang belum selesai.

## Jangan takut merapikan

Riwayat yang sudah berantakan bukan alasan untuk berhenti belajar. Mulai dari commit berikutnya, jaga agar tetap kecil dan jelas. Kebiasaan baik yang konsisten lebih berguna daripada riwayat sempurna yang hanya terjadi sekali.
