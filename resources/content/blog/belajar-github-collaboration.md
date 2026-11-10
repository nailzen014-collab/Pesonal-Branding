---
title: "Panduan Belajar GitHub Collaboration untuk Tim Kecil"
slug: belajar-github-collaboration
excerpt: "Catatan kerja kolaborasi satu repository untuk tim SMK: identitas Git, branch per fitur, pull request, review, dan menyelesaikan konflik."
category: Tips
published_at: 2026-11-10 12:00
sort_order: 8
meta_description: "Alur kolaborasi GitHub untuk tim kecil: setup identity Git, branch per fitur, pull request, review, rebase, dan resolusi konflik."
---
# Panduan Belajar GitHub Collaboration

Dokumen ini adalah catatan belajar kolaborasi untuk proyek **Sistem CV Builder —
SMK Informatika Pesat**. Tim beranggotakan **Abbad, Alief, Hanif, Salman, Syafiq**,
semuanya bekerja di **satu repository yang sama**.

> **Aturan emas:** satu pengguna GitHub = satu akun (`user.name`/`user.email` di
> Git harus milikmu sendiri). Kalau di laptop dipakai bergantian, git akan
> mencatat commit atas nama pemilik konfigurasi terakhir — dan itu merusak riwayat.

---

## 1. Setup Awal (sekali saja per laptop)

```bash
# Cek identitas Git yang sedang aktif
git config --global user.name
git config --global user.email

# Set kalau masih kosong / salah
git config --global user.name "Abbad"
git config --global user.email "email-github-abbad@example.com"

# Login GitHub lewat browser (disarankan dibanding password/token manual)
gh auth login
```

Verifikasi:

```bash
git config --global --list
git remote -v          # harus menunjuk ke repository proyek ini
```

Jika `git remote -v` kosong, tambahkan:

```bash
git remote add origin https://github.com/<owner>/<repo>.git
git fetch origin
```

---

## 2. Konsep Dasar yang Wajib Paham Dulu

| Istilah | Arti sederhana |
| :--- | :--- |
| **Repository** | Folder proyek yang seluruh riwayat perubahannya dicatat Git |
| **Commit** | Satu snapshot perubahan + pesan penjelas. Terjadi **di lokal** |
| **Push** | Mengirim commit lokal ke GitHub (remote) |
| **Pull** | Mengambil commit terbaru dari GitHub ke lokal |
| **Branch** | Jalur kerja terpisah. `master`/`main` = jalur utama yang stabil |
| **Merge** | Menggabungkan branch ke branch lain |
| **Pull Request (PR)** | Permintaan merge + tempat review & diskusi sebelum masuk `master` |
| **Conflict** | Dua orang mengubah baris yang sama sehingga Git tidak bisa memilih otomatis |
| **Stash** | Menyimpan perubahan sementara tanpa commit |

**Alur besar yang dipakai tim ini:**

```
fitur di branch  →  commit kecil-kecil  →  push  →  Pull Request  →  review  →  merge ke master
```

---

## 3. Konvensi Branch Proyek Ini

Dari riwayat repo, pola yang sudah berjalan:

| Jenis branch | Format | Contoh di repo |
| :--- | :--- | :--- |
| Fitur | `feat/nama-fitur` | `feat/FormAlumni`, `feat/membuat-halaman-dashboard-rekap-data-alumni-untuk-admin` |
| Perbaikan | `fix/nama-bug` | `fix/ignore-qoder-folder` |
| Sprint / area kerja | `nama-area` | `authentication` (milik Abbad) |

**Aturan tim:**

1. **Jangan pernah commit langsung ke `master`.** Selalu buat branch baru.
2. Nama branch pakai huruf kecil dan tanda hubung: `feat/crud-lowongan` ✅,
   `FormAlumni` ❌ (kurang prefix, huruf kapital).
3. Nama branch mengandung lingkup kerja supaya jelas siapa yang mengerjakan apa.
4. Satu branch = satu tujuan. Kalau di tengah jalan ketemu bug lain, buat branch
   baru, jangan campur.

```bash
# Mulai fitur baru dari master yang sudah terbaru
git switch master
git pull origin master
git switch -c feat/crud-lowongan
```

---

## 4. Siklus Kerja Harian

### 4.1 Sebelum mulai kerja — ambil update terbaru

```bash
git status                 # pastikan tidak ada perubahan tak tersimpan
git switch <branch-kamu>
git pull origin master     # bawa perubahan rekan lain ke branch kamu
```

### 4.2 Saat kerja — commit kecil dan sering

```bash
git status                 # lihat apa yang berubah
git diff                   # lihat isi perubahannya
git add app/Http/Controllers/LowonganController.php
git add resources/views/lowongan/
git commit -m "feat: tambah daftar lowongan untuk siswa"
```

> **Jangan `git add .` secara membabi buta.** Cek `git status` setelahnya.
> File `.env`, `.env.backup`, `opencode.json`, `CLAUDE.md`, dan folder tool AI
> sudah masuk `.gitignore` — jangan dipaksa tambah dengan `git add -f`.

**Konvensi pesan commit** (dipakai di riwayat repo):

| Prefix | Untuk apa |
| :--- | :--- |
| `feat:` | Fitur baru |
| `fix:` | Perbaikan bug |
| `refactor:` | Merapikan kode tanpa mengubah perilaku |
| `docs:` | Dokumentasi saja |
| `test:` | Menambah/memperbaiki test |
| `chore:` | Konfigurasi, dependensi, `.gitignore` |

Contoh bagus: `feat: update password requirements to a minimum of 8 characters`

- Pakai bahasa yang jelas (tidak harus dua bahasa, tapi konsisten satu tim).
- Tulis **kenapa** lebih penting daripada **apa** (apa-nya sudah terlihat di diff).

### 4.3 Selesai kerja — push

```bash
git push -u origin feat/crud-lowongan
```

`-u` cukup dipakai sekali; setelah itu cukup `git push`.

---

## 5. Sinkronisasi dengan `master`

Rekan lain sudah merge sesuatu sementara kamu masih di branch? Pilih salah satu:

### Opsi A — Merge (aman untuk pemula)

```bash
git switch feat/crud-lowongan
git fetch origin
git merge origin/master
```

Jika konflik, lihat **bagian 7**.

### Opsi B — Rebase (riwayat lebih lurus)

```bash
git switch feat/crud-lowongan
git fetch origin
git rebase origin/master
```

⚠️ **Jangan pernah rebase branch yang sudah di-push dan sedang dipakai orang lain.**
Rebase menulis ulang history; rekan yang sudah `pull` akan kacau.

---

## 6. Pull Request (PR)

PR adalah cara tim ini saling review sebelum kode masuk `master`.

```bash
# setelah push branch
gh pr create --base master --head feat/crud-lowongan \
  --title "feat: CRUD lowongan kerja admin" \
  --body "Menambahkan CRUD lowongan untuk admin dan daftar lowongan untuk siswa."
```

Aturan PR tim:

1. PR **kecil**: satu fitur / satu perbaikan. PR raksasa sulit di-review.
2. Selalu isi deskripsi: apa yang berubah dan bagaimana cara mengetesnya.
3. Minimal **1 reviewer** sebelum merge.
4. Jangan merge kalau ada percakapan reviewer yang belum selesai.
5. Setelah merge, hapus branch-nya (`gh pr merge --delete-branch`).

Melihat PR dan komentar rekan:

```bash
gh pr list
gh pr view 12
gh pr view 12 --comments
gh pr checkout 12          # menguji PR rekan secara lokal
gh pr checks 12            # status test/CI
```

---

## 7. Menyelesaikan Merge Conflict

Muncul saat dua orang mengubah **baris yang sama** di file yang sama.

### Langkah

```bash
git status
# "both modified: resources/views/.../create.blade.php"

git diff --name-only --diff-filter=U   # daftar file konflik
```

Buka file yang konflik — akan ada penanda:

```php
<<<<<<< HEAD
        <h1>Daftar Lowongan</h1>
=======
        <h1>Lowongan Industri Mitra</h1>
>>>>>>> origin/master
```

Perbaiki: **hapus penanda, pilih/satukan** hasil akhir yang diinginkan.

```php
        <h1>Lowongan Industri Mitra</h1>
```

Lalu:

```bash
git add resources/views/.../create.blade.php
git commit                 # menyelesaikan merge (pesan sudah disiapkan Git)
# atau: git rebase --continue  → kalau sedang rebase
```

Cek hasil sebelum commit:

```bash
git diff --check           # memastikan tidak ada penanda konflik tertinggal
php artisan test           # konflik sering menyembunyikan error logika
```

### Kalau panik dan ingin batal

```bash
git merge --abort          # membatalkan merge
git rebase --abort         # membatalkan rebase
```

### Konflik berbeda namespace (banyak file kecil di banyak fitur)

```bash
git checkout --ours path/file   # ambil versi branch kamu
git checkout --theirs path/file # ambil versi master
```

Orang yang **commit terakhir ke baris itu** adalah orang yang harus dihubungi
kalau keputusannya tidak jelas:

```bash
git log --oneline -5 -- path/file
git blame path/file | head -30
```

---

## 8. Skenario Tim yang Sering Terjadi

### 8.1 Push ditolak (`rejected — non-fast-forward`)

Artinya remote sudah punya commit yang belum ada di lokal. **Jangan langsung
`push --force`.**

```bash
git pull --rebase origin <branch>
# selesaikan konflik jika ada, lalu
git push
```

### 8.2 Salah commit ke `master` di lokal (belum push)

```bash
git switch -c feat/terlupakan        # pindahkan commit ke branch baru
git switch master
git reset --hard origin/master       # master kembali bersih
```

> `git reset --hard` membuang perubahan. Jalankan `git status` dan
> `git stash -u` dulu kalau ada kerjaan lain yang belum diamankan.

### 8.3 Lupa pindah branch lalu mengubah file

Kalau perubahan **belum di-commit**, biasanya ikut terbawa pindah branch:

```bash
git stash -u
git switch feat/benar
git stash pop
```

Di **shared worktree**, jangan pakai `git stash pop` polos — pakai tag:

```bash
git stash push -u -m "milik-abhad-wip"
# ...
git stash apply stash^{/milik-abhad-wip}   # apply, bukan pop
git stash drop stash^{/milik-abhad-wip}
```

### 8.4 Ingin membuang semua perubahan lokal

```bash
git status                 # BACA dulu apa yang akan hilang
git stash -u               # amankan (bisa diambil lagi)
# kalau yakin memang mau dibuang:
git restore .
git clean -fd
```

### 8.5 Commit terakhir salah (belum di-push)

```bash
git commit --amend        # ubah pesan / tambah file yang lupa masuk
```

Kalau commit sudah di-push dan **dipakai orang lain**, jangan amend —
buat commit perbaikan baru.

### 8.6 Commit password / token `.env`

1. **Ganti nilainya segera** di server/provider — menganggap sudah bocor.
2. Beri tahu tim.
3. Hapus dari riwayat (`git filter-repo` / GitHub secret scanning) dilakukan
   bersama-sama, bukan sendiri-sendiri.

`.env` memang sudah di `.gitignore` di repo ini, tapi tetap cek tiap PR:

```bash
git diff --cached --name-only     # sebelum commit
```

### 8.7 `autocrlf` / line ending

Repo ini punya `.gitattributes` dengan `* text=auto eol=lf`. Kalau file terlihat
"berubah semua" tanpa alasan, kemungkinan CRLF vs LF:

```bash
git config --global core.autocrlf input
git rm --cached -r . && git reset --hard   # ⚠️ lakukan hanya setelah koordinasi tim
```

---

## 9. Review Kode Rekan

Saat menjadi reviewer:

- Baca diff, bukan hanya judul PR: `gh pr diff 12`
- Cek hal sesuai `AGENTS.md`: role/otorisasi, validasi di FormRequest,
  warna tema (`ink`, `forest`, `moss`… bukan `gray-*`/`indigo-*`), teks UI
  Bahasa Indonesia, ada test untuk perubahan otorisasi.
- Komentar harus **spesifik & sopan**, sertakan alasan. Contoh:
  “Endpoint ini hanya `auth`, belum `role:admin`. Siswa bisa akses (AGENTS.md §Keamanan).”
- Andaikan kamu pembuat kode: PR kecil, deskripsi jelas, test lulus.

Alur merge yang disarankan:

```bash
gh pr checkout 12
git switch <branch-mu> && git pull --rebase origin master && php artisan test
gh pr merge 12 --squash --delete-branch
```

Squash dipakai agar satu fitur = satu commit bersih di `master`.

---

## 10. Cheat Sheet

| Keperluan | Perintah |
| :--- | :--- |
| Cek kondisi | `git status`, `git branch -vv` |
| Update lokal | `git fetch origin` + `git pull --rebase` |
| Branch baru | `git switch -c feat/xxx` |
| Commit | `git add -p` → `git commit -m "feat: ..."` |
| Push pertama | `git push -u origin feat/xxx` |
| Lihat riwayat | `git log --oneline --graph --decorate -20` |
| Batalkan file | `git restore <file>` |
| Batalkan staging | `git restore --staged <file>` |
| Simpan sementara | `git stash push -u -m "wip"` → `git stash apply` |
| Abaikan merge | `git merge --abort` |
| Siapa ubah baris ini | `git blame <file>` |
| PR | `gh pr create`, `gh pr view`, `gh pr checkout`, `gh pr merge` |

---

## 11. Yang **Jangan** Dilakukan

| ❌ | Kenapa |
| :--- | :--- |
| `git push --force` ke `master` / branch orang lain | Menghapus commit rekan |
| Commit langsung ke `master` | Merusak jalur stabil |
| `git add .` tanpa lihat `git status` | Ikut masuk `.env`/file sampah |
| Commit password, token, kunci API | Bocor permanen di riwayat |
| `git reset --hard` sebelum `git stash` | Kerjaan hilang permanen |
| Sinkron `master` pakai `git stash pop` di shared worktree | Bisa menimpa stash orang |
| Rebase branch yang sudah dipakai orang | Riwayat rekan kacau |
| Merge PR tanpa review | Fitur bertabrakan aturan `AGENTS.md` |
| Mengubah `composer.lock`/`package-lock.json` manual | Dependensi tidak konsisten |

---

## 12. Ringkasan Satu Halaman

```bash
# 1. Mulai
git switch master && git pull origin master
git switch -c feat/nama-fitur

# 2. Kerja & commit kecil
git add <file-specific>
git commit -m "feat: deskripsi singkat"

# 3. Ambil update rekan
git fetch origin && git merge origin/master   # selesaikan konflik jika ada

# 4. Test & push
php artisan test
git push -u origin feat/nama-fitur

# 5. PR → review → merge
gh pr create --base master
```

> Kalau ragu, **berhenti dan `git status`**. Tidak ada perintah Git yang lebih
> penting daripada tahu di mana posisi Anda sekarang.
