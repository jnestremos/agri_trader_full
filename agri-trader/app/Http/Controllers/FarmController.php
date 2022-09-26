<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\FarmAddress;
use App\Models\FarmOwner;
use App\Models\FarmPartner;
use App\Models\Produce;
use App\Models\ProduceTrader;
use App\Models\Trader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FarmController extends Controller
{
    //
    public function add(Request $request)
    {
        $newFarm = $request->validate([
            'owner_id' => 'required|exists:farm_owners,id',
            'farm_hectares' => 'required|numeric',
            'farm_titleNum' => 'required|string|unique:farms,farm_titleNum',
            'farm_name' => 'required|string|unique:farms,farm_name',
            'farm_imageUrl' => 'required|image|mimes:jpg,jpeg,png|max:5048',
            //'farm_imageUrl' => 'required',
            'farm_province' => 'required',
            'farm_address' => 'required',
            'farm_city' => 'required',
            'farm_zipcode' => 'required'
        ]);

        if (!$newFarm) {
            return response([
                'error' => 'Invalid farm details!'
            ], 400);
        }
        
        $fileNameWithExt = $request->file('farm_imageUrl')->getClientOriginalName();
        $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
        $extension = $request->file('farm_imageUrl')->getClientOriginalExtension();
        $fileNameToStore = $fileName . '_' . time() . '.' . $extension;
        $request->file('farm_imageUrl')->storeAs('public/farms', $fileNameToStore);   
        
        $trader = Trader::where('user_id', auth()->id())->first();

        $farm = Farm::create([
            'farm_owner_id' => $request->owner_id,
            'trader_id' => $trader->id,
            'farm_name' => $request->farm_name,
            'farm_hectares' => $request->farm_hectares,
            'farm_titleNum' => $request->farm_titleNum,
            'farm_imageUrl' => $fileNameToStore,
        ]);

        FarmAddress::create([
            'farm_id' => $farm->id,
            'farm_province' => $request->farm_province,
            'farm_address' => $request->farm_address,
            'farm_zipcode' => $request->farm_zipcode,
            'farm_city' => $request->farm_city,
        ]);

        return response([
            'farm_id' => $farm->id,
            'farm_owner_id' => $farm->farm_owner_id,
            'trader_id' => $farm->trader_id,
            'farm_hectares' => $farm->farm_hectares,
            'farm_titleNum' => $farm->farm_titleNum,
            'farm_imageUrl' => $farm->farm_imageUrl,
            'farm_province' => $request->farm_province,
            'farm_address' => $request->farm_address,
            'farm_zipcode' => $request->farm_zipcode,
            'farm_city' => $request->farm_city,
        ], 200);
    }

    public function addProduce(Request $request)
    {
        $query = $request->validate([
            'id' => 'required',        
            'farm_id' => 'required|exists:farms,id',    
        ]);

        // $result1 = DB::table('produce_trader')->where('id', '=', $request->id)->first();
        $owner = Farm::find($request->farm_id)->farm_owner_id;
        $trader = Trader::where('user_id', auth()->id())->first();
        $result2 = DB::table('owner_trader')->where([['farm_owner_id', '=', $owner], ['trader_id', '=', $trader->id]])->first();

        // if (!$query || !$result1 || !$result2) {
        //     return response([
        //         'error' => 'Invalid!'
        //     ], 400);
        // }
        if (!$query || !$result2) {
            return response([
                'error' => 'Invalid!'
            ], 400);
        }

        $farm = Farm::find($request->farm_id);  
        $produces = ProduceTrader::where([['produce_id', $request->id], ['trader_id', Trader::where('user_id', auth()->id())->first()->id]])->get();     
        foreach($produces as $produce){
            DB::table('farm_produce')->insert([
                'farm_id' => $farm->id,
                'farm_name' => $farm->farm_name,
                'produce_trader_id' => $produce->id,
                'produce_id' => $produce->produce_id,
                'prod_name' => $produce->prod_name,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $prodNumOfFarms = $produce->prod_numOfFarms;
            $produce->prod_numOfFarms = $prodNumOfFarms + 1;
            $produce->save();
            // DB::table('produce_trader')->where('id', '=', $request->id)->update([
            //     'prod_numOfFarms' => $prodNumOfFarms + 1
            // ]);
        } 

        // $farm->produces()->attach($produce);
        return response([
            'produces' => DB::table('farm_produce')->where('farm_id', $request->farm_id)->get()
        ], 200);
    }
}
