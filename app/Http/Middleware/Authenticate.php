<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): void
    {
        if ($request->expectsJson()) {
            abort(response()->json([
                'success'    => false,
                'message'   => 'Unauthorized'
            ], Response::HTTP_UNAUTHORIZED));
        } else {
            abort(route('login'));
        }
    }
}
