<?php

namespace App\Helpers;

use App\Enums\ResponseMessage;

class ApiResponse
{
public static function success($data = null, ResponseMessage $message = ResponseMessage::SUCCESS, $status = 200)
    {
        return response()->json([
            'message' => $message->message(),
            'data' => $data,
        ], $status);
    }

    public static function error(ResponseMessage $message, $status = 400, $errors = null)
    {
        return response()->json([
            'message' => $message->message(),
            'errors' => $errors,
        ], $status);
    }
}
