# Setup Secrets Staging

Kredensial staging disimpan **hanya di GitLab CI/CD variables**. Tidak ada data
sensitif yang di-commit, dan tidak ada yang perlu disiapkan manual di server —
job deploy yang menulis `.env` ke server pada setiap deploy.

Anda perlu akses **Maintainer** di project GitLab. Akses ke server tidak diperlukan.

---

## 1. Isi file environment

Kerjakan pada salinan lokal — jangan pernah di-commit kembali.

```bash
cp deployment/env/staging/.env.example /tmp/staging.env
```

Ganti semua `__CHANGE_ME__` di `/tmp/staging.env`:

| Key | Diisi dengan |
|---|---|
| `APP_URL` | URL publik staging, misal `https://staging.example.com` |
| `DB_PASSWORD` | Password Postgres (Anda yang menentukan; container DB dibuat memakai nilai ini) |
| `MAIL_FROM_ADDRESS` | Alamat pengirim, misal `noreply@example.com` |
| `MINIO_ROOT_USER` / `MINIO_ROOT_PASSWORD` | Login admin MinIO (minimal 8 karakter) |
| `MINIO_ACCESS_KEY` / `MINIO_SECRET_KEY` | Kredensial MinIO yang dipakai disk S3 aplikasi |
| `MINIO_URL` | URL publik MinIO, misal `https://files.staging.example.com` |
| `REVERB_APP_ID` / `REVERB_APP_KEY` / `REVERB_APP_SECRET` | String acak bebas — syaratnya hanya harus sama antara container app dan reverb |
| `REVERB_HOST` | Host websocket, umumnya domain yang sama dengan `APP_URL` |

Lalu isi `APP_KEY`:

```bash
php artisan key:generate --show
```

Kalau PHP di host kekurangan ekstensi, jalankan di dalam container:

```bash
docker compose exec app php artisan key:generate --show
```

Salin seluruh hasil `base64:...` ke `APP_KEY=`.

> **Kalau staging sudah berjalan**, pakai `APP_KEY` yang sedang aktif di sana,
> jangan generate yang baru. Key baru membuat semua user ter-logout dan data
> terenkripsi yang sudah ada tidak bisa dibaca lagi.

---

## 2. Simpan sebagai GitLab variable

**Settings → CI/CD → Variables → Add variable**

| Field | Nilai |
|---|---|
| Key | `STAGING_ENV_FILE` |
| Value | seluruh isi `/tmp/staging.env` |
| Type | **File** |
| Environments | `staging` |
| Protect variable | ✅ **ya** |
| Mask variable | tidak tersedia — variable tipe File tidak bisa di-mask |
| Expand variable reference | ❌ **tidak** |

Dua setting yang penting:

- **Expand variable reference harus mati.** File ini berisi referensi bergaya
  `${APP_NAME}` dan `${REVERB_APP_KEY}` yang memang ditujukan untuk Laravel.
  Kalau menyala, runner akan meng-expand-nya lebih dulu menjadi string kosong.
- **Protect variable harus menyala.** Variable yang di-protect hanya bisa dibaca
  dari protected branch, sehingga branch fitur tidak bisa mengakses secrets staging.

Agar itu berfungsi, protect juga branch-nya: **Settings → Repository → Protected
branches** → `main`.

Setelah tersimpan, hapus salinan lokal Anda:

```bash
rm /tmp/staging.env
```

---

## 3. Pastikan variable lain sudah ada

Pipeline juga membutuhkan variable berikut (di halaman Variables yang sama):

| Key | Type | Protect | Mask | Keterangan |
|---|---|---|---|---|
| `SSH_PRIVATE_KEY_STAGING` | Var | ✅ | — | Private key yang terdaftar di server staging. **Var, bukan File** — job mengalirkan nilainya ke `ssh-add`. Tempel seluruh key termasuk baris BEGIN/END |
| `SSH_USER_STAGING` | Var | ✅ | — | User SSH, misal `administrator` |
| `SSH_STAGING` | Var | ✅ | — | Host atau IP server |
| `SSH_KNOWN_HOSTS_STAGING` | Var | ✅ | — | Hasil `ssh-keyscan -H <host>`; tanpa ini deploy mempercayai host begitu saja |
| `token_pull` | Var | ✅ | ✅ | Token GitLab dengan akses `read_repository` — dipakai server untuk clone |
| `APP_URL` | Var | ✅ | — | URL yang sama dengan di file environment; dipakai sebagai link environment |
| `FLUX_USERNAME` | Var | ✅ | — | Email akun Flux Pro |
| `FLUX_LICENSE_KEY` | Var | ✅ | ✅ | Lisensi Flux Pro — tanpa ini `composer install` gagal |

---

## 4. Deploy

Push ke `main`, atau jalankan ulang pipeline. Urutan job:

1. `build` → `test` — harus lulus sebelum apa pun di-deploy
2. `deploy:staging:code` — pull, tulis `.env`, migrate, build ulang cache
3. `deploy:staging:containers` — **manual**, hanya untuk rebuild container penuh
   (job ini menghapus lalu clone ulang direktori aplikasi lebih dulu)

Kalau `STAGING_ENV_FILE` belum diisi, job deploy langsung berhenti dan menampilkan
instruksi ini, bukan melanjutkan deploy dengan environment yang usang.

---

## Mengubah secret di kemudian hari

Edit variable `STAGING_ENV_FILE`, lalu jalankan ulang `deploy:staging:code`. Tidak
ada salinan di server yang bisa diedit, jadi deploy ulang adalah satu-satunya cara
menerapkannya.

## Aturan

- Jangan pernah commit file environment yang sudah terisi. `deployment/env/*/.env`
  sudah masuk gitignore, dan `.env.example` harus tetap memakai placeholder
  `__CHANGE_ME__`.
- Jangan pernah `cat`, `echo`, atau `set -x` terhadap file environment di dalam job
  CI. Variable tipe File tidak bisa di-mask, dan log job bisa dibaca semua
  Developer di project ini.
- Rotasi `DB_PASSWORD`, key MinIO, dan `REVERB_APP_SECRET` kalau nilainya pernah
  ter-commit — file `.env` lama masih ada di git history repository ini.
