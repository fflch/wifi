<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CheckPatrocinador
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Gate::allows('patrocinador')) {
            Auth::logout();
            $request->session()->regenerateToken();
            return redirect('/')->with('alert-danger', 'Você não tem permissão para acessar o sistema.');
        }

        return $next($request);
    }
}
