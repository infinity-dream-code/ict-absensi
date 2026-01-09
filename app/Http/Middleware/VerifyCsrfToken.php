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
        //
    ];

    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        // Allow GET logout dengan fallback parameter untuk handle expired token
        if ($request->isMethod('get') && $request->has('fallback') && 
            ($request->is('logout') || $request->is('admin/logout'))) {
            return true;
        }

        return parent::tokensMatch($request);
    }
}
