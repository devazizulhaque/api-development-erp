<?php

namespace App\helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class TokenHelper
{
    public static function checkToken($request): ?JsonResponse
    {
        $token_check = DB::table('personal_access_tokens')
            ->where('token', $request->header('token'))
            ->first();

        if (!$token_check) {
            $response = [
                'code' => '201',
                'status' => false,
                'token' => $request->header('token'),
                'data' => null,
                'message' => 'Incorrect Access Token!',
            ];

            return response()->json($response, 200);
        }

        return null; // Token is valid, return null
    }
}