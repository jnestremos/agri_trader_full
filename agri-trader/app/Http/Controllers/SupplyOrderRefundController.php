<?php

namespace App\Http\Controllers;

use App\Models\SupplyOrderRefund;
use App\Models\SupplyPurchaseOrder;
use Illuminate\Http\Request;

class SupplyOrderRefundController extends Controller
{
    public function addSupplyRefund (Request $request){
        $refund = $request->validate([
            'purchaseOrder_num' => 'required',
            'supply_id' => 'required|array',
            'purchaseOrder_qtyDefect' => 'required|array',
            'purchaseOrder_unit' => 'required|array',
            'purchaseOrder_subTotal' => 'required|array',
        ]);

        if(!$refund){
            return response([
                'error' => "Error adding Refund"
            ], 400);
        }

        for($i = 0; $i < count($request->supply_id); $i++){
            SupplyOrderRefund::create([
                'purchaseOrder_num' => $request->purchaseOrder_num,
                'supply_id' => $request->supply_id[$i],
                'purchaseOrder_qtyDefect' => $request->purchaseOrder_qtyDefect[$i],
                'purchaseOrder_unit' => $request->purchaseOrder_unit[$i],
                'refundOrder_status' => 'Pending',
                'purchaseOrder_subTotal' => $request->purchaseOrder_subTotal[$i],
            ]);
        }
    }

    public function confirmSupplyRefund($id){
        SupplyOrderRefund::where('purchaseOrder_num', $id)->update([
            'refundOrder_status' => 'Confirmed',
        ]);

        SupplyPurchaseOrder::where('purchaseOrder_num', $id)->update([
            'purchaseOrder_status' => 'Confirmed'
        ]);

        return response([
            'message' => 'Refund Confirmed'
        ]);
    }
}
