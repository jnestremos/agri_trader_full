<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplyOrderRefund extends Model
{
    use HasFactory;

    protected $fillable = [
        'supply_id',
        'purchaseOrder_num',
        'purchaseOrder_qtyDefect',
        'purchaseOrder_unit',
        'refundOrder_status',
        'purchaseOrder_subTotal',
        'refund_remark',
    ];

    public function supply(){
        return $this->belongsTo(Supply::class);
    }
}
