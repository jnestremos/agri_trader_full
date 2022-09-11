<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Produce;
use App\Models\ProduceInventory;
use App\Models\ProduceTrader;
use App\Models\ProduceYield;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduceYieldController extends Controller
{
    public function add(Request $request)
    {
        $yield = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'produce_trader_id' => 'required',
            'produce_yield_qtyHarvested' => 'required',
            'produce_yield_price' => 'required',
            'project_harvestableEnd' => 'required|date',
            'produce_yield_dateHarvestFrom' => 'required|date|after:project_harvestableEnd',
            'produce_yield_dateHarvestTo' => 'required|date|after:produce_yield_dateHarvestFrom'
        ]);
        $contract = Project::find($request->project_id)->contract()->first();
        $inventory = ProduceYield::where('project_id', $request->project_id)->first();
        $produce = null;        
        //$num = $contract->produce_trader()->first()->produce_numOfGrades;
        if(is_array($request->produce_trader_id)){
            foreach($request->produce_trader_id as $id) {
                if(!ProduceTrader::find($id)){
                    return response([
                        'error' => 'Invalid Produce!'
                    ], 400);                    
                }              
            }
        }   
        else{
            $produce = ProduceTrader::find($request->produce_trader_id)->produce()->first();        
        }

        if(!$produce && !is_array($request->produce_trader_id)){
            return response([
                'error' => 'Invalid Produce!'
            ], 400); 
        }
        else{
            if(is_array($request->produce_trader_id)){
                foreach($request->produce_trader_id as $p){                    
                    $produce = ProduceTrader::find($p)->produce()->first();                                     
                    if (!$yield || $contract->produce_id != $produce->id || $inventory) {
                        return response([
                            'error' => 'Invalid Harvest Details!'
                        ], 400);
                    }                    
                }
            }
            else{
                if (!$yield || $contract->produce_id != $produce->id || $inventory) {
                    return response([
                        'error' => 'Invalid Harvest Details!'
                    ], 400);
                }
            }
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
                    'produce_trader_id' => $request->produce_trader_id[$i],
                    'produce_yield_class' => $classes[$i],
                    'produce_yield_qtyHarvested' => $request->produce_yield_qtyHarvested[$i],
                    'produce_yield_price' => $request->produce_yield_price[$i],
                    'produce_yield_dateHarvestFrom' => $request->produce_yield_dateHarvestFrom,
                    'produce_yield_dateHarvestTo' => $request->produce_yield_dateHarvestTo,
                ]);
                $prodInventory = ProduceInventory::create([
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
            $prodInventory = ProduceInventory::create([
                'produce_yield_id' => $yield->id,
                'produce_inventory_qtyOnHand' => $yield->produce_yield_qtyHarvested
            ]);
        }   

        if(is_array($request->produce_trader_id)){
            foreach($request->produce_trader_id as $p){
                $prodTotalQty = ProduceTrader::find($p)->prod_totalQty;
                // $prodTotalQty = DB::table('produce_trader')->where([['produce_id', '=', $request->produce_id], ['trader_id', '=', auth()->id()]])->first()->prod_totalQty;
                $yield = Project::find($request->project_id)->produce_yield()->where('produce_trader_id', $p)->first();
                ProduceTrader::find($p)->update([
                    'prod_totalQty' => $prodTotalQty + $yield->produce_yield_qtyHarvested,
                    'prod_lastDateOfHarvest' => $yield->produce_yield_dateHarvestTo
                ]);
                $farm = Project::find($request->project_id)->contract()->first()->farm()->first();
                $farm_produce = DB::table('farm_produce')->where([['farm_id', $farm->id], ['produce_trader_id', $p]])->first();
                DB::table('farm_produce')->where([['farm_id', $farm->id], ['produce_trader_id', $p]])->update([
                    'on_hand_latestPrice' => $yield->produce_yield_price,
                    'on_hand_qty' => $farm_produce->on_hand_qty + $yield->produce_yield_qtyHarvested,                    
                    'prod_lastDateOfHarvest' => $yield->produce_yield_dateHarvestTo,
                    'updated_at' => Carbon::now(),
                ]);            
            }
        }
        else{
            $prodTotalQty = ProduceTrader::find($request->produce_trader_id)->where('trader_id', auth()->id())->first()->prod_totalQty;
            // $prodTotalQty = DB::table('produce_trader')->where([['produce_id', '=', $request->produce_id], ['trader_id', '=', auth()->id()]])->first()->prod_totalQty;
    
            ProduceTrader::find($request->produce_trader_id)->where('trader_id', auth()->id())->update([
                'prod_totalQty' => $prodTotalQty + $yield->produce_yield_qtyHarvested,
                'prod_lastDateOfHarvest' => $yield->produce_yield_dateHarvestTo
            ]);
            $farm = Project::find($request->project_id)->contract()->first()->farm()->first();
            $farm_produce = DB::table('farm_produce')->where([['farm_id', $farm->id], ['produce_trader_id', $request->produce_trader_id]])->first();
            DB::table('farm_produce')->where([['farm_id', $farm->id], ['produce_trader_id', $request->produce_trader_id]])->update([
                'on_hand_latestPrice' => $yield->produce_yield_price,
                'on_hand_qty' => $farm_produce->on_hand_qty + $yield->produce_yield_qtyHarvested,
                'prod_lastDateOfHarvest' => $yield->produce_yield_dateHarvestTo,
                'updated_at' => Carbon::now(),
            ]);
        }


        // DB::table('produce_trader')->where([['produce_id', '=', $request->produce_id], ['trader_id', '=', auth()->id()]])->update([
        //     'prod_totalQty' => $prodTotalQty + $yield->produce_yield_qtyHarvested,
        //     'prod_lastDateOfHarvest' => ProduceYield::where('produce_id', $request->produce_id)->orderBy('desc')->first()
        // ]);

        return response([
            'message' => 'Harvest Successful!'
        ], 200);
    }
}
