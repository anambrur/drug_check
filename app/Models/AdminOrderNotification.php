<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AdminOrderNotification extends Model
{
    protected $fillable = [
        'type',
        'title',
        'body',
        'notifiable_type',
        'notifiable_id',
        'link_route',
        'link_params',
        'read_at',
    ];

    protected $casts = [
        'link_params' => 'array',
        'read_at' => 'datetime',
    ];

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    public function markAsRead(): void
    {
        if ($this->read_at === null) {
            $this->forceFill(['read_at' => now()])->save();
        }
    }

    public function listUrl(): string
    {
        return route($this->link_route, $this->link_params ?? []);
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'dot' => 'DOT Testing',
            'non_dot' => 'Non-DOT Testing',
            'consortium' => 'Random Consortium',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }

    public function detailUrl(): ?string
    {
        return match ($this->type) {
            'dot', 'non_dot' => route('admin.orders.applications.show', $this->notifiable_id),
            'consortium' => route('consortium-enrollments.show', $this->notifiable_id),
            default => null,
        };
    }
}
