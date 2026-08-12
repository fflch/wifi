<?php declare(strict_types=1);

namespace App\Models;

use App\Enums\WifiRequestStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WifiRequest extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUlids;

    protected $fillable = [
        'visitor_id',
        'reason',
        'status',
        'approved_by',
        'rejected_by',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => WifiRequestStatus::class,
            'expires_at' => 'datetime',
        ];
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function getBadgeClassAttribute(): string
    {
        return match ($this->status) {
            WifiRequestStatus::APPROVED => 'badge-success',
            WifiRequestStatus::REJECTED => 'badge-danger',
            WifiRequestStatus::EXPIRED => 'badge-secondary',
            default => 'badge-warning',
        };
    }
}
