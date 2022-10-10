<?php

namespace App\Http\Controllers;

use App\Models\SupplyOrderRefund;
use App\Models\SupplyOrderReturn;
use App\Models\SupplyPurchaseOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplyOrderRefundController extends Controller
{
    public function addSupplyRefund (Request $request){
        $refund = $request->validate([
            'purchaseOrder_num' => 'required',
            'supply_id' => 'required|array',
            'purchaseOrder_qtyDefect' => 'required|array',
            'purchaseOrder_unit' => 'required|array',
            'purchaseOrder_subTotal' => 'required|array',
            'refund_remark' => 'required'
        ]);

        if(!$refund){
            return response([
                'error' => "Error adding Refund"
            ], 400);
        }
        $return = SupplyOrderReturn::where('returnOrder_num', $request->purchaseOrder_num)->first();
        for($i = 0; $i < count($request->supply_id); $i++){  
            if($return){
                SupplyOrderRefund::create([
                    'purchaseOrder_num' => $return->purchaseOrder_num,
                    'supply_id' => $request->supply_id[$i],
                    'purchaseOrder_qtyDefect' => $request->purchaseOrder_qtyDefect[$i],
                    'purchaseOrder_unit' => $request->purchaseOrder_unit[$i],
                    'refundOrder_status' => 'Pending',
                    'purchaseOrder_subTotal' => $request->purchaseOrder_subTotal[$i] * $request->purchaseOrder_qtyDefect[$i],
                    'refund_remark' => $request->refund_remark
                ]);
            } 
            else{
                SupplyOrderRefund::create([
                    'purchaseOrder_num' => $request->purchaseOrder_num,
                    'supply_id' => $request->supply_id[$i],
                    'purchaseOrder_qtyDefect' => $request->purchaseOrder_qtyDefect[$i],
                    'purchaseOrder_unit' => $request->purchaseOrder_unit[$i],
                    'refundOrder_status' => 'Pending',
                    'purchaseOrder_subTotal' => $request->purchaseOrder_subTotal[$i] * $request->purchaseOrder_qtyDefect[$i],
                    'refund_remark' => $request->refund_remark
                ]);
            }         

        }
    }

    public function getSupplyRefunds(){
        $user = User::find(auth()->id())->trader()->first();
        $orders = SupplyPurchaseOrder::where('trader_id', $user->id)->get();
        $purchaseOrder_refunds = [];
        $supplies = [];
        $suppliers = [];
        foreach($orders as $order){
            $refunds = SupplyOrderRefund::where('purchaseOrder_num', $order->purchaseOrder_num)->get();           
            foreach($refunds as $refund){
                if(!in_array($refund, $purchaseOrder_refunds)){
                    array_push($purchaseOrder_refunds, $refund);
                }                
                if(!in_array($refund->supply()->first()->supplier()->first(), $suppliers)){
                    array_push($suppliers, $refund->supply()->first()->supplier()->first());
                }
                if(!in_array($refund->supply()->first(), $supplies)){
                    array_push($supplies, $refund->supply()->first());
                }
            }
        }
        $ids = [];
        foreach($purchaseOrder_refunds as $refund){
            array_push($ids, $refund->id);
        }
        $refunds_filtered = SupplyOrderRefund::select(
            DB::raw('purchaseOrder_num, sum(purchaseOrder_qtyDefect) as 
            purchaseOrder_qtyDefect, sum(purchaseOrder_subTotal) as purchaseOrder_subTotal,
            refundOrder_status, created_at, updated_at'))
            ->whereIn('id', $ids)->groupBy('purchaseOrder_num')->get();
        return response([
            'purchaseOrder_refunds' => $purchaseOrder_refunds,
            'refunds_filtered' => $refunds_filtered,
            'supplies' => $supplies,
            'suppliers' => $suppliers,
        ]);
    }

    public function confirmSupplyRefund($id){
        SupplyOrderRefund::where('purchaseOrder_num', $id)->update([
            'refundOrder_status' => 'Confirmed',
        ]);

        $returns = SupplyOrderReturn::where('purchaseOrder_num', $id)->get();
        foreach($returns as $return){
            SupplyPurchaseOrder::where('purchaseOrder_num', $return->returnOrder_num)->update([
                'purchaseOrder_status' => 'Confirmed'
            ]);
        }

        SupplyPurchaseOrder::where('purchaseOrder_num', $id)->update([
            'purchaseOrder_status' => 'Confirmed'
        ]);

        return response([
            'message' => 'Refund Confirmed'
        ]);
    }
}
