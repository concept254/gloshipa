<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Shipment extends Model
{
    use HasFactory;

    /** status value => label, in the order a shipment normally moves through them */
    public const STATUSES = [
        'pending' => 'Pending',
        'in_transit' => 'In transit',
        'at_port' => 'At port',
        'customs' => 'In customs',
        'out_for_delivery' => 'Out for delivery',
        'delivered' => 'Delivered',
        'delayed' => 'Delayed',
    ];

    protected $fillable = [
        'tracking_id',
        'container_number',
        'user_id',
        'origin',
        'destination',
        'current_location',
        'cargo_description',
        'weight_kg',
        'status',
        'eta',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'eta' => 'date',
            'delivered_at' => 'datetime',
            'weight_kg' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(ShipmentEvent::class)->orderByDesc('occurred_at')->orderByDesc('id');
    }

    /** Everything that has not been delivered yet. */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', '!=', 'delivered');
    }

    /** Match a tracking ID or a container number, ignoring case and spaces/dashes. */
    public function scopeMatchingReference(Builder $query, string $reference): Builder
    {
        $clean = static::normaliseReference($reference);

        return $query->where(function (Builder $q) use ($clean) {
            $q->where('tracking_id', $clean)->orWhere('container_number', $clean);
        });
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? Str::headline($this->status);
    }

    public static function statusLabelFor(?string $status): string
    {
        return self::STATUSES[$status] ?? Str::headline((string) $status);
    }

    public static function normaliseReference(string $value): string
    {
        return strtoupper(preg_replace('/[\s\-]+/', '', $value));
    }

    public static function newTrackingId(): string
    {
        do {
            $id = 'GS' . strtoupper(Str::random(10));
        } while (static::where('tracking_id', $id)->exists());

        return $id;
    }
}
