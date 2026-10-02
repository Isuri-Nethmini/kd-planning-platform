<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Proof of an offline payment, uploaded by the client.
 *
 * Financial documents, so they live on the private disk: no URL reaches the
 * file directly. They are served by ProtectedFileController, which checks the
 * person asking is the owning client or an admin.
 */
class PaymentReceipt extends Model
{
    protected $fillable = [
        'payment_instalment_id', 'file_path', 'original_name', 'amount_paid', 'paid_on',
        'reference', 'status', 'reviewed_by', 'reviewed_at', 'rejection_reason',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'paid_on'     => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function instalment(): BelongsTo
    {
        return $this->belongsTo(PaymentInstalment::class, 'payment_instalment_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'reviewed_by');
    }

    public function getIsImageAttribute(): bool
    {
        return in_array(strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true);
    }
}
