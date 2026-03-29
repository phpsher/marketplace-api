<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    use ResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()->role->role !== 'admin') {
            return $this->error(
                message: 'You are not admin',
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        }

        return $next($request);
    }
}
