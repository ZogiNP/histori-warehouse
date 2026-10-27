# Histori Warehouse

Aplikasi dokumentasi aktivitas fisik barang warehouse berbasis Laravel.
Aplikasi ini mendukung dokumentasi operasional dan tidak menggantikan
transaksi atau persediaan di Accurate Online.

## Teknologi

- Laravel 12
- PHP minimal 8.2
- MySQL/MariaDB
- Laravel Blade
- Storage privat untuk bukti media

## Status pengembangan

- Halaman sambutan tersedia.
- Struktur database warehouse dan relasi Eloquent tersedia.
- Seeder tiga divisi awal tersedia.
- Sebelas pengujian warehouse lulus pada lingkungan pengembangan.
- Login, CRUD, upload foto, dan audit otomatis belum dibuat.

## Menjalankan aplikasi lokal

1. Pasang dependensi dengan `composer install`.
2. Buat `.env` dari `.env.example` dan sesuaikan koneksi database.
3. Jalankan `php artisan key:generate`.
4. Buat database kosong yang sesuai konfigurasi.
5. Tinjau migration sebelum menjalankan `php artisan migrate`.
6. Jalankan `php artisan db:seed --class=DivisiSeeder`.
7. Jalankan `php artisan serve`.

## Pengujian

Siapkan `.env.testing` dan database terpisah bernama
`histori_warehouse_testing`. Pastikan koneksi benar sebelum menjalankan
migration pengujian.

Jalankan:

```shell
php artisan migrate --env=testing
php artisan test --filter=Warehouse
```

Pengujian storage membuat file teks kecil pada folder privat
`storage/app/private/warehouse/pengujian-storage`.

## Keamanan

- Jangan commit `.env`, `.env.testing`, kredensial, atau data perusahaan.
- Foto warehouse disimpan pada storage privat.
- Jangan gunakan `migrate:fresh`, `migrate:refresh`, atau `db:wipe`
  pada database aplikasi.
- Akun bersama tidak membuktikan identitas individual petugas.