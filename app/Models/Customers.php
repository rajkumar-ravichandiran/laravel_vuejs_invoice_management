<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Customers extends Model
{
    use SoftDeletes, Sortable;

    protected $table = 'customers';

    protected $fillable = [
        'name', 'type', 'active', 'portal', 'company_name', 'payment_terms' ,'currency_id' ,'website' ,'custom_fields', 'billing_address', 'shipping_address', 'language_code', 'notes', 'contact_persons', 'country_code', 'place_of_contact', 'gst_no', 'gst_treatment', 'tax_authority_name', 'tax_exemption_code', 'tax_exemption_id', 'tax_authority_id', 'tax_id', 'is_taxable', 'facebook', 'twitter'
    ];
    
    public $sortable = [
        'id', 'name', 'type', 'created_at'
    ];

    protected $appends = ['currency'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($customer) {
            // Delete associated billing and shipping addresses
            $customer->billingAddress()->delete();
            $customer->shippingAddress()->delete();
        });
    }

    public function invoices()
    {
        return $this->hasMany(Invoices::class);
    }

    public function recurringinvoices()
    {
        return $this->hasMany(RecurringInvoices::class);
    }

    public function estimates()
    {
        return $this->hasMany(Estimates::class);
    }

    public function payments()
    {
        return $this->hasMany(Payments::class);
    }

    public function billingaddress()
    {
        return $this->hasOne(Address::class, 'id', 'billing_address');
    }

    public function shippingaddress()
    {
        return $this->hasOne(Address::class,'id', 'shipping_address');
    }

    public function getCustomerTypeAttribute(){
        if($this->type == 1){
            return "Business";
        }else{
            return "Individual";
        }
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
