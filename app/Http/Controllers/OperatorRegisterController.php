<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Operator;
use Illuminate\Support\Facades\Hash;

class OperatorRegisterController extends Controller
{
    function register(Request $request){
        $resp = array();

    	$firstName = $request->input('first_name');
    	$lastName = $request->input('last_name');
    	$authId = $request->input('auth_id');
    	$date_of_birth = $request->input('dob');
        $password = $request->input('password');

        $auth_id_check = Operator::where('auth_id',$authId)->take(1)->get();

        if($auth_id_check->count() > 0){
            $resp['msg'] = 'Auth Id taken';
            $resp['id'] = 0;
            $resp['error'] = 2;
            $resp['success'] = 0;
        }else{
            $operator = new Operator();
            $operator->first_name = $firstName;
            $operator->last_name = $lastName;
            $operator->auth_id = $authId;
            $operator->dob = $date_of_birth;
            $operator->password = Hash::make($password);

            if($operator->save()){
                $resp['msg'] = 'Process up successful';
                $resp['id'] = $operator->id;
                $resp['error'] = 0;
                $resp['success'] = 1;
            }else{
                    $resp['msg'] = 'Process up failed';
                    $resp['id'] = 0;
                    $resp['error'] = 1; 
                    $resp['success'] = 0;
            }
        }
        
        return json_encode($resp);
    }
}
