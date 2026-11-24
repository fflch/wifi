<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Permission;

abstract class TestCase extends BaseTestCase
{
    /**
     * Usuário com vínculo Servidor na Senha Única (pode aprovar)
     */
    protected function servidor(): User
    {
        $user = $this->usuario();
        $user->givePermissionTo(Permission::findByName('Servidor', 'senhaunica'));

        return $user;
    }

    /**
     * Usuário logado pela Senha Única, mas sem vínculo Servidor (ex.: aluno)
     */
    protected function usuario(): User
    {
        $user = User::factory()->create();
        $user->criarPermissoesPadrao(); // o senhaunica-socialite faz isso no login

        return $user;
    }
}
