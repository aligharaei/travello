<?php

use Illuminate\Http\JsonResponse;


if (!function_exists('success')) {
    /**
     * Return successful response from the application.
     *
     * @param string $message
     * @param null $object
     * @param int $status
     *
     * @return JsonResponse
     */
    function success(string $message = '', $object = null, int $status = 200): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $object], $status);
    }
}

if (!function_exists('failed')) {
    /**
     * Return failed response from the application.
     *
     * @param string $message
     * @param int $status
     *
     * @return JsonResponse
     */
    function failed(string $message = '', int $status = 500): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message, 'statusCode' => $status], $status);
    }
}
