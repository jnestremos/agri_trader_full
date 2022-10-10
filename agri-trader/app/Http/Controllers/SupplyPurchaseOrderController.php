<?php

namespace App\Http\Controllers;

use App\Models\Produce;
use App\Models\Supplier;
use App\Models\Supply;
use App\Models\SupplyOrderPayment;
use App\Models\SupplyOrderReturn;
use App\Models\SupplyPurchaseOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SupplyPurchaseOrderController extends Controller
{
    public function formForAddPO(){        
        $uuid = null;        
        while(true){
            $uuid = mt_rand(000000, 999999);
            // $uuid = Str::uuid()->toString();
            if(!SupplyPurchaseOrder::where('purchaseOrder_num', 'PO-'.$uuid)->first()
            || !SupplyOrderReturn::where('returnOrder_num', 'PO-'.$uuid)->first()){
                break;
            }
        }        
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
            'uuid' => $uuid,
            'suppliers' => $suppliers,
            'supplies' => $supplies,
            'produces' => Produce::groupBy('prod_type')->get()
        ]);
    }

    public function addPO(Request $request){
        $purchaseOrder = $request->validate([
            // 'supplier_id' => 'required',
            'supply_id' => 'required|array',
            'purchaseOrder_num' => 'required',
            'purchaseOrder_status' => 'required',
            'purchaseOrder_qty' => 'required|array',
            'purchaseOrder_unit' => 'required|array',
            'purchaseOrder_subTotal' => 'required|array',
            'purchaseOrder_totalBalance' => 'required|numeric',
            // 'purchaseOrder_paymentMethod' => 'required',
            // 'purchaseOrder_bankName' => [Rule::requiredIf(fn () => $request->purchaseOrder_paymentMethod == 'Bank' || $request->purchaseOrder_paymentMethod == 'Others')],
            // 'purchaseOrder_accNum' => [Rule::requiredIf(fn () => $request->purchaseOrder_paymentMethod == 'Bank' || $request->purchaseOrder_paymentMethod == 'Others')],
            // 'purchaseOrder_accName' => 'nullable',
            'purchaseOrder_dpAmount' => 'required|numeric|gt:0',
            'purchaseOrder_percentage' => 'required|numeric',
            'purchaseOrder_balance' => 'required|numeric',
            'purchaseOrder_paymentType' => 'required',
            'purchaseOrder_images' => 'required',
        ]);

        if(!$purchaseOrder){
            return response([
                'error' => "Error adding Purchase Order!"
            ], 400);
        }
        $fileNames = [];
        if($request->hasFile('purchaseOrder_images')){
            $images = $request->file('purchaseOrder_images');
            foreach($images as $image){            
                $fileNameWithExt = $image->getClientOriginalName();
                $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
                $extension = $image->getClientOriginalExtension();
                $fileNameToStore = $fileName . '_' . time() . '.' . $extension;
                $image->storeAs('public/proof_of_payments', $fileNameToStore);
                array_push($fileNames, $fileNameToStore);
            }
        }      
      
       
   

        $trader = User::find(auth()->id())->trader()->first();
        $supplyPO = null;
        for($i = 0; $i < count($request->supply_id); $i++){
            $supplier = Supply::find($request->supply_id[$i])->supplier()->first();
            $supplyPO = SupplyPurchaseOrder::create([
                'trader_id' => $trader->id,
                'supplier_id' => $supplier->id,
                'supply_id' => $request->supply_id[$i],
                'purchaseOrder_num' => $request->purchaseOrder_num,
                'purchaseOrder_status' => $request->purchaseOrder_status,
                'purchaseOrder_qty' => $request->purchaseOrder_qty[$i],
                'purchaseOrder_unit' => $request->purchaseOrder_unit[$i],
                'purchaseOrder_subTotal' => $request->purchaseOrder_subTotal[$i],
            ]);
        }
        // $supplier = Supplier::find($request->supplier_id)->supplier_contact_person()->first();
        SupplyOrderPayment::create([
            'purchaseOrder_num' => $supplyPO->purchaseOrder_num,            
            // 'purchaseOrder_paymentMethod' => $request->purchaseOrder_paymentMethod,                                                       
            'purchaseOrder_paymentType' => $request->purchaseOrder_paymentType,                                                       
            // 'purchaseOrder_bankName' => $request->purchaseOrder_bankName,    
            // 'purchaseOrder_accNum' => $request->purchaseOrder_accNum,   
            // 'purchaseOrder_accName' => $request->purchaseOrder_accName ?? $supplier->contact_firstName . ' ' . $supplier->contact_lastName,                  
            'purchaseOrder_dpAmount' => $request->purchaseOrder_dpAmount,                  
            'purchaseOrder_percentage' => $request->purchaseOrder_percentage,                  
            'purchaseOrder_balance' => $request->purchaseOrder_balance, 
            'purchaseOrder_totalBalance' => $request->purchaseOrder_totalBalance, 
            'purchaseOrder_images' => $fileNames, 
        ]);
    

        return response([
            'message' => 'Purchase Order added successfully!'  
        ]);        
    }
    public function setDashboard(){
        $trader = User::find(auth()->id())->trader()->first();
        $purchaseOrders = SupplyPurchaseOrder::where('trader_id', $trader->id)->get();
        $purchaseOrders_filtered = SupplyPurchaseOrder::select(DB::raw('supplier_id, purchaseOrder_num, count(supply_id) as qty, purchaseOrder_status'))->where('trader_id', $trader->id)
        ->groupBy('purchaseOrder_num');
        $purchaseOrder_accs = [];
        $suppliers = [];
        $supplies = [];
        foreach($purchaseOrders as $purchaseOrder){
            if(!in_array($purchaseOrder->supplier()->first(), $suppliers)){
                array_push($suppliers, $purchaseOrder->supplier()->first());
            }
            if(!in_array($purchaseOrder->supply()->first(), $supplies)){
                array_push($supplies, $purchaseOrder->supply()->first());
            }
            $purchaseOrder_acc = SupplyOrderPayment::where('purchaseOrder_num', $purchaseOrder->purchaseOrder_num)->latest()->first();
            if(!in_array($purchaseOrder_acc, $purchaseOrder_accs)){
                array_push($purchaseOrder_accs, $purchaseOrder_acc);
            }
        }
       
        if(count($purchaseOrders_filtered->get()) > 6){
            return response([
                'purchaseOrders' => $purchaseOrders,
                'suppliers' => $suppliers,
                'supplies' => $supplies,
                'purchaseOrders_filtered' => $purchaseOrders_filtered->paginate(6),
                'purchaseOrder_accs' => $purchaseOrder_accs,
            ]);
        }
        else{
            return response([
                'purchaseOrders' => $purchaseOrders,
                'suppliers' => $suppliers,
                'supplies' => $supplies,
                'purchaseOrders_filtered' => $purchaseOrders_filtered->get(),
                'purchaseOrder_accs' => $purchaseOrder_accs,
            ]);
        }

    }

    public function updateStatus($id){
        SupplyPurchaseOrder::where('purchaseOrder_num', $id)->update([
            'purchaseOrder_status' => "For Delivery"
        ]);

        return response([
            'message' => 'Purchase Order updated successfully!'
        ]);
    }

    public function updatePayment($id){
        $purchaseOrder = SupplyOrderPayment::where('purchaseOrder_num', $id)->first();
        if($purchaseOrder->purchaseOrder_paymentType != 'Full (Return Order)'){
            SupplyOrderPayment::where('purchaseOrder_num', $id)->update([
                'purchaseOrder_dpAmount' => $purchaseOrder->purchaseOrder_totalBalance,            
                'purchaseOrder_balance' => 0,
            ]);
               
        }
        SupplyPurchaseOrder::where('purchaseOrder_num', $id)->update([
            'purchaseOrder_status' => "Delivered"
        ]);

        return response([
            'message' => 'Payment Successful!'   
        ]);

    }
}
