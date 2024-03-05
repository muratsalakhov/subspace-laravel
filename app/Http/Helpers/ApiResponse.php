<?php

namespace App\Http\Helpers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ответ API
 */
class ApiResponse
{
    public static function success(mixed $data = null, string $message = '', int $code = Response::HTTP_OK): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        return response()->json($response, $code);
    }

    public static function error(mixed $data = null, string $message = '', $code = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'data' => $data,
        ];

        return response()->json($response, $code);
    }
}
