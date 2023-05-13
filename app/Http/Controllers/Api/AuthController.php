<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'email' => 'required|email|max:191|unique:users,email',
            'password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->messages(),
            ]);
        } else {
            $users = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $users->createToken($users->email . '_token')->plainTextToken;

            return response()->json([
                'status' => 200,
                'userName' => $users->name,
                'token' => $token,
                'message' => 'resgister successfully',
            ]);
        }
    }



    public function Login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validator_errors' => $validator->message(),
            ]);
        } else {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => '401',
                    'message' => 'invalid credentials'
                ]);
            } else {


                $token = $user->createToken($user->email . '_token')->plainTextToken;

                return response()->json([
                    'status' => 200,
                    'userName' => $user->name,
                    'token' => $token,
                    'message' => 'Loged in successfully',
                ]);
            }
        }
    }
}
