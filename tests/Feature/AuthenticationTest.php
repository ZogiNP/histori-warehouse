<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    private bool $transaksiDimulai = false;

    protected function setUp(): void
    {
        parent::setUp();

        // Periksa tujuan sebelum membuat data pengujian.
        $this->assertTrue(app()->environment('testing'));
        $this->assertSame(
            'histori_warehouse_testing',
            DB::selectOne('SELECT DATABASE() AS nama')->nama
        );
        $this->assertSame('array', config('cache.default'));

        DB::beginTransaction();
        $this->transaksiDimulai = true;
    }

    protected function tearDown(): void
    {
        try {
            if ($this->transaksiDimulai) {
                DB::rollBack();
            }
        } finally {
            parent::tearDown();
        }
    }

    private function buatAkun(): User
    {
        return User::factory()->create([
            'email' => Str::uuid().'@example.test',
            'password' => 'PasswordUji12345',
        ]);
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->get('/')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_halaman_login_dapat_dibuka(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Histori Warehouse')
            ->assertSee('name="password"', false);
    }

    public function test_login_dengan_password_benar(): void
    {
        $user = $this->buatAkun();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'PasswordUji12345',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);

        $this->get('/')
            ->assertOk()
            ->assertSee('Selamat Datang di Histori Warehouse');
    }

    public function test_password_salah_ditolak_tanpa_disimpan_di_sesi(): void
    {
        $user = $this->buatAkun();

        $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'PasswordYangSalah',
        ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email')
            ->assertSessionMissing('_old_input.password');

        $this->assertGuest();
    }

    public function test_email_dan_password_wajib_diisi(): void
    {
        $this->from('/login')->post('/login', [])
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertGuest();
    }

    public function test_pembatasan_login_dan_pemulihan_setelah_menunggu(): void
    {
        $user = $this->buatAkun();

        for ($i = 0; $i < 5; $i++) {
            $this->from('/login')->post('/login', [
                'email' => $user->email,
                'password' => 'PasswordSalah',
            ])->assertSessionHasErrors([
                'email' => 'Email atau password tidak sesuai.',
            ]);
        }

        // Password benar tetap ditolak selama masa pembatasan.
        $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'PasswordUji12345',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();

        $pesan = session('errors')->first('email');
        $this->assertStringContainsString(
            'Terlalu banyak percobaan',
            $pesan
        );

        // Memajukan waktu pengujian, bukan menunggu sungguhan.
        $this->travel(61)->seconds();

        try {
            $this->post('/login', [
                'email' => $user->email,
                'password' => 'PasswordUji12345',
            ])->assertRedirect(route('home'));

            $this->assertAuthenticatedAs($user);
        } finally {
            $this->travelBack();
        }
    }

    public function test_pengguna_login_dialihkan_dari_halaman_login(): void
    {
        $user = $this->buatAkun();

        $this->actingAs($user)
            ->get('/login')
            ->assertRedirect('/');
    }

    public function test_logout_mengakhiri_sesi_login(): void
    {
        $user = $this->buatAkun();

        $this->actingAs($user)
            ->withSession(['penanda_uji' => 'sementara'])
            ->post('/logout')
            ->assertRedirect(route('login'))
            ->assertSessionMissing('penanda_uji');

        $this->assertGuest();

        $this->get('/')
            ->assertRedirect(route('login'));
    }

    public function test_logout_tidak_menerima_metode_get(): void
    {
        $this->get('/logout')->assertStatus(405);
    }
}