<?php


namespace App\Exceptions;

use App\Enums\ResponseMessage;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e): JsonResponse|Response
    {
        if ($request->expectsJson()) {
            if ($e instanceof ValidationException) {
                return response()->json([
                    'status' => 'error',
                    'message' => ResponseMessage::VALIDATION_ERROR,
                    'errors' => $e->errors()
                ], 422);
            }

            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    'status' => 'error',
                    'message' => ResponseMessage::NOT_FOUND
                ], 404);
            }

            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    'status' => 'error',
                    'message' => ResponseMessage::NOT_FOUND
                ], 404);
            }

            if ($e instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'status' => 'error',
                    'message' => ResponseMessage::FORBIDDEN
                ], 405);
            }

            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'status' => 'error',
                    'message' => ResponseMessage::UNAUTHORIZED
                ], 401);
            }
            Log::error($e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => 'error',
                'message' => ResponseMessage::SERVER_ERROR,
                'error_detail' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
        return parent::render($request, $e);
    }
}
