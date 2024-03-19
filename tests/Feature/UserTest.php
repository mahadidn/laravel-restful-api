<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess(){

        $this->post('/api/users', [
            'username' => "mahadi",
            "password" => "rahasia",
            "name" => "Mahadi Dwi Nugraha"
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "username" => "mahadi",
                    "name" => "Mahadi Dwi Nugraha"
                ]
            ]);

    }


    public function testRegisterFailed(){

        $this->post('/api/users', [
            'username' => "",
            "password" => "",
            "name" => ''
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "username" => [
                        "The username field is required."
                    ],
                    "password" => [
                        "The password field is required."
                    ],
                    "name" => [
                        "The name field is required."
                    ]
                ]
            ]);

    }

    public function testRegisterUsernameAlreadyExists(){

        // daftar dulu yang sudah berhasil
        $this->testRegisterSuccess();

        // daftar lagi pake username yg sama
        $this->post('/api/users', [
            'username' => "mahadi",
            "password" => "rahasia",
            "name" => "Mahadi Dwi Nugraha"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "username" => [
                        "username already registered"
                    ]
                ]
            ]); 
    }

    public function testLoginSuccess(){
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => "testtsts",
            "password" => "test123"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "testtsts",
                    "name" => "test test test"
                ]
            ]);
            
        $user = User::where('username', 'testtsts')->first();
        self::assertNotNull($user->token);

    }

    public function testLoginFailedUsernameNotFound(){

        $this->post('/api/users/login', [
            'username' => "testtsts",
            "password" => "test123"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "username or password wrong"
                    ]
                ]
            ]);

    }

    public function testLoginFailedPasswordWrong(){

        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => "testtsts",
            "password" => "salah"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "username or password wrong"
                    ]
                ]
            ]);

    }

    public function testGetSuccess(){

        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
            "Authorization" => "test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "testtsts",
                    "name" => "test test test",
                ]
            ]);

    }

    public function testGetUnauthorized(){
        $this->get('/api/users/current', [
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);

    }

    public function testGetInvalidToken(){

        $this->seed([UserSeeder::class]);
        $this->get('/api/users/current', [
            "Authorization" => "salah"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);

    }



}
