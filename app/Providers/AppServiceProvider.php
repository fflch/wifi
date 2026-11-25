<?php

namespace App\Providers;

use App\Models\Solicitacao;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use OwenIt\Auditing\Events\AuditCustom;
use Spatie\ModelStatus\Events\StatusUpdated;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (\App::environment('production')) {
            \URL::forceScheme('https');
        }

        // Atrás do Traefik: confia no proxy para obter o IP real do cliente (TRUSTED_PROXIES)
        if (config('wifi.proxies_confiaveis')) {
            TrustProxies::at(config('wifi.proxies_confiaveis'));
        }

        // Quem pode aprovar: servidores e docentes da unidade (gates do senhaunica-socialite)
        Gate::define('autorizador', function ($user) {
            return Gate::forUser($user)->any(['senhaunica.servidor', 'senhaunica.docente']);
        });

        // Quem pode rejeitar:
        //  - pendente: qualquer autorizador
        //  - aprovada: o admin ou o servidor que assumiu a solicitação (quem aprovou)
        Gate::define('rejeitar', function ($user, Solicitacao $solicitacao) {
            if ($solicitacao->status == 'pendente') {
                return Gate::forUser($user)->allows('autorizador');
            }
            if ($solicitacao->status == 'aprovado') {
                return Gate::forUser($user)->allows('admin') || $solicitacao->user_id == $user->id;
            }

            return false;
        });

        // Toda mudança de status (pendente/aprovado/rejeitado):
        //  1. atualiza a radcheck do FreeRADIUS
        //  2. fica registrada na auditoria, com o usuário que fez a mudança
        Event::listen(StatusUpdated::class, function (StatusUpdated $event) {
            $solicitacao = $event->model;
            $solicitacao->visitante->sincronizarRadius();

            $solicitacao->auditEvent = 'status';
            $solicitacao->isCustomEvent = true;
            $solicitacao->auditCustomOld = ['status' => $event->oldStatus?->name];
            $solicitacao->auditCustomNew = ['status' => $event->newStatus->name, 'observacao' => $event->newStatus->reason];
            Event::dispatch(new AuditCustom($solicitacao));
        });
    }
}
