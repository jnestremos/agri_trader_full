<?php

namespace App\Http\Controllers;

use App\Models\Produce;
use App\Models\SupplyOrderPayment;
use App\Models\SupplyPurchaseOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            'purchaseOrder_totalBalance' => 'required|numeric',
            'purchaseOrder_paymentMethod' => 'required',
            'purchaseOrder_bankName' => [Rule::requiredIf(fn () => $request->purchaseOrder_paymentMethod == 'Bank' || $request->purchaseOrder_paymentMethod == 'Others')],
            'purchaseOrder_accNum' => [Rule::requiredIf(fn () => $request->purchaseOrder_paymentMethod == 'Bank' || $request->purchaseOrder_paymentMethod == 'Others')],
            'purchaseOrder_accName' => 'nullable',
            'purchaseOrder_dpAmount' => 'required|numeric|gt:0',
            'purchaseOrder_percentage' => 'required|numeric',
            'purchaseOrder_balance' => 'required|numeric',
            'purchaseOrder_paymentType' => 'required',
        ]);

        if(!$purchaseOrder){
            return response([
                'error' => "Error adding Purchase Order!"
            ], 400);
        }

        $trader = User::find(auth()->id())->trader()->first();
        $supplyPO = null;
        for($i = 0; $i < count($request->supply_id); $i++){
            $supplyPO = SupplyPurchaseOrder::create([
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

        SupplyOrderPayment::create([
            'purchaseOrder_num' => $supplyPO->purchaseOrder_num,            
            'purchaseOrder_paymentMethod' => $request->purchaseOrder_paymentMethod,                                                       
            'purchaseOrder_paymentType' => $request->purchaseOrder_paymentType,                                                       
            'purchaseOrder_bankName' => $request->purchaseOrder_bankName,    
            'purchaseOrder_accNum' => $request->purchaseOrder_accNum,   
            'purchaseOrder_accName' => $request->purchaseOrder_accName ?? $trader->trader_firstName . ' ' . $trader->trader_lastName,                  
            'purchaseOrder_dpAmount' => $request->purchaseOrder_dpAmount,                  
            'purchaseOrder_percentage' => $request->purchaseOrder_percentage,                  
            'purchaseOrder_balance' => $request->purchaseOrder_balance, 
        ]);
    

        return response([
            'message' => 'Purchase Order added successfully!'  
        ]);

    }
}
