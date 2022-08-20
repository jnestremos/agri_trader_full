<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ProduceInventory;
use App\Models\ProduceTrader;
use App\Models\ProduceYield;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduceYieldController extends Controller
{
    public function add(Request $request)
    {
        $yield = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'produce_trader_id' => 'required|exists:produce_trader,id',
            'produce_yield_qtyHarvested' => 'required',
            'produce_yield_price' => 'required',
            'project_harvestableEnd' => 'required|date',
            'produce_yield_dateHarvestFrom' => 'required|date|after:project_harvestableEnd',
            'produce_yield_dateHarvestTo' => 'required|date|after:produce_yield_dateHarvestFrom'
        ]);
        $contract = Project::find($request->project_id)->contract()->first();
        $inventory = ProduceYield::where('project_id', $request->project_id)->first();
        //$num = $contract->produce_trader()->first()->produce_numOfGrades;

        if (
            !$yield || $contract->produce_trader_id != $request->produce_trader_id || $inventory
            || is_array($request->produce_yield_qtyHarvested)
        ) {
            return response([
                'error' => 'Invalid Harvest Details!'
            ], 400);
        }
        if(is_array($request->produce_yield_qtyHarvested) && is_array($request->produce_yield_price)){
            foreach($request->produce_yield_qtyHarvested as $qty){
                if($qty <= 0){
                    return response([
                        'error' => 'Invalid Quantity Placed!'
                    ], 400);
                }
            }
            foreach($request->produce_yield_price as $price){
                if($price <= 0){
                   return response([
                       'error' => 'Invalid Price Indicated!'
                   ], 400);
               }
            }
            $classes = ['A', 'B', 'C'];
            for ($i = 0; $i < count($request->produce_yield_qtyHarvested); $i++) {
                $yield = ProduceYield::create([
                    'project_id' => $request->project_id,
                    'produce_trader_id' => $request->produce_trader_id,
                    'produce_yield_class' => $classes[$i],
                    'produce_yield_qtyHarvested' => $request->produce_yield_qtyHarvested[$i],
                    'produce_yield_price' => $request->produce_yield_price[$i],
                    'produce_yield_dateHarvestFrom' => $request->produce_yield_dateHarvestFrom,
                    'produce_yield_dateHarvestTo' => $request->produce_yield_dateHarvestTo,
                ]);
                ProduceInventory::create([
                    'produce_yield_id' => $yield->id,
                    'produce_inventory_qtyOnHand' => $yield->produce_yield_qtyHarvested
                ]);
            }            
        }
        else {
            if($request->produce_yield_qtyHarvested <= 0){
                return response([
                    'error' => 'Invalid Quantity Placed!'
                ], 400);
            }
            else if($request->produce_yield_price <= 0){
                return response([
                    'error' => 'Invalid Price Indicated!'
                ], 400); 
            }
            $yield = ProduceYield::create([
                'project_id' => $request->project_id,
                'produce_trader_id' => $request->produce_trader_id,
                'produce_yield_class' => 'N/A',
                'produce_yield_qtyHarvested' => $request->produce_yield_qtyHarvested,
                'produce_yield_price' => $request->produce_yield_price,
                'produce_yield_dateHarvestFrom' => $request->produce_yield_dateHarvestFrom,
                'produce_yield_dateHarvestTo' => $request->produce_yield_dateHarvestTo,
            ]);
            ProduceInventory::create([
                'produce_yield_id' => $yield->id,
                'produce_inventory_qtyOnHand' => $yield->produce_yield_qtyHarvested
            ]);
        }   


        $prodTotalQty = ProduceTrader::find($request->produce_trader_id)->where('trader_id', auth()->id())->first()->prod_totalQty;
        // $prodTotalQty = DB::table('produce_trader')->where([['produce_id', '=', $request->produce_id], ['trader_id', '=', auth()->id()]])->first()->prod_totalQty;

        ProduceTrader::find($request->produce_trader_id)->where('trader_id', auth()->id())->update([
            'prod_totalQty' => $prodTotalQty + $yield->produce_yield_qtyHarvested,
            'prod_lastDateOfHarvest' => $yield->produce_yield_dateHarvestTo
        ]);

        // DB::table('produce_trader')->where([['produce_id', '=', $request->produce_id], ['trader_id', '=', auth()->id()]])->update([
        //     'prod_totalQty' => $prodTotalQty + $yield->produce_yield_qtyHarvested,
        //     'prod_lastDateOfHarvest' => ProduceYield::where('produce_id', $request->produce_id)->orderBy('desc')->first()
        // ]);

        return response([
            'message' => 'Harvest Successful!'
        ], 200);
    }
}
