<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplyPurchaseOrder extends Model
{
    use HasFactory;


    protected $fillable = [
        'supplier_id',
        'supply_id',
        'trader_id',
        'purchaseOrder_num',
        'purchaseOrder_status',
        'purchaseOrder_qty',
        'purchaseOrder_unit',
        'purchaseOrder_subTotal',
    ];

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }
    public function supply(){
        return $this->belongsTo(Supply::class);
    }
    public function trader(){
        return $this->belongsTo(Trader::class);
    }
}
