<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeePayment extends Model
{
    /** @use HasFactory<\Database\Factories\FeePaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'fee_collection_id',
        'student_id',
        'amount_paid',
        'collected_by',
        'payment_date',
        'receipt_number',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount_paid' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function feeCollection(): BelongsTo
    {
        return $this->belongsTo(FeeCollection::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }
}
