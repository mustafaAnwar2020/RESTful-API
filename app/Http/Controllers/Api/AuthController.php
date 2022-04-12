<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Traits\ResponseJson;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ResponseJson;
    public function login(Request $request){
        if(Auth::attempt(['email'=>$request->email,'password'=>$request->password])){
            $user = Auth::user();
            $success['token'] = $user->createToken('appToken')->plainTextToken;
            $success['name'] = $user->name;
            return $this->sendResponse($success,'You logged in successfully!' );
        }
        else{
            return $this->sendError(['error'=>'User unauthorized'],'wrong or invalid credintials');
        }
    }

    public function register(Request $request){
        $input = $request->all();
        $validator = Validator::make($input,[
            'name' =>"required|string",
            'email' => 'required|email|max:255',
            'password' =>'required|string|min:8',
            'c_password' => 'required|string|min:8|same:password'
        ]);
        if($validator->fails())
        {
            return $this->sendError($validator->errors(),'someting is wrong!');
        }
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('appToken')->plainTextToken;
        return $this->sendResponse($success,'user created successfully!');
    }
}
