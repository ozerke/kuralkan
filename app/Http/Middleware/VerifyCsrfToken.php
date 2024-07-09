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
        'panel/admin/variations/*/delete-media',
        'panel/admin/variations/*/reorder-media',
        'panel/admin/products/*/delete-media',
        'panel/admin/products/*/reorder-media',
        '/panel/admin/products/specifications/*/update-specification',
        '/panel/admin/products/specifications/*/update-value',
        'panel/admin/categories/update-category/*',
        'panel/admin/categories/update-slug/*',
        'panel/admin/categories/*/update-display-order',
        'data/check-national-id',
        'data/get-stocks',
        'api/get-installments',
        '/handle-payment/*',
        '/handle-fee-payment/*',
        '/handle-bond-payment/*',
        '/testing/success',
    ];
}
