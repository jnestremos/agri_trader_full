<?php

namespace App\Http\Controllers;

use App\Models\StockOut;
use App\Models\Supply;
use App\Models\SupplyInventory;
use Illuminate\Http\Request;

class StockOutController extends Controller
{
    public function addStockOut(Request $request, $id){
        $stock = $request->validate([          
            'supply_id' => 'required|array',
            'supply_qty' => 'required|array',       
            'stockOut_remark' => 'required',
        ]);

        if(!$stock){
            return response([
                'error' => "Error adding supplies to project!"
            ], 400);
        }
        for($i = 0; $i < count($request->supply_id); $i++){
            $supplyy = Supply::find($request->supply_id[$i]);
            $supplier = $supplyy->supplier()->first();
            StockOut::create([
                'project_id' => $id,
                'supplier_id' => $supplier->id,
                'supply_id' => $request->supply_id[$i],
                'supply_qty' => $request->supply_qty[$i],
                'supply_unit' => $supplyy->supply_unit,
                'stockOut_remark' => $request->stockOut_remark
            ]);
            $inventory = SupplyInventory::where('supply_id', $request->supply_id[$i])->first();
            SupplyInventory::where('supply_id', $request->supply_id[$i])->update([
                'supply_qty' => $inventory->supply_qty - $request->supply_qty[$i]
            ]);
        }
        return response([
            'message' => 'Supplies now added to you project!'
        ]);
    }
}
