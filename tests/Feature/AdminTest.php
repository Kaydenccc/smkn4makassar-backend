<?php

namespace Tests\Feature;

use App\Models\Admin;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testStoreSuccess(): void
    {
        $this->seed([AdminSeeder::class]);
        $this->post('/api/admin',[
            "username" => 'test1',
            "password" => 'test'
        ], [
            "Authorization"=> 'test'
        ] )
       ->assertStatus(201)
       ->assertJson([
                "data"=> [
                    "username" => 'test1',
                ]
            ]
         );
    }
    public function testStoreFailed(): void
    {
        $this->seed([AdminSeeder::class]);
        $this->post('/api/admin',[
            "username" => '',
            "password" => ''
        ], [
            "Authorization"=> 'test'
        ] )
       ->assertStatus(400)
       ->assertJson([
           "errors" =>[
                "username" => [
                    'The username field is required.'
                ],
                "password" => [
                    'The password field is required.'
                ],
            ]
        ]
        );
    }
    public function testStoreUsernameHasBeenUsed(): void
    {
        $this->seed([AdminSeeder::class]);
        $this->post('/api/admin',[
            "username" => 'test',
            "password" => 'test'
        ], [
            "Authorization"=> 'test'
        ] )
       ->assertStatus(400)
       ->assertJson([
        'errors' => [
            "message"=>[
                "Username has been used."
            ]
        ]
        ]
        );
    }

    public function testLoginSuccess(): void {
        $this->seed([AdminSeeder::class]);

        $this->post('/api/admin/login',[
            "username"=> 'test',
            "password"=>"test"
        ])
        ->assertStatus(200)
        ->assertJson([
            "data"=> [
                "username"=> "test"
            ]
            ]);
        $admin = Admin::where('username', 'test')->first();
        self::assertNotNull($admin->token);
    }
    public function testLoginFailed(): void {
        $this->seed([AdminSeeder::class]);

        $this->post('/api/admin/login',[
            "username"=> 'test1',
            "password"=> "test1"
        ])
        ->assertStatus(401)
        ->assertJson([
            "errors"=> [
                "message"=> [
                    "Username or password is wrong."
                ]
            ]
            ]);
        $admin = Admin::where('username', 'test')->first();
        self::assertNotNull($admin->token);
    }


    public function testGetCurrentAdminSuccess(): void {
        $this->seed([AdminSeeder::class]);
        $this->get('/api/admin/current', [
            "Authorization"=> 'test'
        ])
        ->assertStatus(200)
        ->assertJson([
            "data" => [
                "username" => 'test',
                "token" => 'test'
            ]
        ]);
    }
    public function testGetCurrentAdminUnauthorized(): void {
        $this->seed([AdminSeeder::class]);
        $this->get('/api/admin/current')
        ->assertStatus(401)
        ->assertJson([
            "errors"=>[
                "message"=>[
                    'Unauthorized.'
                ]
            ]
        ]);
    }
    public function testGetCurrentAdminInvalidToken(): void {
        $this->seed([AdminSeeder::class]);
        $this->get('/api/admin/current', [
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

    public function testUpdateAdminSuccess(): void {
        $this->seed([AdminSeeder::class]);
        $oldUser = Admin::where('username', 'test')->first();
        $this->patch('/api/admin/current', [
            "username"=> 'baru'
        ], [
            "Authorization"=> 'test'
        ])
        ->assertStatus(201)
        ->assertJson([
            "data"=>[
                "username"=> 'baru'
            ]
        ]);

        $newGuru = Admin::where('username', 'baru')->first();
        self::assertNotEquals($oldUser->username, $newGuru->username);
    }
    public function testUpdateAdminFailed(): void {
        $this->seed([AdminSeeder::class]);
        $this->patch('/api/admin/current', [
            "username"=> 'barujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdasbarujklasjdkladasdajdkasdasdas'
        ], [
            "Authorization"=> 'test'
        ])
        ->assertStatus(400)
        ->assertJson([
            "errors"=> [
                "username"=> [
                    "The username field must not be greater than 100 characters."
                ],
            ]
        ]);
    }

    public function testLogoutAdminSuccess(): void {
        $this->seed([AdminSeeder::class]);
        $this->delete(uri:'/api/admin/logout', headers:[
            "Authorization"=> 'test'
         ])
        ->assertStatus(200)
        ->assertJson(
            [
                "data"=> true
            ]);
            $user = Admin::where('username', 'test')->first();
            self::assertNull($user->token);
    }
    public function testLogoutAdminFailed(): void {
        $this->seed([AdminSeeder::class]);
        $this->delete(uri:'/api/admin/logout', headers:[
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
