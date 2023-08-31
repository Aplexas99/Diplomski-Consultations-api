<?php

namespace App\Services;

use Illuminate\Support\Facades\Password;

class AuthService
{
    public function sendResetPasswordLink(string $email)
    {
        $status = Password::sendResetLink([ 'email' => $email ]);

        if($status === Password::RESET_LINK_SENT) {
            return [
                'statusCode' => 200,
                'error' => "Reset link sent.",
            ];
        }
        else {
            return [
                'statusCode' => 403,
                'error' => $status,
            ];
        }
    }
}
