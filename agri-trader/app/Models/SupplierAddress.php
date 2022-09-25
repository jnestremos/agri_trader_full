<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'address_zipcode',
        'address_street',
        'address_town',
        'address_province',
    ];


    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }
}
