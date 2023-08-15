<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Address extends Model
{
    use SoftDeletes, Sortable;

    protected $table = 'address';

    protected $fillable = [
        'attention', 'address', 'street2', 'city', 'state', 'country', 'fax', 'phone'
    ];

    public function customer()
    {
        return $this->belongsTo(Customers::class);
    }

    public function estimate()
    {
        return $this->belongsTo(Estimates::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoices::class);
    }

    public function recurringinvoice()
    {
        return $this->belongsTo(RecurringInvoices::class);
    }

}
