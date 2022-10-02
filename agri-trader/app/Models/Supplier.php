<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'trader_id',
        'supplier_name'
    ];

    public function supplier_contact(){
        return $this->hasOne(SupplierContact::class);
    }
    public function trader(){
        return $this->belongsTo(Trader::class);
    }
    public function supplier_address(){
        return $this->hasOne(SupplierAddress::class);
    }
    public function supplier_contact_person(){
        return $this->hasOne(SupplierContactPerson::class);
    }
    public function supply(){
        return $this->hasMany(Supply::class);
    }
    public function supply_purchase_order(){
        return $this->hasMany(SupplyPurchaseOrder::class);
    }
    public function supply_inventory(){
        return $this->hasMany(SupplyInventory::class);
    }
}
