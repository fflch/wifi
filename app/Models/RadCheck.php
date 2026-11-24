<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabela padrão do FreeRADIUS. O Laravel escreve, o FreeRADIUS só lê.
 * Ver Visitante::sincronizarRadius()
 */
class RadCheck extends Model
{
    protected $table = 'radcheck';

    public $timestamps = false;
}
