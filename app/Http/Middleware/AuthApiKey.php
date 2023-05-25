<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('x-api-key');

        if ($key === env('X_API_KEY')) {
            return $next($request);
        } else {
            return abort(response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], Response::HTTP_UNAUTHORIZED));
        }
    }
}
