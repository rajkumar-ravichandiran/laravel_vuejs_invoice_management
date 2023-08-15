<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Estimates extends Model
{
    use SoftDeletes, Sortable;

    protected $table = 'estimates';

    protected $fillable = [
        'number', 'date', 'reference_number', 'gst_no', 'status', 'customer_id', 'currency_id', 'is_invoice','expiry_date', 'discount','discounted_amount' ,'is_discount_before_tax', 'discount_type', 'line_items', 'shipping_charge', 'adjustment', 'adjustment_description', 'net','sub_total', 'total', 'tax_total', 'billing_address', 'shipping_address', 'notes', 'terms'
    ];
    public $sortable = [
        'id', 'number', 'created_at', 'date', 'reference_number'
    ];

    protected $appends = ['currency'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            $lastInvoice = static::orderByDesc('number')->first();
            $nextNumber = $lastInvoice ? (int) $lastInvoice->number + 1 : 1;
            $invoice->number = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        });
    }
    

    public function customer()
    {
        return $this->belongsTo(Customers::class);
    }

    public function billingaddress()
    {
        return $this->hasOne(Address::class, 'id', 'billing_address');
    }

    public function shippingaddress()
    {
        return $this->hasOne(Address::class,'id', 'shipping_address');
    }

    public function getCurrencyAttribute(){
        if($this->currency_id == '1'){
            return "AED";
        }elseif($this->currency_id == '2'){
            return "AUD";
        }elseif($this->currency_id == '3'){
            return "CAD";
        }elseif($this->currency_id == '4'){
            return "CNY";
        }elseif($this->currency_id == '5'){
            return "EUR";
        }elseif($this->currency_id == '6'){
            return "GBP";
        }elseif($this->currency_id == '7'){
            return "INR";
        }elseif($this->currency_id == '8'){
            return "JPY";
        }else{
            return "INR";
        }
    }
}
