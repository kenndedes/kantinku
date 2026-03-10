<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $stand_id
 * @property string $name
 * @property float $price
 * @property int $stock
 * @property string|null $photo
 * @property bool $is_available
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stand_id',
        'category_id',
        'name',
        'price',
        'stock',
        'photo',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'is_available' => 'boolean',
        ];
    }

    public function stand(): BelongsTo
    {
        return $this->belongsTo(Stand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
