<?php

namespace Tests\Feature;

use App\Models\Kampung;
use App\Models\Level;
use App\Models\Murid;
use App\Models\PendaftaranSpmb;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use App\Models\User;
use App\Models\WaliMurid;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SpmbTest extends TestCase
{
    use RefreshDatabase;

    protected $tahun;
    protected $tingkat;
    protected $level;
    protected $kampung;
    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tahun = TahunPelajaran::create([
            'nama_hijriyah' => '1447-1448',
            'nama_masehi'   => '2026-2027',
            'is_active'     => true
        ]);

        $this->tingkat = Tingkat::create([
            'kode_tingkat'     => 'TPQ',
            'urutan_tingkat'   => 1,
            'nama_tingkat'     => 'TAMAN PENDIDIKAN ALQURAN',
            'kode_mdt_tingkat' => 'RA',
            'nama_mdt_tingkat' => 'RAUDLATUL ATHFAL',
            'kode_warna'       => '#10B981',
            'is_active'        => 1
        ]);

        $this->level = Level::create([
            'tingkat_id'   => $this->tingkat->id,
            'nama_level'   => '1 TPQ',
            'urutan_level' => 1
        ]);

        $this->kampung = Kampung::create([
            'kode'         => 'K01',
            'nama_kampung' => 'Morkoneng'
        ]);

        Role::firstOrCreate(['name' => 'administrator', 'guard_name' => 'web']);

        $this->adminUser = User::create([
            'name'      => 'Admin SPMB',
            'username'  => 'admin_spmb',
            'email'     => 'admin@spmb.test',
            'password'  => bcrypt('password'),
            'is_active' => true
        ]);
        $this->adminUser->assignRole('administrator');
    }

    public function test_halaman_spmb_dapat_diakses_publik()
    {
        $response = $this->get(route('spmb.form'));
        $response->assertStatus(200);
        $response->assertSee('Penerimaan Murid Baru');
    }

    public function test_pencarian_kk_berfungsi()
    {
        $wali = WaliMurid::create([
            'no_kk'                => '3526110000000001',
            'kepala_keluarga'      => 'Ayah',
            'nama_kepala_keluarga' => 'AHMAD TEST',
            'no_hp'                => '08123456789',
            'kampung_id'           => $this->kampung->id,
            'is_active'            => true
        ]);

        $resAda = $this->getJson(route('spmb.search-kk', ['no_kk' => '3526110000000001']));
        $resAda->assertStatus(200);
        $resAda->assertJsonFragment(['status' => 'success', 'nama_kepala_keluarga' => 'AHMAD TEST']);

        $resTidakAda = $this->getJson(route('spmb.search-kk', ['no_kk' => '9999999999999999']));
        $resTidakAda->assertStatus(200);
        $resTidakAda->assertJsonFragment(['status' => 'not_found']);
    }

    public function test_wali_murid_bisa_mendaftar_dan_mendapatkan_nomor_registrasi()
    {
        $wali = WaliMurid::create([
            'no_kk'                => '3526110000000099',
            'kepala_keluarga'      => 'Ayah',
            'nama_kepala_keluarga' => 'BAPAK TEST MURID',
            'no_hp'                => '081299998888',
            'kampung_id'           => $this->kampung->id,
            'is_active'            => true
        ]);

        $postData = [
            'tahun_pelajaran_id'   => $this->tahun->id,
            'level_id'             => $this->level->id,
            'wali_murid_id'        => $wali->id,
            'no_kk'                => '3526110000000099',
            'kepala_keluarga'      => 'Ayah',
            'nama_kepala_keluarga' => 'BAPAK TEST MURID',
            'no_hp'                => '081299998888',
            'kampung_id'           => $this->kampung->id,
            'nama_lengkap'         => 'MURID BARU TEST',
            'nama_panggilan'       => 'Murid',
            'jenis_kelamin'        => 'L',
            'nik'                  => '3526110000000088',
            'tempat_lahir'         => 'BANGKALAN',
            'tanggal_lahir'        => '2018-05-10',
            'anak_ke'              => 1,
            'hub_kel'              => 'Anak Kandung',
            'nama_ayah'            => 'BAPAK TEST MURID',
            'status_ayah'          => 'Hidup',
            'nama_ibu'             => 'IBU TEST MURID',
            'status_ibu'           => 'Hidup',
        ];

        $response = $this->post(route('spmb.store'), $postData);
        $this->assertDatabaseHas('pendaftaran_spmbs', [
            'nama_lengkap' => 'MURID BARU TEST',
            'status_pendaftaran' => 'Menunggu Verifikasi'
        ]);

        $pendaftaran = PendaftaranSpmb::where('nama_lengkap', 'MURID BARU TEST')->first();
        $response->assertRedirect(route('spmb.bukti', $pendaftaran->nomor_pendaftaran));

        $resBukti = $this->get(route('spmb.bukti', $pendaftaran->nomor_pendaftaran));
        $resBukti->assertStatus(200);
        $resBukti->assertSee($pendaftaran->nomor_pendaftaran);
    }

    public function test_admin_dapat_memverifikasi_pendaftaran_dan_menerbitkan_nism()
    {
        $pendaftaran = PendaftaranSpmb::create([
            'nomor_pendaftaran'  => 'SPMB-2026-0001',
            'tahun_pelajaran_id' => $this->tahun->id,
            'level_id'           => $this->level->id,
            'nama_lengkap'       => 'CALON MURID VERIFIKASI',
            'jenis_kelamin'      => 'L',
            'hub_kel'            => 'Anak Kandung',
            'status_ayah'        => 'Hidup',
            'status_ibu'         => 'Hidup',
            'status_pendaftaran' => 'Menunggu Verifikasi'
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('spmb-admin.verifikasi', $pendaftaran->id), [
            'nism' => '999901',
            'status_pembayaran' => 'Lunas',
            'catatan_admin' => 'Verifikasi berkas lengkap.'
        ]);

        $pendaftaran->refresh();
        $this->assertEquals('Diterima', $pendaftaran->status_pendaftaran);
        $this->assertEquals('999901', $pendaftaran->nism_diberikan);
        $this->assertNotNull($pendaftaran->murid_id);

        $this->assertDatabaseHas('murids', [
            'nism' => '999901',
            'nama_lengkap' => 'CALON MURID VERIFIKASI',
            'status' => 'Aktif'
        ]);
    }
}
