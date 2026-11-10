<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session('admin_authenticated')) {
            return redirect()->route('admin.login');
        }
        // 運営者のブラウザとして記録(閲覧数などの計測から除外)
        \App\Support\InternalVisitor::mark();
        return $next($request);
    }
}
