<?php

namespace App\Http\Controllers;

use App\Models\Produce;
use App\Models\Supplier;
use App\Models\Supply;
use App\Models\Trader;
use App\Models\User;
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

    public function getSupplyList(){
        $user = User::find(auth()->id())->trader()->first();
        $suppliers = Supplier::where('trader_id', $user->id)->get();
        $container = [];
        $supplies = [];
        foreach($suppliers as $supplier){
            if(count($supplier->supply()->get()) > 0){
                array_push($container, $supplier);
            }
            foreach($supplier->supply()->get() as $supply){
                array_push($supplies, $supply);
            }
        }
        $suppliers = $container;
        return response([
            'supplies' => $supplies,
            'suppliers' => $suppliers,
        ]);
    }

    public function editSupply(Request $request, $id){
        $supply = $request->validate([
            'supply_initialPrice' => 'required|numeric|gt:0',
            'supply_reorderLevel' => 'required|numeric|gt:0',
        ]);

        if(!$supply){
            return response([
                'error' => "Error updating Supply!"
            ], 400);
        }

        Supply::find($id)->update([
            'supply_initialPrice' => $request->supply_initialPrice,
            'supply_reorderLevel' => $request->supply_reorderLevel,
        ]);

        return response([
            'message' => 'Supply updated successfully'
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

        // $supply = Supply::where('supply_name', $request->supply_name)->first();

        // if($supply){
        //     if($supply->supplier()->first()->trader_id == Trader::where('user_id', auth()->id())->first()->id){
        //         return response([
        //             'error' => 'Supply was already added!'
        //         ], 400);
        //     }
        // }

       
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
