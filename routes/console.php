<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command(\App\Console\Commands\ExpireDiscounts::class, function () {
})->hourly();
Artisan::command(\App\Console\Commands\ExpireTokens::class, function () {
})->daily();
