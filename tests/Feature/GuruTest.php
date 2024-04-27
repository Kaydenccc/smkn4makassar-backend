<?php

namespace Tests\Feature;

use App\Models\Guru;
use Database\Seeders\AdminSeeder;
use Database\Seeders\GuruSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertNotEquals;

class GuruTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testRegisterGuruSuccess(){
        $this->seed([AdminSeeder::class]);
        $this->post('/api/gurus',[
            "nama"=> 'test',
            "nip"=> 'nip191992',
            "email"=> 'test@test.com',
            "password"=> 'test',
            "no_hp"=>"02928829291"
        ] , [
            "Authorization"=>"test"
        ])
        ->assertStatus(201)
        ->assertJson(
            [
                'data'=>[
                    "nama"=> 'test',
                    "nip"=> 'nip191992',
                    "email"=> 'test@test.com',
                    // "password"=> 'test',
                    "no_hp"=>"02928829291"
                ]
            ]
        );
    }
    public function testRegisterGuruFeiled(){
        $this->seed([AdminSeeder::class]);
        $this->post('/api/gurus',[
            "nama"=> '',
            "nip"=> '',
            "email"=> '',
            "password"=> '',
            "no_hp"=>""
        ] , [
            "Authorization"=>"test"
        ])
        ->assertStatus(400)
        ->assertJson(
            [
                "errors"=> [
                    "nama"=> [
                        "The nama field is required."
                    ],
                    "nip"=> [
                        "The nip field is required."
                    ],
                    "email"=> [
                        "The email field is required."
                    ],
                    "password"=> [
                        "The password field is required."
                    ]
                ]
            ]
        );
    }
    public function testRegisterGuruNipAlreadyExist(){
        $this->testRegisterGuruSuccess();
        $this->post('/api/gurus',[
            "nama"=> 'test',
            "nip"=> 'nip191992',
            "email"=> 'test1@test.com',
            "password"=> 'test',
            "no_hp"=>"02928829291"
        ], [
            "Authorization"=>"test"
        ] )
        ->assertStatus(400)
        ->assertJson(
            [
                'errors'=> [
                    "message"=> [
                        "nip or email already exist"
                    ]
                ]
            ]
        );
    }
    public function testRegisterGuruEmailAlreadyExist(){
        $this->testRegisterGuruSuccess();
        $this->post('/api/gurus',[
            "nama"=> 'test',
            "nip"=> 'nip191993',
            "email"=> 'test@test.com',
            "password"=> 'test',
            "no_hp"=>"02928829291"
        ], [
            "Authorization"=>"test"
        ] )
        ->assertStatus(400)
        ->assertJson(
            [
                'errors'=> [
                    "message"=> [
                        "nip or email already exist"
                    ]
                ]
            ]
        );
    }


    public function testGuruLoginSuccess(){
        $this->seed([GuruSeeder::class]);
        $this->post('/api/gurus/login', [
            'nip'=> 'nip191993',
            'password'=>'test'
        ])->assertStatus(200)
        ->assertJson([
            "data" => [
                "nama" => 'test',
                "nip" => 'nip191993'
            ]
        ]);

        $guru = Guru::where('nip', 'nip191993')->first();
        self::assertNotNull($guru->token);

    }
    public function testGuruLoginFailedDataNotFound(){
        $this->post('/api/gurus/login', [
            'nip'=> 'nip191993',
            'password'=>'test'
        ])->assertStatus(401)
        ->assertJson([
            "errors" => [
                "message"=>[
                    "NIP or password is wrong."
                ]
            ]
        ]);
    }
    public function testGuruLoginFailedPasswordWrong(){
        $this->seed(GuruSeeder::class);
        $this->post('/api/gurus/login', [
            'nip'=> 'nip191993',
            'password'=>'test1'
        ])->assertStatus(401)
        ->assertJson([
            "errors" => [
                "message"=>[
                    "NIP or password is wrong."
                ]
            ]
        ]);
    }
    public function testGuruLoginFailedNipWrong(){
        $this->seed(GuruSeeder::class);
        $this->post('/api/gurus/login', [
            'nip'=> 'nip191991',
            'password'=>'test'
        ])->assertStatus(401)
        ->assertJson([
            "errors" => [
                "message"=>[
                    "NIP or password is wrong."
                ]
            ]
        ]);
    }
    public function testGetGuruSuccess()
    {
         $this->seed(GuruSeeder::class);
         $this->get('/api/gurus/current', [
            "Authorization"=> 'test'
         ])-> assertStatus(200)
         ->assertJson([
            "data"=> [
                'nip' => 'nip191993',     
                'nama' => 'test',
                'email' => 'test@test.com',
                'no_hp' => '02928829291', 
            ]
        ]);
    }
    public function testGetGuruUnauthorized()
    {
        $this->seed(GuruSeeder::class);
        $this->get('/api/gurus/current',[
            "Authorization"=> 'salah'
         ])
        ->assertStatus(401)
         ->assertJson([
            "errors"=>[
                "message"=>[
                    'Unauthorized.'
                ]
            ]
        ]);
    }
    public function testGetGuruInvalidToken()
    {
        $this->seed(GuruSeeder::class);
        $this->get('/api/gurus/current')
        ->assertStatus(401)
         ->assertJson([
            "errors"=>[
                "message"=>[
                    'Unauthorized.'
                ]
            ]
        ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed(GuruSeeder::class);
        $oldUser = Guru::where('nip', 'nip191993')->first();
        

        $this->patch('/api/gurus/current',[
            // 'nip' => 'nip100000',     
            // 'nama' => 'testudpate',
            // 'email' => 'testupdate@test.com',
            "nama"=> 'baru',
            // 'no_hp' => '02928829291'
        ],[
            "Authorization"=> 'test'
         ])
        ->assertStatus(201)
         ->assertJson([
            "data"=> [
                'nama' => 'baru',
                'nip' => 'nip191993',     
                'email' => 'test@test.com',
                'no_hp' => '02928829291',
            ]
        ]);

        $newGuru = Guru::where('nip', 'nip191993')->first();
        self::assertNotEquals($oldUser->nama, $newGuru->nama);
    }
    public function testUpdateFailed()
    {
        $this->seed(GuruSeeder::class);
        $this->patch('/api/gurus/current',[
            "nama"=> "barujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdas"
        ],[
            "Authorization"=> 'test'
         ])
        ->assertStatus(400)
        ->assertJson(
            [
                "errors"=> [
                    "nama"=> [
                        "The nama field must not be greater than 100 characters."
                    ],
                ]
            ]);

    }
    
    public function testLogoutSuccess()
    {
        $this->seed(GuruSeeder::class);
        $this->delete(uri:'/api/gurus/logout', headers:[
            "Authorization"=> 'test'
         ])
        ->assertStatus(200)
        ->assertJson(
            [
                "data"=> true
            ]);
            $user = Guru::where('nip', 'nip191993')->first();
            self::assertNull($user->token);
    }
    public function testLogoutFailed()
    {
        $this->seed(GuruSeeder::class);
        $this->delete(uri:'/api/gurus/logout', headers:[
            "Authorization"=> 'salah'
         ])
        ->assertStatus(401)
        ->assertJson(
            [
                "errors"=> [
                    "message"=> [
                        "Unauthorized."
                    ],
                ]
            ]);

    }
}
