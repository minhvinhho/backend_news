<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserChangePassRequest;
use App\Models\User;

class AuthController extends Controller
{
    // Ham dang ky 

    public function register(UserRegisterRequest $request)
    {
        $validated = $request->validated();
        $validated["password"] = bcrypt($validated["password"]);
        $user = User::create($validated);
        return response()->json(["user" => $user, 'msg' => 'Sign up successfully'], 200);
    }

    // Dang nhap

    public function login(UserLoginRequest $request)
    {
        $validated = $request->validated();

        if (auth()->attempt($validated)) {
            $user = auth()->user();
            $token = $user->createToken("app")->accessToken;
            return response()->json(['user' => $user, 'token' => $token, 'msg' => "Successful login"], 200);
        } else {
            return response()->json(['msg' => "Login failed"], 211);
        }
    }

    // Get me

    public function getMe()
    {
        $user = auth()->user();
        return response()->json(['user' => $user], 200);
    }

    // Change pass

    public function changePassword(UserChangePassRequest $request)
    {

        $validated = $request->validated();
        $user = auth()->user();
        $isUpdate = User::where('id', $user->id)
           // ->where('password', bcrypt($validated["old_password"]))
            ->update(["password" => bcrypt($validated["password"])]);
        if ($isUpdate) {
            return response()->json(['msg' => "Change password successfully"], 200);
        } else {
            return response()->json(['msg' => "Change password failed"], 211);
        }
    }
}
