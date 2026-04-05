# Panduan Deployment: Laravel ke Google Cloud Run (Firebase)

Ikuti langkah-langkah di bawah ini untuk mengunggah aplikasi Anda ke internet.

## Prasyarat
1.  **Google Cloud Account**: Pastikan Anda sudah memiliki akun di [console.cloud.google.com](https://console.cloud.google.com/).
2.  **Google Cloud CLI**: Instal alat perintah `gcloud` di komputer Anda ([Panduan Instalasi](https://cloud.google.com/sdk/docs/install)).
3.  **Docker**: Pastikan Docker Desktop berjalan di komputer Anda.

---

## Langkah 1: Persiapan di Google Cloud
Buka terminal Anda dan jalankan perintah berikut:

```bash
# Login ke akun Google
gcloud auth login

# Buat project baru (ganti 'absensi-rsud' dengan nama unik Anda)
gcloud projects create absensi-rsud --set-as-default

# Aktifkan layanan yang diperlukan
gcloud services enable cloudbuild.googleapis.com run.googleapis.com containerregistry.googleapis.com
```

---

## Langkah 2: Build dan Upload Aplikasi
Gunakan Google Cloud Build untuk membungkus aplikasi Anda secara otomatis di server Google:

```bash
# Jalankan perintah ini di folder project absensi/backend
gcloud builds submit --tag gcr.io/absensi-rsud/app
```

---

## Langkah 3: Deploy (Menghidupkan Aplikasi)
Setelah proses upload selesai, jalankan aplikasi Anda:

```bash
gcloud run deploy absensi-app \
  --image gcr.io/absensi-rsud/app \
  --platform managed \
  --region asia-southeast2 \
  --allow-unauthenticated
```
> [!TIP]
> Region `asia-southeast2` berada di Jakarta, sehingga akses dari Indonesia akan sangat cepat.

---

## Langkah 4: Pengaturan Variabel Lingkungan (.env)
Setelah deploy berhasil, Anda akan mendapatkan URL (misal: `https://absensi-app-xyz.a.run.app`). Anda perlu mengatur `APP_KEY` dan Database di Google Cloud Console:

1.  Buka [Cloud Run Console](https://console.cloud.google.com/run).
2.  Pilih layanan `absensi-app`.
3.  Klik **Edit & Deploy New Revision**.
4.  Di tab **Variables**, tambahkan:
    - `APP_KEY`: (Ambil dari file .env di laptop Anda)
    - `APP_DEBUG`: `false`
    - `DB_CONNECTION`: `sqlite` (Untuk demo awal)

---

## Langkah 5: Migrasi Database & Seeder
Agar database di server terisi, jalankan perintah ini via terminal (menggunakan fitur Cloud Run Execute):

```bash
# Menjalankan migrasi dan seeder demo di server
gcloud run services proxy absensi-app --region asia-southeast2
# Lalu di terminal terpisah jalankan artisan via docker exec atau tool cloud
```
*Atau cara termudah untuk demo:* Pastikan database SQLite (`database/database.sqlite`) **ikut ter-upload** jika hanya untuk keperluan presentasi jangka pendek.

---

> [!WARNING]
> **Penting**: Google Cloud Run memiliki paket gratis yang besar, namun pastikan untuk mematikan layanan jika sudah tidak digunakan untuk menghindari tagihan tak terduga jika trafik melonjak.
