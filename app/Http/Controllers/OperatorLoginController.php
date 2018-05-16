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
				$resp['user'] = ['first_name'=>$user[0]->first_name,
                                'last_name'=>$user[0]->last_name,
                                'auth_id'=>$user[0]->auth_id,
                                'image'=>$user[0]->image];
				$resp['error'] = 0;
				$resp['success'] = 1;
    		
	    	}else{
	    		$resp['msg'] = 'Incorrect password';
				$resp['id'] = 0;
				$resp['error'] = 1; 
				$resp['success'] = 0;
	    	}

    	}else{
    		
    		$resp['msg'] = 'Login failed';
    		$resp['id'] = 0;
    		$resp['error'] = 2;
    		$resp['success'] = 0;

    	}

    	return json_encode($resp);
    }

    function save(Request $request){
        $id = $request->input('id');
        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $password = $request->input('password');

        $user = Operator::find($id);

        if($first_name != null && $first_name !=''){
            $user->first_name = $first_name;
        }

        if($last_name != null && $last_name !=''){
            $user->last_name = $last_name;
        }

        if($password != null && $password !=''){
            $user->password = Hash::make($password);
        }

        if($user->save()){
            $resp['msg'] = 'Save successful';
            $resp['id'] = $user->id;
            $resp['user'] = ['first_name'=>$user->first_name,
                            'last_name'=>$user->last_name,
                            'auth_id'=>$user->auth_id];
            $resp['error'] = 0;
            $resp['success'] = 1;
        }else{
            $resp['msg'] = 'Save failed';
            $resp['id'] = 0;
            $resp['error'] = 1;
            $resp['success'] = 0;
        }
        

        return json_encode($resp);
    }

    function uploadPhoto()
    {

        $resp = array();

        if (isset($_POST["image"])) {

            $encoded_string = $_POST["encoded_string"];
            $image_name = $_POST["image"];
            $id = $_POST["id"];

            $decoded_string = base64_decode($encoded_string);

            $path = public_path() . '/user_images/' . $image_name;

            $file = fopen($path, 'wb');
            $is_written = fwrite($file, $decoded_string);
            if ($is_written) {
                fclose($file);
                sleep(10);

                $u = Operator::find($id);
                $u->image = $image_name;
                $u->save();

                $resp['msg'] = "success";
                $resp['success'] = 1;
                $resp['error'] = 0;
                $resp['image'] = $image_name;
                return json_encode($resp);
            } else {
                $resp['msg'] = "failed";
                $resp['success'] = 0;
                $resp['error'] = 1;
                return json_encode($resp);
            }
        }
    }
}
