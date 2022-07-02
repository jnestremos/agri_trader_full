<?php

namespace App\Http\Controllers;

use App\Models\Produce;
use App\Models\Trader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduceController extends Controller
{
    public function assignProduceToTrader(Request $request)
    {
        $produce = $request->validate([
            'produce_id' => 'required|exists:produces,id',
            'produce_numOfGrades' => 'required|numeric',
            'prod_details' => 'nullable|string'
        ]);

        $result = DB::table('produce_trader')->where([['produce_id', '=', $request->produce_id], ['trader_id', '=', auth()->id()]])->first();

        if (!$produce || $result) {
            return response([
                'error' => 'Invalid produce details!'
            ], 400);
        }
        $produce = Produce::find($request->produce_id);
        $trader = Trader::find(auth()->id());
        $produce->traders()->attach($trader);

        DB::table('produce_trader')->where([['produce_id', '=', $request->produce_id], ['trader_id', '=', auth()->id()]])->update([
            'prod_name' => $produce->prod_name,            
            'produce_numOfGrades' => $request->produce_numOfGrades,
            'prod_details' => $request->prod_details
        ]);

        return response([
            'message' => 'Successful!'
        ], 200);
    }
}
