<?php

namespace Tests\Feature;

use App\Models\Kelas;
use Database\Seeders\AdminSeeder;
use Database\Seeders\KelasSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class KelasTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testStoreKelasSuccess(): void
    {
        $this->seed([AdminSeeder::class]);
        $this->post('/api/kelas', [
            "kelas"=> "X TKJ 1"
        ], [
            "Authorization"=>"test"
        ])
        ->assertStatus(201)
        ->assertJson([
            "data" => [
                "kelas"=> 'X TKJ 1'
            ]
            ]);
    }
    public function testStoreKelasAlreadyExist(): void
    {
        $this->testStoreKelasSuccess();
        $this->post('/api/kelas', [
            "kelas"=> "X TKJ 1"
        ], [
            "Authorization"=>"test"
        ])
        ->assertStatus(400)
        ->assertJson([
            'errors'=> [
                "message"=> [
                    "Kelas already exist"
                ]
            ]
            ]);
    }

    public function testGetKelasSuccess(): void
    {
        $this->testStoreKelasSuccess();
        $kelas = Kelas::first();
        $this->get('/api/kelas/'. $kelas->id, [
            "Authorization" => 'test'
        ])->assertStatus(200)
        ->assertJson([
            "data" => [
                "kelas"=> 'X TKJ 1'
            ]
            ]);
    }
    public function testGetKelasFailed(): void
    {
        $this->testStoreKelasSuccess();
        $kelas = Kelas::first();
        $this->get('/api/kelas/'. $kelas->id +1, [
            "Authorization" => 'test'
        ])->assertStatus(404)
        ->assertJson([
            'errors'=> [
                "message"=> [
                    "Not found."
                ]
            ]
            ]);
    }

    public function testGetAllKelasSuccess(): void
    {
        $this->testStoreKelasSuccess();
        $this->get('/api/kelas', [
            "Authorization" => 'test'
        ])->assertStatus(200)
        ->assertJson([
            'data'=> [
                [
                    "kelas"=> 'X TKJ 1'
                ]
            ]
            ]);
    }
    public function testGetAllKelasFailed(): void
    {
        $this->testStoreKelasSuccess();
        $this->get('/api/kelas', [
            "Authorization" => 'salah'
        ])->assertStatus(401)
        ->assertJson([
            "errors" => [
                "message" => [
                    "Unauthorized."
                ]
            ]
            ]);
    }

    public function testUpdateKelasSuccess(): void
    {
        $this->seed([AdminSeeder::class, KelasSeeder::class]);
        $kelas = Kelas::first();
        $this->patch('/api/kelas/'. $kelas->id, [
            "kelas" =>'baru'
        ],
        [
            "Authorization" => 'test'
        ])->assertStatus(201)
        ->assertJson([
            "data"=>[
                "kelas"=>'baru'
            ]
            ]);
        
        $newKelas = Kelas::first();
        self::assertNotEquals($kelas->kelas, $newKelas->kelas);

    } 
    public function testUpdateKelasFailed(): void
    {
        $this->seed([AdminSeeder::class, KelasSeeder::class]);
        $kelas = Kelas::first();
        $this->patch('/api/kelas/'. $kelas->id, [
            "kelas" =>''
        ],
        [
            "Authorization" => 'test'
        ])->assertStatus(400)
        ->assertJson([
            "errors"=> [
                "kelas"=> [
                    "The kelas field is required."  
                ]
            ]
            ]);     
    }

    public function testDeleteKelasSuccess(): void
    {
        $this->seed([AdminSeeder::class, KelasSeeder::class]);
        $kelas = Kelas::first();

        $this->delete(uri: '/api/kelas/'. $kelas->id, headers:[
            "Authorization" => 'test'
        ])->assertStatus(200)
        ->assertJson([
            "data" => true
        ]);

        $isKelas = Kelas::where('id', $kelas["id"])->first();
        self::assertEmpty($isKelas);
    }
    public function testDeleteKelasFailed(): void
    {
        $this->seed([AdminSeeder::class, KelasSeeder::class]);
        $kelas = Kelas::first();

        $this->delete(uri: '/api/kelas/'. $kelas->id + 1, headers:[
            "Authorization" => 'test'
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
