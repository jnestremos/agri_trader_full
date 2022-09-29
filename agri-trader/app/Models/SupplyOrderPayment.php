<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplyOrderPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchaseOrder_num',            
        'purchaseOrder_paymentMethod',                                                       
        'purchaseOrder_paymentType',                                                       
        'purchaseOrder_bankName',    
        'purchaseOrder_accNum',   
        'purchaseOrder_accName',                  
        'purchaseOrder_dpAmount',                  
        'purchaseOrder_percentage',                  
        'purchaseOrder_balance',                  
        'purchaseOrder_totalBalance'                  
    ];
}
