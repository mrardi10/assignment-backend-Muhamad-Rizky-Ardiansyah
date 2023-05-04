<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'signup']]);
    }

    //
    public function index()
    {

        $data = User::paginate(10);

        return response()->json(['data' => $data]);
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required|min:2',
            'password' => 'required|min:5',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->createNewToken($token);
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            'username' => 'required|min:2|unique:users',
            'password' => 'required|min:5',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'fullname' => $request->fullname,
            'username' => $request->username,
            'password' => bcrypt($request->password)
        ]);

        $token = Auth::login($user);
        return response()->json([
            'succees' => true,
            'message' => 'User successfully registered',
            $this->createNewToken($token)
        ], 200);
    }

    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
