<?php

namespace App\Http\Controllers;

use App\Models\ReceivingReport;
use App\Models\StockIn;
use App\Models\Supply;
use App\Models\SupplyInventory;
use App\Models\SupplyOrderRefund;
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
            'report_remark' => 'required'
        ]);

        if(!$report){
            return response([
                'error' => 'Error adding report'
            ], 400);
        }
        $return = SupplyOrderReturn::where('returnOrder_num', $request->purchaseOrder_num)->first();
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
                'report_remark' => $request->report_remark,
            ]);
            if($request->purchaseOrder_qtyGood[$i] > 0){
                StockIn::create([
                    'supply_id' => $request->supply_id[$i],
                    'purchaseOrder_num' => $return->purchaseOrder_num,
                    'supply_qty' => $request->purchaseOrder_qtyGood[$i],
                    'supply_unit' => $request->purchaseOrder_unit[$i],
                ]);
                if(SupplyInventory::where('supply_id', $request->supply_id[$i])->first()){
                    $supply = SupplyInventory::where('supply_id', $request->supply_id[$i])->first();
                    SupplyInventory::where('supply_id', $request->supply_id[$i])->update([
                        'supply_qty' => $supply->supply_qty + $request->purchaseOrder_qtyGood[$i]
                    ]);
                }
                else{
                    $supply = Supply::find($request->supply_id[$i]);
                    $supplier = $supply->supplier()->first();                    
                    SupplyInventory::create([
                        'supplier_id' => $supplier->id,
                        'supply_id' => $request->supply_id[$i],
                        'supply_name' => $supply->supply_name,
                        'supply_type' => $supply->supply_type,
                        'supply_for' => $supply->supply_for,
                        'supply_reorderLevel' => $supply->supply_reorderLevel,
                        'supply_qty' => $request->purchaseOrder_qtyGood[$i]
                    ]);
                }
            }
        }        
        if(array_sum($request->purchaseOrder_qtyDefect) == 0){
            SupplyPurchaseOrder::where('purchaseOrder_num', $request->purchaseOrder_num)->update([
                'purchaseOrder_status' => 'Confirmed'
            ]);            
            if($return){
                SupplyPurchaseOrder::where('purchaseOrder_num', $return->purchaseOrder_num)->update([
                    'purchaseOrder_status' => 'Confirmed'
                ]);    
                SupplyOrderReturn::where('purchaseOrder_num', $return->purchaseOrder_num)->update([
                    'returnOrder_status' => 'Confirmed'
                ]);
            }
        }

        return response([
            'message' => 'Report Added Successfully!'
        ]);
    }
    
    public function getPOForRR ($id){
        $stockIn = StockIn::where('purchaseOrder_num', $id)->first();
        if($stockIn){
            return response([
                'error' => "Purchase Order already added to Stock In!"
            ], 400);
        }
        else{
            $orders = SupplyPurchaseOrder::where('purchaseOrder_num', $id)->get();
            foreach($orders as $order){
                if($order->purchaseOrder_status != 'Delivered'){
                    return response([
                        'error' => 'Error accessing Initial Stock in'
                    ], 400);
                }
            }
            $supplier = $orders[0]->supplier()->first();
            $supplier_contact = $supplier->supplier_contact()->first();
            $supplier_contact_person = $supplier->supplier_contact_person()->first();
            $supply = $supplier->supply()->get();        
    
            return response([
                'orders' => $orders,
                'supplier' => $supplier,
                'supply' => $supply,
                'supplier_contact' => $supplier_contact,
                'supplier_contact_person' => $supplier_contact_person,
            ]);            
        }        
    }

    public function updateRR($id){
        $orders = ReceivingReport::where('purchaseOrder_num', $id)->where('purchaseOrder_qtyDefect', '>', 0)->get();
        $return = SupplyOrderReturn::where('purchaseOrder_num', $id)->first();
        $refund = SupplyOrderRefund::where('purchaseOrder_num', $id)->first();
        $container = [];
        $supply_ids = [];
        if($return || $refund){
            if($return && !$refund){
                foreach(SupplyOrderRefund::where('purchaseOrder_num', $id)->get() as $order){
                    array_push($supply_ids, $order->supply_id);
                }
                $orders = ReceivingReport::where('purchaseOrder_num', $id)
                ->where('purchaseOrder_qtyDefect', '>', 0)->whereIn('supply_id', $supply_ids)->get();
            }
            else if(!$return && $refund){
                foreach(SupplyOrderReturn::where('purchaseOrder_num', $id)->get() as $order){
                    array_push($supply_ids, $order->supply_id);
                }
                $orders = ReceivingReport::where('purchaseOrder_num', $id)
                ->where('purchaseOrder_qtyDefect', '>', 0)->whereIn('supply_id', $supply_ids)->get();
            }
            else{
                return response([
                    'error' => 'Error accessing Receving Report Summary for Return/Refund'
                ], 400);
            }
        }
        $supply = [];
        foreach($orders as $order){
            array_push($supply, $order->supply()->first());
        }
        $supplier = $supply[0]->supplier()->first();
        $supplier_contact = $supplier->supplier_contact()->first();
        $supplier_contact_person = $supplier->supplier_contact_person()->first();
        return response([
            'orders' => $orders,
            'supplier' => $supplier,
            'supply' => $supply,
            'supplier_contact' => $supplier_contact,
            'supplier_contact_person' => $supplier_contact_person,
        ]);            

    }
}
