<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $stand_id
 * @property string $status
 * @property string|null $stand_name
 * @property string|null $phone
 * @property array|null $documents
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $rejected_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class SellerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stand_id',
        'status',
        'stand_name',
        'phone',
        'documents',
        'approved_at',
        'rejected_at',
    ];

    protected function casts(): array
    {
        return [
            'documents' => 'array',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
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
}
