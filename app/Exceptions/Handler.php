<?php
namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        $statusCode = 500;
        $message = 'Terjadi kesalahan pada server';
        $data = null;

        // Pastikan API mengembalikan response JSON
        if ($request->expectsJson()) {
            // Menangani validasi
            if ($exception instanceof ValidationException) {
                $statusCode = 422;
                $message = 'Validasi gagal';
                $data = $exception->errors();
            }

            // Menangani authentication error
            if ($exception instanceof AuthenticationException) {
                $statusCode = 401;
                $message = 'Anda belum login atau token tidak valid';
            }

            // Menangani ModelNotFoundException (query gagal menemukan data)
            if ($exception instanceof ModelNotFoundException) {
                $statusCode = 404;
                $message = 'Data tidak ditemukan';

                // Cek jika pesan error mengandung "No query results for model"
                if (str_contains($exception->getMessage(), 'No query results for model')) {
                    $message = 'Data yang Anda cari tidak ditemukan';
                }
            }

            // Menangani HttpException (termasuk 404 dari routing API)
            if ($exception instanceof HttpException) {
                $statusCode = $exception->getStatusCode();
                $message = $exception->getMessage() ?: 'Halaman tidak ditemukan';

                if ($statusCode === 404) {
                    $message = 'Endpoint API tidak ditemukan';
                }
            }

            return response()->json([
                'status' => $statusCode,
                'message' => $message,
                'data' => $data
            ], $statusCode);
        }

        // Jika bukan API, gunakan default handler
        return parent::render($request, $exception);
    }
}
