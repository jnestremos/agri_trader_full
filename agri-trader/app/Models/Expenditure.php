<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expenditure extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'exp_ORNum',
        'exp_type',
        'exp_dateFrom',
        'exp_dateTo',
        'exp_paymentType',
        'exp_accNum',
        'exp_bankName',
        'exp_accName',
        'exp_amount',
        'exp_remark',
    ];

    public function project(){
        return $this->belongsTo(Project::class);
    }
}
