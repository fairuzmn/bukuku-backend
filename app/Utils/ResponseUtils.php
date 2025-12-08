<?php

namespace App\Utils;

use Illuminate\Http\JsonResponse;

class ResponseUtils
{
    public static function baseResponse(int $status = 200, string $message = '', mixed $data = null): JsonResponse
    {
        $response = [
            'code' => $status,
            'message' => $message,
            'data' => $data,
        ];

        return response()->json($response, $status);
    }

    public static function errorResponse(mixed $error, int $status = 400, mixed $data = null): JsonResponse
    {
        if ($error instanceof \Throwable) {
            $message = $error->getMessage();
        } else {
            $message = (string) $error;
        }

        return self::baseResponse($status, $message, $data);
    }
}
