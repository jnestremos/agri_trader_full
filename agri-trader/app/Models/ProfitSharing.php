<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfitSharing extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'farm_id',
        'farm_owner_id',
        'produce_id',
        'ar_totalSales',
        'ar_totalExpenses',
        'ar_ownerShare',
        'ar_ownerShareType',
        'ar_ownerPercentage',
        'ar_profit',
        'ar_paymentMethod',
        'ar_accName',
        'ar_accNum',
        'ar_bankName',
        'ar_datePaid',
        'ar_approvedOn',
        'ar_remark',
    ];

    public function project(){
        return $this->belongsTo(Project::class);
    }
    public function farm(){
        return $this->belongsTo(Farm::class);
    }
    public function farm_owner(){
        return $this->belongsTo(FarmOwner::class);
    }
    public function produce(){
        return $this->belongsTo(Produce::class);
    }
}
