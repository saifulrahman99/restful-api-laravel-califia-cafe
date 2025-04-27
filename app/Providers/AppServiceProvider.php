<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Cek apakah user dengan email tertentu sudah ada
        if (!User::where('email', env('ADMIN_EMAIL'))->exists()) {
            // Insert user baru
            User::create([
                'name' => env('ADMIN_NAME'),
                'email' => env('ADMIN_EMAIL'),
                'password' => Hash::make(env('ADMIN_PASSWORD')), // Hash password
            ]);
        }
    }
}
