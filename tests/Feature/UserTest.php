<?php

namespace Tests\Feature;

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
}
