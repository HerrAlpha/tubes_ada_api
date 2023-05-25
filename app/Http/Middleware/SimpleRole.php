<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SimpleRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $args = func_get_args();

        if (isset($request->user()->role)) {
            if (!in_array($request->user()->role, array_slice($args, 2))) {
                return $this->sendErrorForbidden();
            }
        } else {
            return $this->sendErrorForbidden();
        }

        return $next($request);
    }

    private function sendErrorForbidden(): Response
    {
        $res = [
            'success'   => false,
            'message'   => 'Forbidden!'
        ];

        return response()->json($res, Response::HTTP_FORBIDDEN);
    }
}
