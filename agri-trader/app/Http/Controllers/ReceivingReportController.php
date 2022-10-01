<?php

namespace App\Http\Controllers;

use App\Models\ReceivingReport;
use App\Models\StockIn;
use App\Models\SupplyOrderReturn;
use App\Models\SupplyPurchaseOrder;
use Illuminate\Http\Request;

class ReceivingReportController extends Controller
{
    public function addReceivingReport(Request $request){
        $report = $request->validate([
            'purchaseOrder_num' => 'required',
            'supply_id' => 'required|array',
            'purchaseOrder_qtyGood' => 'required|array',
            'purchaseOrder_qtyDefect' => 'required|array',
            'purchaseOrder_unit' => 'required|array',
            'purchaseOrder_subTotal' => 'required|array',
        ]);

        if(!$report){
            return response([
                'error' => 'Error adding report'
            ], 400);
        }
        $orders = SupplyPurchaseOrder::where('purchaseOrder_num', $request->purchaseOrder_num)->get();
        for($i = 0; $i < count($orders); $i++){
            if($request->purchaseOrder_qtyGood[$i] + $request->purchaseOrder_qtyDefect[$i] 
            != $orders[$i]->purchaseOrder_qty){
                return response([
                    'error' => 'Invalid Input for Quantity'
                ], 400);
            }
        }
        for($i = 0; $i < count($request->supply_id); $i++){
            ReceivingReport::create([
                'supply_id' => $request->supply_id[$i],
                'purchaseOrder_num' => $request->purchaseOrder_num,
                'purchaseOrder_qtyGood' => $request->purchaseOrder_qtyGood[$i],
                'purchaseOrder_qtyDefect' => $request->purchaseOrder_qtyDefect[$i],
                'purchaseOrder_unit' => $request->purchaseOrder_unit[$i],
                'purchaseOrder_subTotal' => $request->purchaseOrder_subTotal[$i],
            ]);
            if($request->purchaseOrder_qtyGood[$i] > 0){
                StockIn::create([
                    'supply_id' => $request->supply_id[$i],
                    'purchaseOrder_num' => $request->purchaseOrder_num,
                    'supply_qty' => $request->purchaseOrder_qtyGood[$i],
                    'supply_unit' => $request->purchaseOrder_unit[$i],
                ]);
            }
        }
        $return = SupplyOrderReturn::where('returnOrder_num', $request->purchaseOrder_num)->first();
        if(array_sum($request->purchaseOrder_qtyDefect) == 0){
            SupplyPurchaseOrder::where('purchaseOrder_num', $request->purchaseOrder_num)->update([
                'purchaseOrder_status' => 'Confirmed'
            ]);            
            if($return){
                SupplyPurchaseOrder::where('purchaseOrder_num', $return->purchaseOrder_num)->update([
                    'purchaseOrder_status' => 'Confirmed'
                ]);    
            }
        }

        return response([
            'message' => 'Report Added Successfully!'
        ]);
    }    
}
