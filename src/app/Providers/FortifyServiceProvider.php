<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse;
use Illuminate\Support\Facades\URL;
use Laravel\Fortify\Contracts\RegisterResponse;
class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::registerView(function () {
            return view('auth.register');
        });
        $this->app->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    $features = config('fortify.features', []);

                    if (
                        in_array(Features::emailVerification(), $features)
                        && !$request->user()->hasVerifiedEmail()
                    ) {
                        return redirect()->route('verification.notice');
                    }

                    return redirect('/attendance');
                }
            };
        });

        // 🚀 ログイン画面の分岐
        Fortify::loginView(function () {
            return request()->is('admin/*') ? view('auth.admin-login') : view('auth.login');
        });
        
        // 🚀 認証処理のカスタマイズ（リダイレクト処理は削除済み）
        Fortify::authenticateUsing(function (Request $request) {
            return Auth::attempt([
                'email' => $request->email,
                'password' => $request->password
            ]) ? Auth::user() : null;
        });

        // 🚀 Fortify のログイン後のリダイレクト処理をカスタマイズ
        $this->app->singleton(
            LoginResponse::class,
            function () {
                return new class implements LoginResponse {
                    public function toResponse($request)
                    {
                        $user = Auth::user();
                        $previousUrl = URL::previous(); // ログインページの URL を取得

                        // 🚀 `/admin/login` からログインした管理者のみ `/admin/attendance/list` へリダイレクト
                        if ($user->role === 'admin' && str_contains($previousUrl, '/admin/login')) {
                            return redirect('/admin/attendance/list');
                        }

                        // 🚀 一般ユーザーは `/attendance` にリダイレクト
                        return redirect('/attendance');
                    }
                };
            }
        );

        // 🚀 ログイン試行回数の制限
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
            return Limit::perMinute(10)->by($email . $request->ip());
        });
    }
}
