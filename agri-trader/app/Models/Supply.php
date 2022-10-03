<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'supply_for',
        'supply_name',
        'supply_type',
        'supply_description',
        'supply_unit',
        'supply_reorderLevel',
        'supply_initialPrice',
    ];

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }
    public function produce(){
        return $this->belongsTo(Produce::class);
    }
    public function supply_purchase_order(){
        return $this->hasMany(SupplyPurchaseOrder::class);
    }
    public function receiving_report(){
        return $this->hasMany(ReceivingReport::class);
    }
    public function supply_order_return(){
        return $this->hasMany(SupplyOrderReturn::class);
    }
    public function supply_order_refund(){
        return $this->hasMany(SupplyOrderRefund::class);
    }
    public function stock_in(){
        return $this->hasMany(StockIn::class);
    }
    public function supply_inventory(){
        return $this->hasOne(SupplyInventory::class);
    }

}
