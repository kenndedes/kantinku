<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'ref_id',
        'transaction_id',
        'payment_intent_id',
        'channel_code',
        'payment_method',
        'proof_image',
        'admin_note',
        'reviewed_by',
        'reviewed_at',
        'amount',
        'status',
        'payment_status',
        'qris_text',
        'qris_url',
        'provider_ref',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'expires_at'  => 'datetime',
        'reviewed_at' => 'datetime',
        'metadata'    => 'array',
    ];

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
