<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class BuatAkunWarehouse extends Command
{
    protected $signature = 'warehouse:buat-akun';

    protected $description = 'Membuat akun bersama pertama Histori Warehouse';

    public function handle(): int
    {
        $this->info('Database tujuan: '.User::getConnectionResolver()
            ->connection()->getDatabaseName());

        // Perintah ini khusus untuk pembuatan akun pertama.
        if (User::exists()) {
            $this->error(
                'Akun sudah tersedia. Tidak ada akun yang dibuat atau diubah.'
            );

            return self::FAILURE;
        }

        if (! $this->confirm('Buat akun bersama pertama pada database ini?')) {
            return self::SUCCESS;
        }

        $nama = trim((string) $this->ask(
            'Nama akun',
            'Akun Bersama Warehouse'
        ));

        $email = strtolower(trim((string) $this->ask('Email akun')));

        // Jangan izinkan fallback berupa input password yang terlihat.
        $password = $this->secret('Password (minimal 12 karakter)', false);
        $konfirmasi = $this->secret('Ulangi password', false);

        $validator = Validator::make([
            'name' => $nama,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'max:255',
                Password::min(12)->letters()->numbers(),
            ],
        ], [
            'name.required' => 'Nama akun wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 12 karakter.',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $pesan) {
                $this->error($pesan);
            }

            return self::FAILURE;
        }

        if ($password !== $konfirmasi) {
            $this->error('Konfirmasi password tidak sama.');

            return self::FAILURE;
        }

        User::create([
            'name' => $nama,
            'email' => $email,
            // Cast "hashed" pada model User akan melakukan hashing.
            'password' => $password,
        ]);

        $this->info('Akun bersama berhasil dibuat.');

        return self::SUCCESS;
    }
}