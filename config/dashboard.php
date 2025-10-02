<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dashboard Configuration
    |--------------------------------------------------------------------------
    | Configuración general para el dashboard de administración
    */

    // Configuración de la UI
    'ui' => [
        'page_size' => env('DASHBOARD_PAGE_SIZE', 15),
        'animation_duration' => 300,
        'auto_refresh' => env('DASHBOARD_AUTO_REFRESH', false),
        'refresh_interval' => env('DASHBOARD_REFRESH_INTERVAL', 30000), // ms
    ],

    // Configuración de las tablas
    'tables' => [
        'default_sorting' => 'id ASC',
        'enable_paging' => true,
        'show_record_count' => true,
        'enable_search' => true,
    ],

    // Configuración de validación
    'validation' => [
        'lessons' => [
            'name_min_length' => 3,
            'name_max_length' => 255,
            'description_min_length' => 10,
            'description_max_length' => 2000,
            'level_min' => 1,
            'level_max' => 100,
            'time_min' => 5,
            'time_max' => 600,
        ],
        'courses' => [
            'name_min_length' => 3,
            'name_max_length' => 255,
            'description_min_length' => 10,
            'description_max_length' => 1000,
        ],
    ],

    // Opciones predefinidas
    'options' => [
        'difficulties' => [
            'fácil' => 'Fácil',
            'intermedio' => 'Intermedio',
            'difícil' => 'Difícil',
        ],
        'lesson_default_difficulty' => 'fácil',
        'lesson_default_duration' => 30,
        'lesson_default_level' => 1,
    ],

    // Configuración de alertas
    'alerts' => [
        'success_duration' => 5000,
        'error_duration' => 8000,
        'warning_duration' => 6000,
        'info_duration' => 4000,
    ],

    // URLs y rutas
    'urls' => [
        'login_redirect' => '/',
        'logout_redirect' => '/',
        'dashboard_home' => '/dashboard',
    ],

    // Configuración de archivos
    'assets' => [
        'enable_cdn' => env('DASHBOARD_USE_CDN', true),
        'cdn_fallback' => true,
        'cache_version' => env('ASSET_VERSION', '1.0'),
    ],

    // Configuración de logging para el dashboard
    'logging' => [
        'log_user_actions' => env('LOG_USER_ACTIONS', true),
        'log_api_requests' => env('LOG_API_REQUESTS', false),
        'log_errors' => true,
    ],

    // Features opcionales
    'features' => [
        'enable_debug_info' => env('DASHBOARD_DEBUG', false),
        'enable_export' => env('DASHBOARD_EXPORT', true),
        'enable_import' => env('DASHBOARD_IMPORT', false),
        'enable_bulk_actions' => true,
    ],
];