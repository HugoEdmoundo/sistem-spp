<?php
// app/Http/Middleware/MuridMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MuridMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isMurid()) {
            return $next($request);
        }

        return redirect('/')->with('error', 'Akses ditolak.');
    }
}