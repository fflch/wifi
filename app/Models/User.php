<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Uspdev\SenhaunicaSocialite\Traits\HasSenhaunica;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasSenhaunica;
    use HasRoles;

    protected $fillable = [
        'name',
        'email',
        'codpes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Solicitações que este autorizador aprovou ou rejeitou
     */
    public function solicitacoes()
    {
        return $this->hasMany(Solicitacao::class);
    }
}
