<?php

use Illuminate\Support\Facades\Route;

Route::get('health', function () {
    return response()->json(['status' => 'ok']);
});

Route::get("debug/cors", function () {
    return response()->json([
        "cors_allowed_origins_env" => env("CORS_ALLOWED_ORIGINS", "NOT_SET"),
        "cors_supports_credentials_env" => env("CORS_SUPPORTS_CREDENTIALS", "NOT_SET"),
        "parsed_allowed_origins" => array_filter(array_map("trim", explode(",", env("CORS_ALLOWED_ORIGINS", "http://localhost:3000")))),
        "parsed_supports_credentials" => filter_var(env("CORS_SUPPORTS_CREDENTIALS", false), FILTER_VALIDATE_BOOL),
        "config_cached" => file_exists(base_path("bootstrap/cache/config.php")),
    ]);
});
