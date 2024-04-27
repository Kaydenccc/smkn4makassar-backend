<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Siswa;
use Database\Seeders\AdminSeeder;
use Database\Seeders\KelasSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SiswaTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testStoreSiswaSuccess(): void
    {
        $this->seed([AdminSeeder::class, KelasSeeder::class]);
        $kelas = Kelas::first();
        $this->post('/api/siswa', [
            "nama" => 'test',
            "nis" => "test",
            "jenis_kelamin" => "test",
            "id_kelas" => $kelas->id,
            "kontak" => 'test',
            "kontak_orang_tua" => "test",
            "alamat" => "test",
        ],[
            "Authorization"=>'test'
        ])
        ->assertStatus(201)
        ->assertJson([
            "data"=>[
            "nama" => 'test',
            "nis" => "test",
            "jenis_kelamin" => "test",
            "id_kelas" => [
                "id"=> $kelas->id
            ],
            "kontak" => 'test',
            "kontak_orang_tua" => "test",
            "alamat" => "test",
            ]
            ]);
    }

    public function testStoreSiswaFailed(): void
    {
        $this->testStoreSiswaSuccess();
        $kelas = Kelas::first();
        $this->post('/api/siswa', [
            "nama" => 'test',
            "nis" => "test",
            "jenis_kelamin" => "test",
            "id_kelas" => $kelas->id,
            "kontak" => 'test',
            "kontak_orang_tua" => "test",
            "alamat" => "test",
        ],[
            "Authorization"=>'test'
        ])
        ->assertStatus(400)
        ->assertJson([
            "errors" => [
                "message"=> [
                    "NIS already exist."
                ]
            ]
            ]);
    }

    public function testGetSiswaSuccess(): void
    {
        $this->testStoreSiswaSuccess();
        $siswa = Siswa::first();
        $this->get('/api/siswa/'. $siswa->id, [
            'Authorization'=> 'test'
        ])->assertStatus(200)
        ->assertJson([
            "data" => [
                'nama' => 'test',
                'nis' => 'test',
                'jenis_kelamin' => 'test',
                "id_kelas" => [
                    "id"=> $siswa->id_kelas
                ],
                'kontak' => 'test',
                'kontak_orang_tua' => 'test',
                'alamat' => 'test',
            ]
        ]);
    }
    public function testGetSiswaFailed(): void
    {
        $this->testStoreSiswaSuccess();
        $siswa = Siswa::first();
        $this->get('/api/siswa/'. $siswa->id+1, [
            'Authorization'=> 'test'
        ])->assertStatus(404)
        ->assertJson([
           "errors" => [
            "message" => [
                "Not found."
            ]
           ]
        ]);
    }
    public function testGetSiswaByClassSuccess(): void
    {
        $this->testStoreSiswaSuccess();
        $siswa = Siswa::first();
        $this->get('/api/siswa/'. $siswa->id_kelas .'/all', [
            'Authorization'=> 'test'
        ])->assertStatus(200)
        ->assertJson([
            "data" => [
                [
                    'nama' => 'test',
                    'nis' => 'test',
                    'jenis_kelamin' => 'test',
                    'kontak' => 'test',
                    "id_kelas" => [
                        "id"=> $siswa->id_kelas
                    ],
                    'kontak_orang_tua' => 'test',
                    'alamat' => 'test',
                ]
             ]
        ]);
    }
    public function testGetSiswaByClassFailed(): void
    {
        $this->testStoreSiswaSuccess();
        $siswa = Siswa::first();
        $this->get('/api/siswa/'. $siswa->id_kelas+1 .'/all', [
            'Authorization'=> 'test'
        ])->assertStatus(404)
        ->assertJson([
            "errors" => [
                "message" => [
                    "Not found."
                ]
               ]
        ]);
    }
    public function testUpdateSiswaSuccess(): void
    {
        $this->testStoreSiswaSuccess();
        $siswa = Siswa::first();
        $this->put('/api/siswa/'. $siswa->id,[
            'nama' => 'baru',
            'nis' => 'baru',
            'jenis_kelamin' => 'test',
            'kontak' => 'test',
            "id_kelas" => $siswa->id_kelas,
            'kontak_orang_tua' => 'test',
            'alamat' => 'test',
        ], [
            'Authorization'=> 'test'
        ])->assertStatus(201)
        ->assertJson([
            "data"=>[
                'nama' => 'baru',
                'nis' => 'baru',
                'jenis_kelamin' => 'test',
                'kontak' => 'test',
                "id_kelas" => [
                    "id"=> $siswa->id_kelas
                ],
                'kontak_orang_tua' => 'test',
                'alamat' => 'test',
            ]
        ]);
        $newsiswa = Siswa::find($siswa->id);
        self::assertEquals($newsiswa->nama, 'baru');
        self::assertEquals($newsiswa->nis, 'baru');
    }
    public function testUpdateSiswaFailed(): void
    {
        $this->testStoreSiswaSuccess();
        $siswa = Siswa::first();
        $this->put('/api/siswa/'. $siswa->id+1,[
            'nama' => 'baru',
            'nis' => 'baru',
            'jenis_kelamin' => 'test',
            'kontak' => 'test',
            "id_kelas" => $siswa->id_kelas,
            'kontak_orang_tua' => 'test',
            'alamat' => 'test',
        ], [
            'Authorization'=> 'test'
        ])->assertStatus(404)
        ->assertJson([
            "errors" => [
                "message" => [
                    "Not found."
                ]
               ]
        ]);
    }

    public function testDeleteSiswaSuccess(): void
    {
        $this->testStoreSiswaSuccess();
        $siswa = Siswa::first();
        $this->delete(uri:'/api/siswa/'. $siswa->id, headers:[
            'Authorization'=> 'test'
        ])->assertStatus(200)
        ->assertJson([
            "data"=>true
        ]);
        $newsiswa = Siswa::find($siswa->id);
        self::assertEmpty($newsiswa);
    }

    public function testDeleteSiswaFailed(): void
    {
        $this->testStoreSiswaSuccess();
        $siswa = Siswa::first();
        $this->delete(uri:'/api/siswa/'. $siswa->id+1, headers:[
            'Authorization'=> 'test'
        ])->assertStatus(404)
        ->assertJson([
            "errors" => [
                "message" => [
                    "Not found."
                ]
               ]
        ]);
    }
}
