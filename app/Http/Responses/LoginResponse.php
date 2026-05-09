<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if ($user->is_admin) {
            return redirect()->intended(route('admin.home'));
        }

        return redirect()->intended(Fortify::redirects('login', route('dashboard')));
    }
}
