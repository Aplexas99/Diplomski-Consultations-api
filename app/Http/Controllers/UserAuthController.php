<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class UserAuthController extends Controller
{
    protected $user;
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        $data['password'] = bcrypt($request->password);

        $user = User::create($data);

        $token = $user->createToken('API Token')->accessToken;

        return response([ 'user' => $user, 'token' => $token]);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($data)) {
            return response(['error_message' => 'Incorrect Details. 
            Please try again']);
        }

        $token = auth()->user()->createToken('API Token')->token->id;

        return response(['user' => auth()->user(), 'token' => $token]);

    }

    public function logout(Request $request)
    {
        $this->validate($request, [
          'allDevice' => 'required|boolean',
        ]);

        $user = Auth::user();
            if ($request->allDevice) {
              $user->tokens()->each(function ($token, $key) {
                $token->delete();
              });
              return response()->json('Logged out from all devices', 200);
        } 
        return response()->json(auth()->user());
        $userToken = $user->token();
        $userToken->delete();

        return response()->json('Logged out', 200);
      }
}
