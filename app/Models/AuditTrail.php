<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

class AuditTrail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action_name',
        'important_description',
        'username',
        'action_date',
        'action_time',
        'route_name',
        'http_method',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'action_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(
        string $actionName,
        string $importantDescription,
        ?User $user = null,
        array $context = []
    ): self {
        $actor = $user ?? auth()->user();
        $now = now();

        return static::create(array_merge([
            'user_id' => $actor?->id,
            'action_name' => $actionName,
            'important_description' => $importantDescription,
            'username' => $actor?->name ?? 'Guest',
            'action_date' => $now->toDateString(),
            'action_time' => $now->format('H:i:s'),
        ], Arr::only($context, [
            'route_name',
            'http_method',
            'ip_address',
        ])));
    }
}
