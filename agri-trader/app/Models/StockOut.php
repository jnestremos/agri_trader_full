<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOut extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'supplier_id',
        'supply_id',
        'supply_qty',
        'supply_unit',
        'stockOut_remark',
    ];

    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function supply(){
        return $this->belongsTo(Supply::class);
    }
}
