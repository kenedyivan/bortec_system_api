<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Operator;
use Illuminate\Support\Facades\Hash;

class OperatorLoginController extends Controller
{
    function login(Request $request){
        $resp = array();

    	$authId = $request->input('auth_id');
    	$password = $request->input('password');    	

    	$user = Operator::where('auth_id',$authId)->take(1)
        ->get();
        
        if($user->count() > 0){

    		if (Hash::check($password, $user[0]->password)){
	    		$resp['msg'] = 'Login successful';
				$resp['id'] = $user[0]->id;
				$resp['error'] = 0;
				$resp['success'] = 1;
    		
	    	}else{
	    		$resp['msg'] = 'Incorrect password';
				$resp['id'] = 0;
				$resp['error'] = 1; 
				$resp['success'] = 0;;
	    	}

    	}else{
    		
    		$resp['msg'] = 'Login failed';
    		$resp['id'] = 0;
    		$resp['error'] = 2;
    		$resp['success'] = 0;

    	}

    	return json_encode($resp);
    }
}
