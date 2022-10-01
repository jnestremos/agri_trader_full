<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplyOrderReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'supply_id',
        'returnOrder_num',
        'purchaseOrder_num',
        'purchaseOrder_qtyDefect',
        'purchaseOrder_unit',
        'returnOrder_status',
        'purchaseOrder_subTotal',
    ];

    public function supply(){
        return $this->belongsTo(Supply::class);
    }
}
