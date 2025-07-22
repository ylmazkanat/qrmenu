<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // API routes
        'api/*',
        // Login ve register için CSRF korumasını devre dışı bırak
        '/login',
        '/register',
        '/logout',
        // Restaurant routes
        'restaurant/orders',
        'restaurant/kitchen/*',
        'restaurant/cashier/*',
        'restaurant/api/*',
        'restaurant/orders/*',
    ];
} 