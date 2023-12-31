<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('token');
        if (!$token) {
            return response()->json([
                'code' => '400',
                'status' => false,
                'message' => 'Token not provided',
            ], 400);
        }

        $tokenCheck = DB::table('personal_access_tokens')->where('token', $token)->exists();

        if (!$tokenCheck) {
            return response()->json([
                'code' => '201',
                'status' => false,
                'token' => $token,
                'data' => null,
                'message' => 'Incorrect Access Token!',
            ], 200);
        }

        return $next($request);
    }
}
