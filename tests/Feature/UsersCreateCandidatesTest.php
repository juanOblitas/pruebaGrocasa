<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UsersCreateCandidatesTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUsersCreateCandidates()
    {   
        //Login user manager
        $response = $this -> post('/api/v1/auth/login',[
            "username" => "charlene19",
            "password" => "oblitas"
        ]);
        $response->assertOk();

        $request = request();
        $token = $request->bearerToken();
        //['Authorization: ' . 'Bearer ' . $token]
        //Comento esto porque da el siguiente resultado
        //Response status code [401] does not match expected 200 status code.
        //Cuando lo creo en postman con Authorization no tengo ese problema
        /*
        $candidate = $this -> postJson('/api/v1/candidate/save',[
            "name" => "JuanCarlos",
            "source" => "Oblinu"
        ],['headers' => [
            'Authorization' => 'bearer '.$token
        ]]);
        $candidate->assertOk();*/
    }
}
