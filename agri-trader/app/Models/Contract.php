<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'trader_id',
        'farm_id',
        'contract_share_id',
        // 'produce_trader_id',
        'project_status_id',
        'produce_id',
        'farm_name',
        'contract_estimatedHarvest',
        'contract_estimatedPrice',
        'contract_estimatedSales',
        'contract_ownerShare',
        'contract_traderShare',
    ];
    public function project()
    {
        return $this->hasOne(Project::class);
    }
    public function trader()
    {
        return $this->belongsTo(Trader::class);
    }
    public function farm()
    {
        return $this->belongsTo(Farm::class, 'farm_id');
    }
    public function produce()
    {
        return $this->belongsTo(Produce::class, 'produce_id');
    }
    public function contract_share()
    {
        return $this->belongsTo(ContractShare::class);
    }
    // public function produce_trader(){
    //     return $this->belongsTo(ProduceTrader::class);
    // }
    
}
