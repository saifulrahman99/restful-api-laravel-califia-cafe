<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('discounts:expire', function () {
})->hourly();
Artisan::command('tokens:expire', function () {
})->daily();
