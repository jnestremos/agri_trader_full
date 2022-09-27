<?php

namespace App\Http\Controllers;

use App\Models\Produce;
use App\Models\Supplier;
use App\Models\Supply;
use App\Models\Trader;
use Illuminate\Http\Request;

class SupplyController extends Controller
{
    public function formForAddSupply(){
        $produces = Produce::groupBy('prod_type')->get();
        $trader = Trader::where('user_id', auth()->id())->first();
        $suppliers = Supplier::where('trader_id', $trader->id)->get();

        if(count($suppliers) == 0){
            return response([
                'error' => 'Please add a Supplier!'
            ], 400);
        }

        return response([
            'produces' => $produces,
            'suppliers' => $suppliers,
        ]);
        
    }

    public function addSupply(Request $request){
        $supply = $request->validate([
            'supplier_id' => 'required',
            'supply_name' => 'required',
            'supply_type' => 'required',
            'supply_for' => 'required',
            'supply_description' => 'required',
            'supply_initialPrice' => 'required|numeric|gt:0',
            'supply_unit' => 'required',
        ]);

        if(!$supply || !Supplier::find($request->supplier_id)){
            return response([
                'error' => 'Error adding Supply!'
            ], 400);
        }

        Supply::create([
           'supplier_id' => $request->supplier_id,
           'supply_name' => $request->supply_name,
           'supply_type' => $request->supply_type,
           'supply_for' => $request->supply_for,
           'supply_description' => $request->supply_description,
           'supply_initialPrice' => $request->supply_initialPrice,
           'supply_unit' => $request->supply_unit,           
        ]);

        return response([
            'message' => 'Supply added successfully!'
        ]);
    }
}
