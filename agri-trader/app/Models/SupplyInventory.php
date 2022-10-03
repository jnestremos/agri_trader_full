<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplyInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'supply_id',
        'supply_name',
        'supply_type',
        'supply_for',
        'supply_reorderLevel',
        'supply_qty',
    ];

    public function supply(){
        return $this->belongsTo(Supply::class);
    }
    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }
}
