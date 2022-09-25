<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'supplier_phoneNumber',
        'supplier_telNumber',
        'supplier_email'
    ];

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }
}
