<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Candidate;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

class CandidateController extends Controller
{	
	const ROLEMANAGER = 'manager';
	const ROLEAGENT = 'agent';
    public function saveCandidate(Request $request){	
    	$token=JWTAuth::getToken();
    	if ($token) {
    		$user = Auth::user();//user authentificated
	    	if($user->role==self::ROLEMANAGER){
	    		//owner could be a select in front
	    		$users=User::all();
	    		$num=random_int(1, 10);
	    		$i=1;
	    		$myUser=null;
	    		foreach ($users as $key) {
	    			if($num==$i){
	    				$myUser=$key;
	    			}
	    			$i++;
	    		}
	    		$candidate=Candidate::create([
				'name' => $request->name,
				'source' => $request->source,
				//'owner' => $request->owner,
				'owner' => $myUser->id,
				'created_by' => $user->id
				]);
				return response()->json([
					'meta' => [
						'success' => true,
						'errors' => []
					],
	                'data' => $candidate
				],200);
	    	}//else{
    		return response()->json([
				'meta' => [
					'success' => true,
					'errors' => 'user '.self::ROLEAGENT.' can not create'
				]
			],401);
	    	//}
		}
		return response()->json([
			'meta' => [
				'success' => false,
				'errors' => "Token expired"
			]
		],401);
    }

    public function showCandidates(){
    	$token=JWTAuth::getToken();
    	if ($token) {
	    	$user = Auth::user();//user authentificated
	    	$candidates="";
	    	if($user->role==self::ROLEMANAGER){
	    		$candidates=Candidate::all();
	    	}else if($user->role==self::ROLEAGENT){
	    		$candidates=Candidate::where('owner',$user->id)->get()->first();
	    	}
	    	//if($candidates!=""){
    		return response()->json([
				'meta' => [
					'success' => true,
					'errors' => []
				],
                'data' => $candidates
			],201);
	    	//}
    	}
    	return response()->json([
			'meta' => [
				'success' => false,
				'errors' => "Token expired"
			]
		],401);
    }

    public function showCandidate(Candidate $candidate){
    	$token=JWTAuth::getToken();
    	if ($token) {
    		$user = Auth::user();//user authentificated
	    	$myCandidate=Candidate::where('id',$candidate)->get()->first();
	    	if($user->role==self::ROLEMANAGER){
	    		return response()->json([
					'meta' => [
						'success' => true,
						'errors' => []
					],
	                'data' => $myCandidate
				],201);
	    	}else if($user->role==self::ROLEAGENT){
	    		if($myCandidate==$user->id){
	    			return response()->json([
						'meta' => [
							'success' => true,
							'errors' => []
						],
		                'data' => $myCandidate
					],201);
	    		}
	    		return response()->json([
					'meta' => [
						'success' => false,
						'errors' => "Not Authorized"
					]
				],401);
	    	}
    	}
    	return response()->json([
			'meta' => [
				'success' => false,
				'errors' => "Token expired"
			]
		],401);
    	
    }

}
