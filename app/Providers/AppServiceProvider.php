<?php

namespace App\Providers;

use App\Models\WifiRequest;
use App\Observers\WifiRequestObserver;
use App\Policies\WifiRequestPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('solicitacao-wifi', function (Request $request) {
            if (config('app.debug')) {
                return Limit::perMinute(1000)->by($request->ip());
            }

            return Limit::perHour(3)->by($request->ip())
                ->response(function () {
                    return response()->view('errors.429', [
                        'message' => 'Você excedeu o limite de solicitações. Tente novamente em uma hora.'
                    ], 429);
                });
        });

        Gate::policy(WifiRequest::class, WifiRequestPolicy::class);
        WifiRequest::observe(WifiRequestObserver::class);

        Gate::define('patrocinador', function ($user) {
            $admins = config('senhaunica.admins', []);
            return in_array((string) $user->codpes, $admins, strict: true);
        });

        Gate::define('visitante', function ($user = null) {
            return $user === null;
        });
    }
}
