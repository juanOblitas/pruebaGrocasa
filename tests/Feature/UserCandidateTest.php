<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Response;
use App\Models\User;
use App\Models\Candidate;
use DB;
//use Illuminate\Http\Response;


class UserCandidateTest extends TestCase
{
    const ROLEMANAGER = 'manager';
    const ROLEAGENT = 'agent';

    /**
     * test.
     *
     * @return void
     */
    public function test_user_login_and_managing_candidates()
    {
        //This test will be manager or agent is the same for login
        $user = User::where('role',self::ROLEMANAGER)->get()->first();
        //Login user with only one field username or password
        $userLogin = $this -> post('/api/v1/auth/login',[
            "username" => $user->username
        ]);
        $userLogin->assertStatus(422);
        //Login user with credentials incorrect
        $userLogin = $this -> post('/api/v1/auth/login',[
            "username" => $user->username,
            "password" => "passwordincorrect"
        ]);
        $userLogin->assertStatus(401);
        //Login user successfuly
        $userLogin = $this -> post('/api/v1/auth/login',[
            "username" => $user->username,
            "password" => "oblitas"
        ]);
        $userLogin->assertOk();
        
        $candidateWithFieldsOk = [
            "name" => "Juan Carlos Oblitas",
            "source" => "OBLINU"
        ];
        //USER WITH ROLE MANAGER
        //Not pass token or token invalid
        $tokenInvalid = "tokeninvalid";
        $headerInvalid = ['Authorization' => 'Bearer '.$tokenInvalid];
        //Creating candidate ko
        $createCandidate = $this->actingAs($user)->post('/api/v1/candidate/save', $candidateWithFieldsOk,$headerInvalid);
        $createCandidate->assertStatus(401);
        //Get token valid from user and assigned Authorization
        $user = auth()->user();
        $token=JWTAuth::fromUser($user);
        $header= ['Authorization' => 'Bearer '.$token];
        //name and source are necessaries, if not put owner put a random owner
        $candidate = [
            "name" => "Juan Carlos Oblitas",
        ];
        //Creating candidate ko
        $createCandidate = $this->actingAs($user)->post('/api/v1/candidate/save', $candidate,$header);
        $createCandidate->assertStatus(422);
        //Creating candidate ok
        $createCandidate = $this->actingAs($user)->post('/api/v1/candidate/save', $candidateWithFieldsOk,$header);
        $createCandidate->assertCreated();

        //Show candidates ko, token and header invalid
        $showCandidates = $this -> actingAs($user)->get('/api/v1/candidates/show', $headerInvalid);
        $showCandidates->assertStatus(401);
        //Show candidates ok
        $showCandidates = $this -> actingAs($user)->get('/api/v1/candidates/show', $header);
        $showCandidates->assertStatus(200);

        $myCandidate=Candidate::where('source','OBLINU')->get()->first();
        
        //show candidate ko, token and header invalid
        $showCandidate = $this -> actingAs($user)->get('/api/v1/candidate/'.$myCandidate->id, $headerInvalid);
        $showCandidate->assertStatus(401);

        //header valid but candidate not exist
        $candidateId="idcandidateInvalid";
        $showCandidate = $this -> actingAs($user)->get('/api/v1/candidate/'.$candidateId, $header);
        $showCandidate->assertStatus(404);

        //show candidate ok
        $showCandidate = $this -> actingAs($user)->get('/api/v1/candidate/'.$myCandidate->id, $header);
        $showCandidate->assertStatus(200);

        //logout
        $userLogout = $this->actingAs($user)->get('/api/v1/auth/logout',[
            "username" => $user->username,
            "password" => "oblitas"
        ]);
        $userLogout->assertOk();

        //USER WITH ROLE AGENT
        $user = User::where('role',self::ROLEAGENT)->get()->first();
        $userLogin = $this -> post('/api/v1/auth/login',[
            "username" => $user->username,
            "password" => "oblitas"
        ]);
        $user = auth()->user();
        $token=JWTAuth::fromUser($user);
        $header= ['Authorization' => 'Bearer '.$token];
        //Creating candidate ko, because not have Authorization
        $createCandidate = $this->actingAs($user)->post('/api/v1/candidate/save', $candidateWithFieldsOk,$header);
        $createCandidate->assertStatus(401);

        //Token and header correct ok
        $showCandidates = $this -> actingAs($user)->get('/api/v1/candidates/show', $header);
        $showCandidates->assertStatus(200);

        //show a specify candidate
        $myCandidate = DB::table('candidates')
                ->where('users', 'users._id', '=', 'candidates.owner')
                ->get()->first();
        if($myCandidate!=null){
            $showCandidate = $this -> actingAs($user)->get('/api/v1/candidate/'.$myCandidate->id, $header);
            $showCandidate->assertStatus(200);
        }

    }

}
