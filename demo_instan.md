# Panduan Demo Instan (Tanpa Kartu Kredit)

Karena Firebase/Google Cloud memerlukan kartu kredit, Anda bisa menggunakan **Ngrok** atau **Localtunnel** untuk membuat aplikasi di laptop Anda bisa diakses orang lain lewat internet secara GRATIS dan INSTAN.

---

## Opsi A: Ngrok (Paling Stabil)
Ngrok adalah standar industri untuk membagikan aplikasi lokal ke internet.

1.  **Daftar Gratis**: Buka [ngrok.com](https://ngrok.com/) dan buat akun (GRATIS).
2.  **Dapatkan Authtoken**: Login ke dashboard Ngrok dan salin `Your Authtoken`.
3.  **Instal Ngrok**: Jalankan perintah ini di Mac Anda:
    ```bash
    brew install ngrok/ngrok/ngrok
    ```
4.  **Atur Authtoken**:
    ```bash
    ngrok config add-authtoken <TOKEN_ANDA>
    ```
5.  **Jalankan Tunnel**:
    (Pastikan `php artisan serve` Anda sedang berjalan di port 8000)
    ```bash
    ngrok http 8000
    ```
6.  **Selesai**: Ngrok akan memberikan URL (misal: `https://abc-123.ngrok-free.app`). Bagikan URL tersebut kepada orang lain!

---

## Opsi B: Localtunnel (Paling Cepat / Tanpa Daftar)
Jika Anda tidak ingin membuat akun, gunakan Localtunnel.

1.  **Instal Localtunnel** (Memerlukan NodeJS):
    ```bash
    npm install -g localtunnel
    ```
2.  **Jalankan Tunnel**:
    (Pastikan `php artisan serve` Anda sedang berjalan di port 8000)
    ```bash
    lt --port 8000
    ```
3.  **Selesai**: Anda akan langsung mendapatkan URL (misal: `https://cool-panda-99.loca.lt`).

---

## PENTING: Update file `.env`
Agar tampilan (CSS/JS) tidak berantakan saat dibuka orang lain, Anda **HARUS** memperbarui satu baris di file `.env` Anda sesuai URL yang diberikan:

```env
# Ganti http://localhost dengan URL dari Ngrok/Localtunnel
# Contoh:
APP_URL=https://abc-123.ngrok-free.app
```
*(Jangan lupa tekan `Ctrl+C` pada `php artisan serve` lalu jalankan kembali agar perubahannya aktif).*

---

> [!TIP]
> **Kelebihan**: Aplikasi di laptop Anda kini bisa diakses dari HP atau laptop mana pun di dunia.
> **Peringatan**: URL ini hanya aktif selama Terminal Anda terbuka. Jika Terminal ditutup, URL tidak bisa lagi diakses.
