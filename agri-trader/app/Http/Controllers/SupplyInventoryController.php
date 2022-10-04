<?php

namespace App\Http\Controllers;

use App\Models\Produce;
use App\Models\Supplier;
use App\Models\SupplyInventory;
use App\Models\User;
use Illuminate\Http\Request;

class SupplyInventoryController extends Controller
{
    public function getSupplyInventory(){
        $user = User::find(auth()->id())->trader()->first();
        $suppliers = Supplier::where('trader_id', $user->id)->get();
        $inventory = [];
        $container = []; //for suppliers
        $supplies = [];
        $produces = Produce::groupBy('prod_type')->get();
        foreach($suppliers as $supplier){
            $supply_inventory = SupplyInventory::where('supplier_id', $supplier->id)->get();
            foreach($supply_inventory as $supply){
                if(!in_array($supply->supplier()->first(), $container)){
                    array_push($container, $supply->supplier()->first());
                }
                if(!in_array($supply->supply()->first(), $supplies)){
                    array_push($supplies, $supply->supply()->first());
                }
                array_push($inventory, $supply);
            }
        }  
        $suppliers = $container;

        return response([
            'inventory' => $inventory,
            'suppliers' => $suppliers,
            'supplies' => $supplies,
            'produces' => $produces,
        ]);
    }
}
