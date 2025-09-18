<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Jika request berasal dari API
        if ($request->is('api/*')) {

            // Error Validasi
            if ($e instanceof ValidationException) {
                return response()->json([
                    'message' => 'Validasi gagal',
                    'errors'  => $e->errors(),
                ], 422);
            }

            // Tidak ditemukan
            if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                return response()->json([
                    'message' => 'Resource tidak ditemukan',
                ], 404);
            }

            // Error autentikasi
            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'message' => 'Unauthenticated',
                ], 401);
            }

            // Error umum lainnya
            return response()->json([
                'message' => $e->getMessage() ?: 'Terjadi kesalahan server',
            ], 500);
        }

        // Default render (untuk web / non API)
        return parent::render($request, $e);
    }
}
