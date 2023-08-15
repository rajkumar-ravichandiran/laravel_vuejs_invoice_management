<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Payments extends Model
{
    use SoftDeletes, Sortable;

    protected $table = 'payments';

    protected $fillable = [
        'number','payment_mode', 'amount', 'amount_refunded', 'bank_charges', 'date', 'status', 'reference_number', 'description', 'customer_id', 'invoice_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            // Get the last payment for the same invoice
            $lastPayment = static::where('invoice_id', $payment->invoice_id)
            ->orderByDesc('number')
            ->first();
            $nextNumber = $lastPayment ? (int) $lastPayment->number + 1 : 1;
            $payment->number = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoices::class);
    }
}
