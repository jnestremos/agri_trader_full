<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierContactPerson extends Model
{
    use HasFactory;

    protected $table = 'supplier_contact_people';

    protected $fillable = [
        'supplier_id',
        'contact_firstName',
        'contact_middleName',
        'contact_lastName',
        'contact_suffix',
        'contact_position',
    ];

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }
}
