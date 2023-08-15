<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Items extends Model
{
    use SoftDeletes, Sortable;

    protected $table = 'items';


    protected $fillable = [
        'name', 'active', 'rate', 'description', 'tax_id', 'sku', 'type', 'is_taxable', 'hsn_or_sac',
    ];
    
    public $sortable = [
        'id', 'name', 'product_type','created_at'
    ];

    public function getItemTypeAttribute(){
        if($this->type == 1){
            return "Goods";
        }else{
            return "Service";
        }
    }

    public function getTaxableAttribute(){
        if($this->is_taxable == 0){
            return "Taxable";
        }else{
            return "Non-Taxable";
        }
    }
}
