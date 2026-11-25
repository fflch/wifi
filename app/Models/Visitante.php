<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Visitante extends Model implements Auditable
{
    use HasFactory;
    use HasUlids;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['mac'];

    public function solicitacoes()
    {
        return $this->hasMany(Solicitacao::class);
    }

    /**
     * Converte qualquer formato de MAC para aa:bb:cc:dd:ee:ff
     * Ex.: AA-BB-CC-DD-EE-FF, aabb.ccdd.eeff, AABBCCDDEEFF
     */
    public static function normalizaMac($mac)
    {
        $hex = strtolower(preg_replace('/[^0-9A-Fa-f]/', '', (string) $mac));

        return implode(':', str_split($hex, 2));
    }

    protected function mac(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => self::normalizaMac($value)
        );
    }

    /**
     * MAC no formato usado como username na radcheck: aabbccddeeff
     */
    protected function macRadius(): Attribute
    {
        return Attribute::make(
            get: fn () => str_replace(':', '', $this->mac)
        );
    }

    /**
     * Mantém a tabela radcheck do FreeRADIUS em dia:
     * se o visitante tem alguma solicitação aprovada, o MAC entra; senão, sai.
     */
    public function sincronizarRadius()
    {
        RadCheck::where('username', $this->mac_radius)->delete();

        if ($this->solicitacoes()->currentStatus('aprovado')->exists()) {
            $radcheck = new RadCheck;
            $radcheck->username = $this->mac_radius;
            $radcheck->attribute = 'Auth-Type';
            $radcheck->op = ':=';
            $radcheck->value = 'Accept';
            $radcheck->save();
        }
    }
}
