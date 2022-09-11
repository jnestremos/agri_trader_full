<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'trader_id',
        'distributor_id',
        'message_sentBy',
        'message_body'
    ];

    public function traders(){
        return $this->belongsToMany(Trader::class, 'messages', 'distributor_id', 'trader_id');
    }
    public function distributors(){
        return $this->belongsToMany(Distributor::class, 'messages', 'trader_id', 'distributor_id');
    }
    // public function getCreatedAtAttribute($date)
    // {
    //     return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d g:i A');
    // }
    // public function getUpdatedAtAttribute($date)
    // {
    //     return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d g:i A');
    // }
}
