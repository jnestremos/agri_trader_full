<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_name'
    ];

    public function supplier_contact(){
        return $this->hasOne(SupplierContact::class);
    }
    public function supplier_address(){
        return $this->hasOne(SupplierAddress::class);
    }
    public function supplier_contact_person(){
        return $this->hasOne(SupplierContactPerson::class);
    }
}
