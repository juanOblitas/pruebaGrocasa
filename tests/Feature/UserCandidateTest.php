<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Response;
//use Illuminate\Http\Response;


class UserCandidateTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_creating_candidate()
    {   
        //Login user with only one field username or password
        $userLogin = $this -> post('/api/v1/auth/login',[
            "username" => "charlene19"
        ]);
        $userLogin->assertStatus(422);
        //Login user with credentials incorrect
        $userLogin = $this -> post('/api/v1/auth/login',[
            "username" => "charlene19",
            "password" => "passwordincorrect"
        ]);
        $userLogin->assertStatus(401);
        //Login user with role manager
        $userLogin = $this -> post('/api/v1/auth/login',[
            "username" => "charlene19",
            "password" => "oblitas"
        ]);
        $userLogin->assertOk();
        //Get token from user
        $user = auth()->user();
        $token=JWTAuth::fromUser($user);
        //User with rol manager creating candidate
        $candidate = [
            "name" => "Wendy Mandela",
            "source" => "Oblinu"
        ];
        $header = ['Authorization' => 'Bearer '.$token];
        //Creating candidate
        //$createCandidate = $this->actingAs($user)->post('/api/v1/candidate/save', $candidate,$header);
        //$createCandidate->assertCreated();//HTTP Status 201 (Created)
        //User with role manager can see all candidates
        $showCandidates = $this -> actingAs($user)->get('/api/v1/candidates/show', $header);
        $showCandidates->assertStatus(201);

        //User with role manager can see a specify candidate
        $showCandidate = $this -> actingAs($user)->get('/api/v1/candidate/60631415557b0000df0011b2', $header);
        //$showCandidate->assertStatus(201);

        //Logout user with token correct
        $userLogout = $this->actingAs($user)->get('/api/v1/auth/logout',[
            "username" => "charlene19",
            "password" => "oblitas"
        ]);
        $userLogout->assertOk();

        //Login again
        $userLogin = $this -> post('/api/v1/auth/login',[
            "username" => "charlene19",
            "password" => "oblitas"
        ]);
        $userLogin->assertOk();
        //Modify token, put a token invalid
        $token='invalid';
        $header = ['Authorization' => 'Bearer '.$token];
        //Create candidate with token incorrect
        $candidate = [
            "name" => "Isabel Pantoja3",
            "source" => "Farandu"
        ];
        $createCandidate = $this->actingAs($user)->post('/api/v1/candidate/save', $candidate,$header);
        $createCandidate->assertStatus(401);

        //show candidates with token incorrect
        $showCandidates = $this -> actingAs($user)->get('/api/v1/candidates/show', $header);
        $showCandidates->assertStatus(401);

        //Logout user with token incorrect, logout no need token
        $userLogout = $this->actingAs($user)->get('/api/v1/auth/logout',[
            "username" => "charlene19",
            "password" => "oblitas"
        ]);
        $userLogout->assertStatus(200);

        //Login user with role agent
        $userLogin = $this -> post('/api/v1/auth/login',[
            "username" => "sonya.hickle",
            "password" => "oblitas"
        ]);

        $user = auth()->user();
        $token=JWTAuth::fromUser($user);
        //User with rol agent creating candidate
        $candidate = [
            "name" => "Wendy Mandela",
            "source" => "Oblinu"
        ];
        $header = ['Authorization' => 'Bearer '.$token];
        //Creating candidate
        $createCandidate = $this->actingAs($user)->post('/api/v1/candidate/save', $candidate,$header);
        $createCandidate->assertStatus(401);//not authorized

        //Show candidates, only candidate owners
        $showCandidates = $this -> actingAs($user)->get('/api/v1/candidates/show', $header);
        $showCandidates->assertStatus(201);
        $jsonFragment=[
                    'meta' => [
                        'success' => true,
                        'errors' => []
                    ],
                    'data' => null
                ];
        $showCandidates->assertJsonFragment($jsonFragment);

        //

    }
}
