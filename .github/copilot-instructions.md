# Panduan proyek Histori Warehouse

- Proyek menggunakan Laravel 12, PHP 8.2+, dan MySQL/MariaDB.
- Ikuti struktur Laravel: controller, model Eloquent, Blade, dan migration.
- Aplikasi mendokumentasikan aktivitas warehouse dan tidak mengubah Accurate Online.
- Gunakan bahasa Indonesia untuk antarmuka dan penjelasan.
- Jangan menghapus atau menimpa file pengguna tanpa persetujuan.
- Jangan menjalankan migrate:fresh, migrate:refresh, atau db:wipe.
- Minta persetujuan sebelum menjalankan migration.
- Jangan commit kredensial, file .env, .env.testing, atau data perusahaan.
- Media warehouse menggunakan disk warehouse_private.
- Jangan menyediakan media privat melalui tautan publik.
- Akun bersama bukan bukti autentikasi individual petugas.
- Pengujian database harus menggunakan histori_warehouse_testing.
- Jangan memakai RefreshDatabase pada database aplikasi.
- Pertahankan pengujian dan tambahkan pengujian perilaku untuk fitur baru.
- Jangan membuat fitur di luar tahap yang sedang disepakati.