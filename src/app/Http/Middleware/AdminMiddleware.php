<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // 🚨 未ログインなら `/admin/login` へリダイレクト
        if (!Auth::check()) {
            return redirect('/admin/login');
        }

        // 🚀 `role = admin` の場合は許可
        if (Auth::user()->role === 'admin') {
            return $next($request);
        }

        // 🚨 一般ユーザーなら `/` へリダイレクト
        return redirect('/');
    }
}
