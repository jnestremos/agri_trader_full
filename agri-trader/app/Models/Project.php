<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'contract_id',
        'project_status_id',
        'project_completionDate',
        'project_commenceDate',
        'project_floweringStart',
        'project_floweringEnd',
        'project_fruitBuddingStart',
        'project_fruitBuddingEnd',
        'project_devFruitStart',
        'project_devFruitEnd',
        'project_harvestableStart',
        'project_harvestableEnd',
    ];

    public function statuses()
    {
        return $this->belongsToMany(ProjectStatus::class, 'project_status_history', 'project_id', 'project_status_id');
    }
    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }
    public function bid_order()
    {
        return $this->hasMany(BidOrder::class);
    }
    public function refund()
    {
        return $this->belongsTo(Refund::class, 'refund_id');
    }
    public function produce_yield()
    {
        return $this->hasMany(ProduceYield::class);
    }
    public function project_image(){
        return $this->hasMany(ProjectImage::class);
    }
    public function sale(){
        return $this->hasMany(Sale::class);
    }
    public function stock_out(){
        return $this->hasMany(StockOut::class);
    }
}
