<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash; // Import Hash facade

class AuthController extends Controller
{

    //    ========================================
    //    =============signup ====================
    //    ========================================
    public function signup(Request $request){
        // Validate the request data
        $validateUser = Validator::make(
            $request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ]);

        // If validation fails, return error response
        if($validateUser->fails()){
            return response()->json([
                'status' => false,
                'message'=>"Validation error",
                'errors' => $validateUser->errors()->all()
            ], 401);
        }

        // Hash the password before saving
        //$hashedPassword = bcrypt($request->password);

        // Create the new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        // Return success response
        return response()->json([
            'status' => true,
            'message' => "User Created Successfully",
            'user' => $user,
        ], 200);
    }



//    ========================================
//    =============Login =====================
//    ========================================

    public function login(Request $request){
        $validateUser = Validator::make(
            $request->all(),[
            'email' => 'required|email',
            'password' => 'required',
        ]);


        if($validateUser->fails()){
            return response()->json([
                'status' => false,
                'message'=>"Authentication Failed",
                'errors' => $validateUser->errors()->all()
            ], 401);
        }

        if(Auth::attempt(['email'=>$request->email,'password'=>$request->password])){
            $authUser = Auth::user();
            return response()->json([
                'status' => true,
                'message'=>"User Login Successfully",
                'token'=>$authUser->createToken('API Token')->plainTextToken,
                'token_type'=>'bearer',
            ], 200);

        }else{
            return response()->json([
                'status' => false,
                'message'=>"Email and Password does not matched Failed",
            ], 401);
        }

    }


    //    ========================================
    //    =============Login =====================
    //    ========================================
    public function logout(Request $request){
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'message'=>"You Logged Out Successfully",
        ], 200);
    }
}
