<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visitor extends Model
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes; // Garante a geração automática do ULID no campo 'id'

    protected $fillable = [
        'name',
        'document',
        'email',
        'phone',
        'client_mac',
    ];

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', (string) $this->name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1).substr($words[count($words) - 1], 0, 1));
        }

        return strtoupper(substr((string) $this->name, 0, 2));
    }

    public function getFormattedDocumentAttribute(): string
    {
        $clean = preg_replace('/[^a-zA-Z0-9]/', '', (string) $this->document);
        if (strlen($clean) === 11) {
            return substr($clean, 0, 3).'.***.***-'.substr($clean, -2);
        }

        return $clean;
    }

    /**
     * Relacionamento: Um visitante pode fazer várias solicitações de Wi-Fi ao longo do tempo.
     */
    public function wifiRequests(): HasMany
    {
        return $this->hasMany(WifiRequest::class);
    }
}
