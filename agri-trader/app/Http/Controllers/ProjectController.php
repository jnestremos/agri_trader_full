<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractShare;
use App\Models\Farm;
use App\Models\ProduceTrader;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\Trader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function add(Request $request)
    {
        $project = $request->validate([
            //'trader_id' => 'required',
            'farm_id' => 'required|exists:farms,id',
            'produce_trader_id' => 'required|exists:produce_trader,id',
            'project_status_id' => 'required|exists:project_statuses,id',
            // 'project_stageImg' => 'image',           
            'contract_estimatedHarvest' => 'required|gt:0.00',
            'contract_estimatedPrice' => 'required|gt:0.00',
            'contract_estimatedSales' => 'required|gt:0.00',
            'contract_ownerShare' => 'required|gt:0.00',
            'contract_traderShare' => 'required|gt:0.00',
            'contractShare_type' => 'required',
            'contractShare_amount' => 'required|gt:0.00',
            'project_completionDate' => 'required|date|after:project_commenceDate',
            'project_commenceDate' => 'required|date|before:project_completionDate',
            'project_floweringStart' => 'date|nullable',
            'project_floweringEnd' => 'date|nullable',
            'project_fruitBuddingStart' => 'date|nullable',
            'project_fruitBuddingEnd' => 'date|nullable',
            'project_devFruitStart' => 'date|nullable',
            'project_devFruitEnd' => 'date|nullable',
            'project_harvestableStart' => 'date|nullable',
            'project_harvestableEnd' => 'date|nullable',
        ]);
        $result1 = ProduceTrader::find($request->produce_trader_id);
        $trader = Trader::where('user_id', auth()->id())->first();
        $result2 = DB::table('farm_produce')->where([['farm_id', '=', $request->farm_id], ['produce_trader_id', '=', $request->produce_trader_id]])->first(); 
        if (!$project || !($result1->trader_id == $trader->id) || !$result2) {
            return response([
                'error' => 'Unavailable Produce for that Farm!'
            ], 400);
        }

        $share = ContractShare::create([
            'contractShare_type' => $request->contractShare_type,
            'contractShare_amount' => $request->contractShare_amount,
        ]);

        $farm = Farm::find($request->farm_id);


        // return response([
        //     'produce_trader_id' => $request->produce_trader_id,
        //     'farm_id' => $farm->id,
        //     'trader_id' => auth()->id(),
        //     'share_id' => $share->id
        // ], 200);

        $contract = Contract::create([
            'trader_id' => auth()->id(),
            'farm_id' => $farm->id,
            'contract_share_id' => $share->id,
            'produce_trader_id' => $request->produce_trader_id,
            'contract_estimatedHarvest' => $request->contract_estimatedHarvest,
            'contract_estimatedPrice' => $request->contract_estimatedPrice,
            'contract_estimatedSales' => $request->contract_estimatedSales,
            'contract_ownerShare' => $request->contract_ownerShare,
            'contract_traderShare' => $request->contract_traderShare,
        ]);

        $newProj = Project::create([
            'contract_id' => $contract->id,
            'project_status_id' => $request->project_status_id,
            'project_completionDate' => $request->project_completionDate,
            'project_commenceDate' => $request->project_commenceDate,
            'project_floweringStart' => $request->project_floweringStart,
            'project_floweringEnd' => $request->project_floweringEnd,
            'project_fruitBuddingStart' => $request->project_fruitBuddingStart,
            'project_fruitBuddingEnd' => $request->project_fruitBuddingEnd,
            'project_devFruitStart' => $request->project_devFruitStart,
            'project_devFruitEnd' => $request->project_devFruitEnd,
            'project_harvestableStart' => $request->project_harvestableStart,
            'project_harvestableEnd' => $request->project_harvestableEnd,
        ]);

        $newProj->statuses()->attach($share);

        return response([
            'message' => 'Project Added Successfully!'
        ], 200);
    }
    
    public function addPictures(Request $request, $id){

        if(Project::find($id)){
            $pic = $request->validate([
                'stage' => 'required|string',
                //'image' => 'required|mimes:jpg,jpeg,png|max:5048|image',
                'image' => 'required|string'
            ]);
    
            if(!$pic){
                return response([
                    'error' => 'Invalid image!'
                ], 400);
            }

            $fileNameWithExt = $request->file('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileNameToStore = $fileName . '_' . time() . '.' . $extension;
            $request->file('image')->storeAs('public/project/progress_images', $fileNameToStore);

            ProjectImage::create([
                'project_id' => $id,
                'project_image_stage' => $request->stage,
                'project_image_path' => $fileNameToStore
            ]);            

            return response([
                'message' => 'Image Added!'
            ], 200);
        }
        else{
            return response([
                'error' => 'Project Not Found!'
            ], 400);
        }
       

    }
}
