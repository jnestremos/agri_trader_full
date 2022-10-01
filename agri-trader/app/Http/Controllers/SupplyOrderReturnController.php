<?php

namespace App\Http\Controllers;

use App\Models\Supply;
use App\Models\SupplyOrderPayment;
use App\Models\SupplyOrderReturn;
use App\Models\SupplyPurchaseOrder;
use Illuminate\Http\Request;

class SupplyOrderReturnController extends Controller
{
    public function addSupplyReturn (Request $request){
        $return = $request->validate([
            'purchaseOrder_num' => 'required',
            'supply_id' => 'required|array',
            'purchaseOrder_qtyDefect' => 'required|array',
            'purchaseOrder_unit' => 'required|array',
            'purchaseOrder_subTotal' => 'required|array',
        ]);

        if(!$return){
            return response([
                'error' => 'Error adding Return'
            ], 400);
        }
        $uuid = null;        
        while(true){
            $uuid = mt_rand(000000, 999999);
            // $uuid = Str::uuid()->toString();
            if(!SupplyOrderReturn::where('returnOrder_num', 'PO-'.$uuid)->first()
            || !SupplyPurchaseOrder::where('purchaseOrder_num', 'PO-'.$uuid)->first()){
                break;
            }
        }  
        $order_num = null;      
        $return = SupplyOrderReturn::where('returnOrder_num', $request->purchaseOrder_num)->first();  
        if($return){
            $order_num = $return->purchaseOrder_num;
        }
        else{
            $order_num = $request->purchaseOrder_num;
        }
        for($i = 0; $i < count($request->supply_id); $i++){            
            SupplyOrderReturn::create([
                'supply_id' => $request->supply_id[$i],
                'returnOrder_num' => 'PO-'.$uuid,
                'purchaseOrder_num' => $order_num,
                'purchaseOrder_qtyDefect' => $request->purchaseOrder_qtyDefect[$i],
                'purchaseOrder_unit' => $request->purchaseOrder_unit[$i],
                'returnOrder_status' => 'Pending',
                'purchaseOrder_subTotal' => $request->purchaseOrder_subTotal[$i],
            ]);
            SupplyPurchaseOrder::create([
                'supplier_id' => Supply::find($request->supply_id[$i])->supplier()->first()->id,
                'supply_id' => $request->supply_id[$i],
                'purchaseOrder_num' => 'PO-'.$uuid,
                'purchaseOrder_status' => 'Pending',
                'purchaseOrder_qty' => $request->purchaseOrder_qtyDefect[$i],
                'purchaseOrder_unit' => $request->purchaseOrder_unit[$i],
                'purchaseOrder_subTotal' => $request->purchaseOrder_subTotal[$i],
            ]);
        }
        $payment = SupplyOrderPayment::where('purchaseOrder_num', $order_num)->first();
        SupplyOrderPayment::create([
            'purchaseOrder_num' => 'PO-'.$uuid,
            'purchaseOrder_paymentMethod' => $payment->purchaseOrder_paymentMethod,
            'purchaseOrder_paymentType' => 'Full (Return Order)',
            'purchaseOrder_bankName' => $payment->purchaseOrder_bankName,
            'purchaseOrder_accNum' => $payment->purchaseOrder_accNum,
            'purchaseOrder_accName' => $payment->purchaseOrder_accName,
            'purchaseOrder_dpAmount' => 0,
            'purchaseOrder_percentage' => 0,
            'purchaseOrder_balance' => 0,
            'purchaseOrder_totalBalance' => array_sum($request->purchaseOrder_subTotal)
        ]);
        SupplyPurchaseOrder::where('purchaseOrder_num', $order_num)->update([
            'purchaseOrder_status' => 'Pending for PO-'.$uuid
        ]);
        
        return response([
            'message' => 'Supply Return Successful'
        ]);
    }
}
