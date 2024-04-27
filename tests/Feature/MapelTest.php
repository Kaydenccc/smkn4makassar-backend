<?php

namespace Tests\Feature;

use App\Models\Mapel;
use Database\Seeders\AdminSeeder;
use Database\Seeders\MapelSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MapelTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testStoreSuccess(): void
    {
        $this->seed([AdminSeeder::class]);
        $this->post('/api/mapels', [
            "mapel" => 'test'
        ], [
            "Authorization" => 'test'
        ])->assertStatus(201)
        ->assertJson([
            "data" => [
                "mapel" => "test"
            ]
            ]);
    }
    public function testStoreMapelAlreadyExist(): void
    {
        $this->testStoreSuccess();
        $this->post('/api/mapels', [
            "mapel" => 'test'
        ], [
            "Authorization" => 'test'
        ])->assertStatus(400)
        ->assertJson([
            'errors'=> [
                "message"=> [
                    "The name already exist"
                ]
            ]
        ]);
    }
    public function testStoreMapelUnauthorized(): void
    {
        $this->testStoreSuccess();
        $this->post('/api/mapels', [
            "mapel" => 'test'
        ], [
            "Authorization" => 'salah'
        ])->assertStatus(401)
        ->assertJson([
            'errors'=> [
                "message"=> [
                    "Unauthorized."
                ]
            ]
        ]);
    }
    public function testGetMapelSuccess(): void 
    {
        $this->testStoreSuccess();
        $mapel = Mapel::first();
        $this->get('/api/mapels/'. $mapel->id, [
            "Authorization"=>'test'
        ])->assertStatus(200)
        ->assertJson([
            "data" => [
                "mapel"=> $mapel->mapel
            ]
            ]);
    }
    public function testGetMapelFailed(): void 
    {
        $this->testStoreSuccess();
        $mapel = Mapel::first();
        $this->get('/api/mapels/'. $mapel->id +2, [
            "Authorization"=>'test'
        ])->assertStatus(404)
        ->assertJson([
            "errors" => [
                "message" => [
                    "Not found."
                ]
            ]
            ]); 
    }
    public function testGetAllSuccess(): void {
        $this->testStoreSuccess();
        $this->get('/api/mapels', [
            "Authorization" => 'test'
        ])->assertStatus(200)
        ->assertJson([
            "data" => [
                [
                 "mapel" => "test"
                ]
             ]
            ]);
    }
    public function testGetAllFailed(): void {
        $this->testStoreSuccess();
        $this->get('/api/mapels', [
            "Authorization" => 'salah'
        ])->assertStatus(401)
        ->assertJson([
            'errors'=> [
                "message"=> [
                    "Unauthorized."
                ]
            ]
            ]);
    }

    public function testUpdateMapelSuccess() {
        $this->seed([AdminSeeder::class, MapelSeeder::class]);
        $mapel = Mapel::query()->limit(1)->first();
        $this->patch("/api/mapels/". $mapel->id,
        [
            "mapel"=> "baru"
        ], 
        [
            "Authorization" => 'test'
        ])
        ->assertStatus(200)
        ->assertJson([
            "data" => [
                 "mapel" => "baru"
             ]
            ]);
            $newMapel = Mapel::where('mapel', 'baru')->first();
            self::assertNotEquals($mapel->mapel, $newMapel->mapel);
    }


    public function testUpdateMapelFailedNotFound() {
        $this->seed([AdminSeeder::class, MapelSeeder::class]);
        $mapel = Mapel::query()->limit(1)->first();
        $this->patch("/api/mapels/". $mapel->id +1,
        [
            "mapel"=> "baru"
        ], 
        [
            "Authorization" => 'test'
        ])
        ->assertStatus(404)
        ->assertJson([
            "errors"=> [
                "message"=> [
                    "Not found."
                ]
            ]
            ]);
    }

    public function testDeleteMapelSuccess(): void {
        $this->seed([AdminSeeder::class, MapelSeeder::class]);
        $mapel = Mapel::first();

        $this->delete(uri:'api/mapels/'. $mapel->id, headers:[
            "Authorization"=>"test"
        ])
        ->assertStatus(200)
        ->assertJson([
            "data"=>true
        ]);

        $newMapel = Mapel::where('id', $mapel->id)->first();
        self::assertEquals($newMapel, null);

    }
    public function testDeleteMapelFailed(): void {
        $this->seed([AdminSeeder::class, MapelSeeder::class]);
        $this->delete(uri:'api/mapels/'. 1, headers:[
            "Authorization"=>"test"
        ])
        ->assertStatus(404)
        ->assertJson([
            "errors"=> [
                "message"=> [
                    "Not found."
                ]
            ]
        ]);

       
    }
}
