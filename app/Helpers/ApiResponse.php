<?php

namespace App\Helpers;

use App\Enums\ResponseMessage;

class ApiResponse
{
    public static function commonResponse($data = null, ResponseMessage $message = ResponseMessage::SUCCESS, $status = 200)
    {
        return response()->json([
            'message' => $message->message(),
            'data' => $data,
        ], $status);
    }
}
