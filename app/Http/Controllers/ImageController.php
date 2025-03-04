<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function show($encryptedUrl)
    {
        try {
            // Dekripsi URL gambar
            $decryptedPath = Crypt::decryptString($encryptedUrl);
            // Pastikan file ada di storage
            if (!Storage::disk('public')->exists($decryptedPath)) {
                return response()->json(['error' => 'File not found'], 404);
            }

            // Tampilkan gambar
            return response()->file(storage_path('app/public/' . $decryptedPath));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid Image URL'], 400);
        }
    }
}

