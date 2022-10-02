<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivingReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'supply_id',
        'purchaseOrder_num',
        'purchaseOrder_qtyGood',
        'purchaseOrder_qtyDefect',
        'purchaseOrder_unit',
        'purchaseOrder_subTotal',
        'report_remark'
    ];

    public function supply(){
        return $this->belongsTo(Supply::class);
    }
}
