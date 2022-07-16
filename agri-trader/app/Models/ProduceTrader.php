<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduceTrader extends Model
{
    use HasFactory;

    protected $table = 'produce_trader';

    protected $fillable = [
        'trader_id',
        'produce_id',
        'prod_name',
        'prod_totalQty',
        'prod_numOfFarms',
        'prod_lastDateOfHarvest',
        'prod_details',
        'produce_numOfGrades',
        'produce_yield_dateHarvestTo',
        'prod_timeOfHarvest'
    ];
    
    public function produce(){
        return $this->belongsTo(Produce::class);
    }

    public function trader(){
        return $this->belongsTo(Trader::class);
    }

    public function contract(){
        return $this->hasMany(Contract::class);
    }
    public function produce_yield()
    {
        return $this->hasMany(ProduceYield::class);
    }

}
