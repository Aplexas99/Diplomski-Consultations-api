<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\NewPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->data['email_or_username']) 
            ->orWhere('name', $request->data['email_or_username'])
            ->first();

        if (!$user || md5($request->data['password']) != $user->password) {
            throw ValidationException::withMessages([
                'email_or_username' => [ 'The provided credentials are incorrect.' ],
            ]);
        }

        $apiToken = $user->createToken('test')->plainTextToken;

        return [
            'data' => [
                'api_token' => $apiToken,
                'user' => new UserResource($user),
            ],
        ];
    }

    public function loginAdmin(LoginRequest $request)
    {
        $user = User::where('email', $request->data['email_or_username']) 
            ->orWhere('name', $request->data['email_or_username'])
            ->first();

        if (!$user || md5($request->data['password']) != $user->password) {
            throw ValidationException::withMessages([
                'email_or_username' => [ 'The provided credentials are incorrect.' ],
            ]);
        }

        if($user->role->name != 'admin') {
            throw ValidationException::withMessages([
                'email_or_username' => [ 'The user is not admin.' ],
            ]);
        }
        $apiToken = $user->createToken('test')->plainTextToken;

        return [
            'data' => [
                'admin_api_token' => $apiToken,
                'user' => new UserResource($user),
            ],
        ];
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        $request->user()->tokens()->delete();
    }

    public function sendResetPasswordLink(NewPasswordRequest $request)
    {
        $data = $this->authService->sendResetPasswordLink($request->email);

        return response()->json($data, $data['statusCode']);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ]);
                $user->save();
            }
        );

        if($status === Password::PASSWORD_RESET) {
            $data = [
                'statusCode' => 200,
                'error' => "Password reset.",
            ];
        }
        else {
            $data = [
                'statusCode' => 403,
                'error' => $status,
            ];
        }
        return response()->json($data, $data['statusCode']);

        // php artisan auth:clear-resets
        // $schedule->command('auth:clear-resets')->everyFifteenMinutes();
    }

    public function getLoggedUser(Request $request)
    {
        return new UserResource($request->user());
    }

}
