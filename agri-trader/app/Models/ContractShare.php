<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractShare extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'contractShare_type',
        'contractShare_amount'
    ];

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }
}
