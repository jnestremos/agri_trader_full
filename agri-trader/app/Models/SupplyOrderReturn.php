<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplyOrderReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_num',
        'supply_id',
        'returnOrder_num',
        'purchaseOrder_num',
        'purchaseOrder_qtyDefect',
        'purchaseOrder_unit',
        'returnOrder_status',
        'purchaseOrder_subTotal',
        'return_remark',
    ];

    public function supply(){
        return $this->belongsTo(Supply::class);
    }
}
