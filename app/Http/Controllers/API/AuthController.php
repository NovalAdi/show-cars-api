<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $req)
    {
        $input = $req->all();
        $validator = Validator::make($input, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $validator->errors(),
                    'data' => []
                ]
            );
        }

        $passwordHash = Hash::make($req->input('password'));
        $inputuser = [
            'name' => $req->input('name'),
            'email' => $req->input('email'),
            'password' => $passwordHash
        ];

        $user = User::create($inputuser);

        $token = $user->createToken('authToken')->plainTextToken;

        $data = [
            'token' => $token,
            'user' => $user,
        ];

        return response()->json(
            [
                'status' => true,
                'message' => 'Register succeed',
                'data' => $data
            ]
        );
    }

    public function login(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'password' => 'required',
        ],);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $validator->errors(),
                    'data' => []
                ],
                400
            );
        }

        $loginType = filter_var($req->input('name'), FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $credentials = [
            $loginType => $req->input('name'),
            'password' => $req->input('password')
        ];

        if (auth()->attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;

            $data = [
                'token' => $token,
                'user' => $user,
            ];
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Login succeed',
                    'data' => $data
                ]
            );
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Login failed',
                    'data' => []
                ],
                401
            );
        }
    }

    public function logout(Request $req)
    {
        $req->user()->currentAccessToken()->delete();
        return response()->json(
            [
                'status' => true,
                'message' => 'Logout succeed',
            ]
        );
    }

    function getUser($id) {
        $user = User::find($id);

        if (!$user) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'data not found',
                ],
                404
            );
        }

        return response()->json(
            [
                'status' => true,
                'data' => $user,
            ]
        );
    }
}
