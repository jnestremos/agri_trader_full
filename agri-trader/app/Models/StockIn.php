<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'supply_id',
        'purchaseOrder_num',
        'supply_qty',
        'supply_unit',
    ];

    public function supply(){
        return $this->belongsTo(Supply::class);
    }
}
