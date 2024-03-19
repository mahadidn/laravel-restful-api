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

    public function testUpdatePasswordSuccess(){
        
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'testtsts')->first();

        $this->patch('/api/users/current', 
            [
                "password" => "baru"
            ],
            [ 
                "Authorization" => "test"
            ]
            )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "testtsts",
                    "name" => "test test test",
                ]
            ]);

        $newUser = User::where('username', 'testtsts')->first();
        self::assertNotEquals($oldUser->password, $newUser->password);

    }

    public function testUpdateNameSuccess(){

        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'testtsts')->first();

        $this->patch('/api/users/current', 
            [
                "name" => "mahadi"
            ],
            [ 
                "Authorization" => "test"
            ]
            )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "testtsts",
                    "name" => "mahadi",
                ]
            ]);

        $newUser = User::where('username', 'testtsts')->first();
        self::assertNotEquals($oldUser->name, $newUser->name);

    }


    public function testUpdateFailed(){
        $this->seed([UserSeeder::class]);

        $this->patch('/api/users/current', 
            [
                "name" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Atque, ex totam debitis ad doloremque odio nobis odit sunt consectetur deserunt neque nemo repellendus optio, quas sapiente libero assumenda reiciendis sed maxime facere sint autem modi. Perferendis necessitatibus earum eligendi distinctio cumque ipsam consectetur culpa nesciunt a placeat. Esse accusamus exercitationem, sit alias, recusandae inventore quam impedit magnam quae, ratione similique quod. Saepe laudantium temporibus dolorum similique delectus consequatur dolor atque, libero architecto voluptas autem accusantium optio ullam placeat nulla ipsam ad corrupti porro esse? Impedit doloremque, voluptas ipsa praesentium aliquid reprehenderit error debitis! Quos vitae cumque numquam commodi ratione, mollitia unde non debitis velit, qui eligendi adipisci voluptate atque repudiandae, sed temporibus libero repellat explicabo. Nam sunt explicabo dolores neque"
            ],
            [ 
                "Authorization" => "test"
            ]
            )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => [
                        "The name field must not be greater than 100 characters."
                    ]
                ]
            ]);

    }

    public function testLogoutSuccess(){

        $this->seed([UserSeeder::class]);

        $this->delete(uri:'/api/users/logout', headers:[
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);

        $user = User::where('username', 'testtsts')->first();
        self::assertNull($user->token);

    }

    public function testLogoutFailed(){
        $this->seed([UserSeeder::class]);

        $this->delete(uri:'/api/users/logout', headers:[
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
