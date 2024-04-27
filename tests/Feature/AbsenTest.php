<?php

namespace Tests\Feature;

use App\Models\Absen;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use Carbon\Carbon;
use Database\Seeders\GuruSeeder;
use Database\Seeders\KelasSeeder;
use Database\Seeders\MapelSeeder;
use Database\Seeders\SiswaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AbsenTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testCreateAbsenSuccess(): void
    {
        $this->seed([GuruSeeder::class, KelasSeeder::class, SiswaSeeder::class, MapelSeeder::class]);
        $kelas = Kelas::first();
        $mapel = Mapel::first();
        $guru = Guru::first();
        $this->post('/api/absens', [
            'id_guru'=> $guru->id,
            'id_siswa'=> null,
            "id_kelas"=> $kelas->id,
            "id_mapel"=> $mapel->id,
            'status'=> null, 
            'jam'=> null, 
            "tanggal"=> '2011-10-10T14:48:00', 
            "keterangan"=> null,
            'materi'=> "Pengenalan Mc. word"
        ],
        ["Authorization"=>'test']
        )->assertStatus(201)
        ->assertJson([
            "data"=>[
                "Absen created successfully."
            ]
            ]);

        $absen = Absen::where('id_kelas', $kelas->id)
        ->where('id_guru', $guru->id)
        ->where('id_mapel', $mapel->id)
        ->where('tanggal', '2011-10-10')->get();
        self::assertNotEmpty($absen);
    }

    public function testCreateAbsenFailed(): void
    {
        $this->seed([GuruSeeder::class, KelasSeeder::class, SiswaSeeder::class, MapelSeeder::class]);
        $kelas = Kelas::first();
        $mapel = Mapel::first();
        $guru = Guru::first();
        $this->post('/api/absens', [
            'id_guru'=> $guru->id,
            'id_siswa'=> null,
            "id_kelas"=> $kelas->id+1,
            "id_mapel"=> $mapel->id,
            'status'=> null, 
            'jam'=> null, 
            "tanggal"=> '2011-10-10T14:48:00', 
            "keterangan"=> null,
            'materi'=> "Pengenalan Mc. word"
        ],
        ["Authorization"=>'test']
        )->assertStatus(404)
        ->assertJson([
            "errors"=>[
                "message" =>[
                    "Not found."
                ]
            ]
            ]);
    }

    public function testSetAbsenSuccess(): void {
        $this->testCreateAbsenSuccess();
        $absen = Absen::first();

        $this->put('/api/absens/'.$absen->id, [
            'id_guru'=> $absen->id_guru,
            'id_siswa'=> $absen->id_siswa,
            "id_kelas"=> $absen->id_kelas, 
            "id_mapel"=> $absen->id_mapel, 
            'status'=> "Hadir", 
            "jam" => $absen->jam,
            "tanggal"=> $absen->tanggal, 
            "keterangan"=> $absen->keterangan,
            'materi'=>$absen->materi
        ],
        ["Authorization"=>'test']
        )->assertStatus(201)
        ->assertJson([
            "data"=>[
                'id_guru'=> [
                    "id" => $absen->id_guru
                ],
                'id_siswa'=> [
                    "id" => $absen->id_siswa
                ],
                "id_kelas"=> [
                    "id" =>  $absen->id_kelas
                ], 
                "id_mapel"=> [
                    "id" => $absen->id_mapel, 
                ], 
                'status'=> "Hadir", 
                "jam"=> $absen->jam, 
                "tanggal"=> $absen->tanggal, 
                "keterangan"=> $absen->keterangan,
                'materi'=>$absen->materi
            ]
            ]);
            $newabsen = Absen::find($absen->id);
            self::assertEquals($newabsen['jam'], $absen->jam);
            self::assertEquals($newabsen['status'], 'Hadir');
        
    }
    public function testSetAbsenFailed(): void {
        $this->testCreateAbsenSuccess();
        $absen = Absen::first();

        $this->put('/api/absens/'.$absen->id+100, [
            'id_guru'=> $absen->id_guru,
            'id_siswa'=> $absen->id_siswa,
            "id_kelas"=> $absen->id_kelas, 
            "id_mapel"=> $absen->id_mapel, 
            'status'=> "Hadir", 
            "jam" => $absen->jam,
            "tanggal"=> $absen->tanggal, 
            "keterangan"=> $absen->keterangan,
            'materi'=>$absen->materi
        ],
        ["Authorization"=>'test']
        )->assertStatus(404)
        ->assertJson([
            "errors"=>[
                "message" =>[
                    "Not found."
                ]
            ]
            ]);
    }

    public function testGetAbsenBySuccess(): void {
        $this->testCreateAbsenSuccess();
        $absen = Absen::first();

        $this->get('/api/absens/'. $absen->id_mapel .'/'.$absen->id_guru .'/'.$absen->id_kelas.'/'.$absen->tanggal, 
        ["Authorization"=>'test']
        )->assertStatus(200)
        ->assertJson([
            "data"=>[
                [
         
                ]
            ]
            ]);
    }
    public function testGetAbsenByFailed(): void {
        $this->testCreateAbsenSuccess();
        $absen = Absen::first();

        $this->get('/api/absens/'. $absen->id_mapel-1 .'/'.$absen->id_guru-1 .'/'.$absen->id_kelas-1 .'/'.$absen->tanggal, 
        ["Authorization"=>'test']
        )->assertStatus(404)
        ->assertJson([
            "errors"=>[
                "message" =>[
                    "Not found."
                ]
            ]
            ]);
    }

    public function testDeleteAbsenSuccess(): void {
        $this->testCreateAbsenSuccess();
        $absen = Absen::first();

        $this->delete(uri:'/api/absens/'. $absen->id, 
        headers:["Authorization"=>'test']
        )->assertStatus(200)
        ->assertJson([
            "data"=>true
            ]);

        $absenIsDeleted = Absen::find($absen->id);
        error_log($absenIsDeleted);
        self::assertEmpty($absenIsDeleted);
    }
    public function testDeleteAbsenFailed(): void {
        $this->testCreateAbsenSuccess();
        $absen = Absen::first();

        $this->delete(uri:'/api/absens/'. $absen->id-1, 
        headers:["Authorization"=>'test']
        )->assertStatus(404)
        ->assertJson([
            "errors"=>[
                "message" =>[
                    "Not found."
                ]
            ]
            ]);
    }
}
