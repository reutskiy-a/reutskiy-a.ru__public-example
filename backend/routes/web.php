<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;


Route::any('/', function() {
    return response(['data' => 'hello :) what are u doing here? :)'], 200);
});;
