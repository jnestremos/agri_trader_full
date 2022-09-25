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
        'supply_initialPrice',
    ];

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }
    public function produce(){
        return $this->belongsTo(Produce::class);
    }
}
