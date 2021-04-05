<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Candidate;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Redis;
//use DB;

class CandidateController extends Controller
{	
	const ROLEMANAGER = 'manager';
	const ROLEAGENT = 'agent';
    public function saveCandidate(Request $request){	
    	//$token=JWTAuth::getToken();
    	//if ($token) {
    	if(Auth::check()){
    		$user = Auth::user();//user authentificated
	    	if($user->role==self::ROLEMANAGER){
	    		//Verify fields necessaries as name, source and owner is necessary, but if you don't put the owner, a random value and correct of course will be assigned for that field
	    		//owner must be a select in front
	    		$fields = $request->only('name', 'source');
	    		$validator=Validator::make($fields,[
                	'name' => 'required',
                	'source' => 'required'
            	]);
            	if ($validator->fails()) {
		            return response()->json([
		            	'meta' => [
		            		'success' => false,
		            		'message' => 'name and source are necessaries',
		                	'errors' => $validator->errors()
		            	]
		            ], 422);
		        }
	    		$users=User::all();
	    		$num=random_int(1, 10);
	    		$myUser=null;
	    		$i=1;
	    		$owner=$request->owner;
	    		$myOwner=null;
	    		if($request->owner==null){
	    			//Random value owner
	    			foreach ($users as $key) {
		    			if($num==$i){
		    				$myUser=$key;
		    				break;
		    			}
	    				$i++;
	    			}
    				$owner=$myUser->id;
	    		}
    			$myOwner=User::where('_id',$owner)->get()->first();
    			//$myOwner=DB::table('users')->where('_id', '=', $owner)->get()->first();
	    		if($myOwner!=null){
	    			$candidate=Candidate::create([
					'name' => $request->name,
					'source' => $request->source,
					//'owner' => $request->owner,
					//'owner' => $myUser->id,
					'owner' => $owner,
					'created_by' => $user->id
					]);
					return response()->json([
						'meta' => [
							'success' => true,
							'errors' => []
						],
		                'data' => $candidate
					],201);
				}
				return response()->json([
					'meta' => [
						'success' => false,
						'errors' => ['owner incorrect'],
					],
				],422);	    		
	    	}//else{//no else if and no else too because only have two roles
    		return response()->json([
				'meta' => [
					'success' => false,
					'errors' => 'user '.self::ROLEAGENT.' can not create'
				]
			],401);//401 or 403 not sure
	    	//}
		}
		return response()->json([
			'meta' => [
				'success' => false,
				'errors' => ["Token expired"]
			]
		],401);
    }

    public function showCandidates(){
    	//$token=JWTAuth::getToken();
    	//if ($token) {
    	if(Auth::check()){
	    	$user = Auth::user();//user authentificated
	    	$candidates=null;
	    	if($user->role==self::ROLEMANAGER){
	    		$candidates=Candidate::all();
	    	}else if($user->role==self::ROLEAGENT){
	    		$candidates=Candidate::where('owner',$user->id)->get();
	    	}
	    	return response()->json([
				'meta' => [
					'success' => true,
					'errors' => []
				],
	            'data' => $candidates
			],200);
		}
    	return response()->json([
			'meta' => [
				'success' => false,
				'errors' => ["Token expired"]
			]
		],401);
    }

    public function showCandidate(Candidate $candidate){
    	//$token=JWTAuth::getToken();
    	if (Auth::check()) {
	    	$myCandidate=Candidate::where('_id',$candidate->id)->get()->first();
	    	if($myCandidate==null){
	    		return response()->json([
					'meta' => [
						'success' => false,
						'errors' => ['No lead found'],
					],
				],404);
	    	}
	    	$user = Auth::user();//user authentificated
	    	if($user->role==self::ROLEMANAGER){
	    		return response()->json([
					'meta' => [
						'success' => true,
						'errors' => [],
						//'owner' => $candidate->owner
					],
	                'data' => $candidate
				],200);
	    	}else if($user->role==self::ROLEAGENT){
	    		// $candidate->owner == $user -> id
	    		//Get owner from json $candidate
	    		if($candidate->owner==$user->id){
	    			return response()->json([
						'meta' => [
							'success' => true,
							'errors' => []
						],
		                'data' => $candidate
					],200);
	    		}
	    		return response()->json([
					'meta' => [
						'success' => false,
						'errors' => ["Not Authorized"]
					]
				],401);
	    	}
    	}
    	return response()->json([
			'meta' => [
				'success' => false,
				'errors' => ["Token expired"]
			]
		],401);
    	
    }

}
