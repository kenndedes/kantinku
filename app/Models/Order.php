<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Stand;
use App\Models\OrderStatusLog;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $stand_id
 * @property string $order_number
 * @property \Illuminate\Support\Carbon $order_date
 * @property string $pickup_time
 * @property string $status
 * @property string $order_status
 * @property string $payment_status
 * @property string|null $pickup_code
 * @property string|null $pickup_qr_payload
 * @property string $payment_method
 * @property float $total_price
 * @property int|null $verified_by
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $ready_at
 * @property \Illuminate\Support\Carbon|null $picked_up_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stand_id',
        'order_number',
        'order_date',
        'pickup_time',
        'status',
        'order_status',
        'payment_status',
        'pickup_code',
        'pickup_qr_payload',
        'payment_method',
        'total_price',
        'completed_at',
        'ready_at',
        'picked_up_at',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'total_price' => 'decimal:2',
            'completed_at' => 'datetime',
            'ready_at' => 'datetime',
            'picked_up_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stand(): BelongsTo
    {
        return $this->belongsTo(Stand::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(OrderStatusLog::class)->latest();
    }

    /**
     * Restore stock for all items in this order back to their menu items.
     * Safe to call multiple times; only acts when order is being cancelled.
     */
    public function restockItems(): void
    {
        foreach ($this->items()->with('menuItem')->get() as $item) {
            if ($item->menuItem) {
                $item->menuItem->increment('stock', $item->quantity);
            }
        }
    }

    /**
     * Log a status transition for audit.
     */
    public function logStatusChange(?string $from, string $to, ?int $changedBy = null, ?string $note = null): void
    {
        $this->statusLogs()->create([
            'from_status' => $from,
            'to_status'   => $to,
            'changed_by'  => $changedBy,
            'note'        => $note,
        ]);
    }
}
