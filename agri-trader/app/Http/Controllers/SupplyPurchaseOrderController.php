<?php

namespace App\Http\Controllers;

use App\Models\Produce;
use App\Models\SupplyPurchaseOrder;
use App\Models\User;
use Illuminate\Http\Request;

class SupplyPurchaseOrderController extends Controller
{
    public function formForAddPO(){
        $trader = User::find(auth()->id())->trader()->first();
        $suppliers = $trader->supplier()->get();
        $supplies = [];
        if(count($suppliers) == 0){
            return response([
                'error' => 'Please add a Supplier to your account!'
            ], 400);
        }
        else{
            foreach($suppliers as $supplier){                            
                foreach($supplier->supply()->get() as $supply){                    
                    array_push($supplies, $supply);
                }                
            }
        }
        if(count($supplies) == 0){
            return response([
                'error' => 'Please add a Supply to your account!'
            ], 400);
        }
        return response([
            'suppliers' => $suppliers,
            'supplies' => $supplies,
            'produces' => Produce::groupBy('prod_type')->get()
        ]);
    }

    public function addPO(Request $request){
        $purchaseOrder = $request->validate([
            'supplier_id' => 'required',
            'supply_id' => 'required|array',
            'purchaseOrder_num' => 'required',
            'purchaseOrder_status' => 'required',
            'purchaseOrder_qty' => 'required|array',
            'purchaseOrder_unit' => 'required|array',
            'purchaseOrder_subTotal' => 'required|array',
        ]);

        if(!$purchaseOrder){
            return response([
                'error' => "Error adding Purchase Order!"
            ], 400);
        }

        $trader = User::find(auth()->id())->trader()->first();

        for($i = 0; $i < count($request->supply_id); $i++){
            SupplyPurchaseOrder::create([
                'trader_id' => $trader->id,
                'supplier_id' => $request->supplier_id,
                'supply_id' => $request->supply_id[$i],
                'purchaseOrder_num' => $request->purchaseOrder_num,
                'purchaseOrder_status' => $request->purchaseOrder_status,
                'purchaseOrder_qty' => $request->purchaseOrder_qty[$i],
                'purchaseOrder_unit' => $request->purchaseOrder_unit[$i],
                'purchaseOrder_subTotal' => $request->purchaseOrder_subTotal[$i],
            ]);
        }

        return response([
            'message' => 'Purchase Order added successfully!'  
        ]);

    }
}
