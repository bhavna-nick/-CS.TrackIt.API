<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
         'http://trackit.cueserve.com/public/registerm1',
         'http://trackit.cueserve.com/api/user/authenticate',
         'http://trackit.cueserve.com/api/users',
         'http://trackit.cueserve.com/public/api/users/*',
         'http://trackit.cueserve.com/public/api/users/*',
    ];
}
