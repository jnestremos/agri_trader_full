<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractShare;
use App\Models\Farm;
use App\Models\ProduceTrader;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\ProjectStatus;
use App\Models\Trader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function add(Request $request)
    {
        $project = $request->validate([
            //'trader_id' => 'required',
            'farm_id' => 'required|exists:farms,id',
            // 'produce_trader_id' => 'required|exists:produce_trader,id',
            'produce_id' => 'required|exists:produces,id',
            'project_status_id' => 'required|exists:project_statuses,id',
            // 'project_stageImg' => 'image',           
            'contract_estimatedHarvest' => 'required|gt:0.00',
            'contract_estimatedPrice' => 'required|gt:0.00',
            'contract_estimatedSales' => 'required|gt:0.00',
            'contract_ownerShare' => 'required|gt:0.00',
            'contract_traderShare' => 'required|gt:0.00',
            'contractShare_type' => 'required',
            'contractShare_amount' => 'required|gt:0.00',            
            'project_commenceDate' => 'required|date',
            'project_floweringStart' => 'date|nullable',
            'project_floweringEnd' => 'date|nullable',
            'project_fruitBuddingStart' => 'date|nullable',
            'project_fruitBuddingEnd' => 'date|nullable',
            'project_devFruitStart' => 'date|nullable',
            'project_devFruitEnd' => 'date|nullable',
            'project_harvestableStart' => 'date|required',
            'project_harvestableEnd' => 'date|required',
            'startStage' => 'required'
        ]);
        
        $trader = Trader::where('user_id', auth()->id())->first();
        $result1 = ProduceTrader::where([
            ['produce_id', $request->produce_id],
            ['trader_id', $trader->id]
        ])->first();
        $result2 = DB::table('farm_produce')->where([['farm_id', '=', $request->farm_id], ['produce_id', '=', $request->produce_id]])->first();         
        if (!$project || !($result1->trader_id == $trader->id) || !$result2) {
            return response([
                'error' => 'Unavailable Produce for that Farm!'
            ], 400);
        }
        // if (!$project || !$result2) {
        //     return response([
        //         'error' => 'Unavailable Produce for that Farm!'
        //     ], 400);
        // }

        // return response([
        //     'result' => $request->all()
        // ], 200);
        
        
        $arr = explode('-', $request->project_commenceDate);
        $commenceDate = Carbon::create($arr[0], $arr[1], (int)$arr[2] + 1, 0, 0, 0);
        // $commenceDate->hour(0);
        // $commenceDate->min(0);
        // $commenceDate->second(0);
        $now = Carbon::now();
        $now->hour(0);
        $now->min(0);
        $now->second(0);


        // return response([
        //     'result' => $commenceDate
        // ], 400);

        if($commenceDate->lessThan($now)){
            return response([
                'error' => 'Invalid Project Commence Date!'
            ], 400);
        }


        
        if($request->startStage == 1){                        
            $startDate = Carbon::create($request->project_floweringStart)->addDay();           
            if($commenceDate->greaterThan($startDate)){
                return response([
                    'error' => 'Invalid Date Input Under Flowering!'
                ], 400);
            }
        }
        else if($request->startStage == 2){            
            $startDate = Carbon::create($request->project_fruitBuddingStart)->addDay();
            if($commenceDate->greaterThan($startDate)){
                return response([
                    'error' => 'Invalid Date Input Under Fruit Budding!'
                ], 400);
            }
        }
        else if($request->startStage == 3){            
            $startDate = Carbon::create($request->project_devFruitStart)->addDay();
            if($commenceDate->greaterThan($startDate)){
                return response([
                    'error' => 'Invalid Date Input Under Developing Fruit!'
                ], 400);
            }
        }
        else if($request->startStage == 4){            
            $startDate = Carbon::create($request->project_harvestableStart)->addDay();
            if($commenceDate->greaterThan($startDate)){
                return response([
                    'error' => 'Invalid Date Input Under Harvestable!'
                ], 400);
            }
        }


        $share = ContractShare::create([
            'contractShare_type' => $request->contractShare_type,
            'contractShare_amount' => $request->contractShare_amount,
        ]);

        $farm = Farm::find($request->farm_id);

      

        $contract = Contract::create([
            'trader_id' => $trader->id,
            'farm_id' => $farm->id,
            'contract_share_id' => $share->id,
            // 'produce_trader_id' => $request->produce_trader_id,
            'project_status_id' => $request->project_status_id,
            'produce_id' => $request->produce_id,
            'farm_name' => $farm->farm_name,
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

        $status = ProjectStatus::find($newProj->project_status_id);

        $newProj->statuses()->attach($status);

        DB::table('project_status_history')->where([['project_id', $newProj->id], ['project_status_id', $status->id]])->update([
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return response([
            'message' => 'Project Added Successfully!'
        ], 200);
    }

    public function update(Request $request, $id){
        $project = $request->validate([
            'project_status_id' => 'required',
            'project_floweringStart' => 'date|nullable',
            'project_floweringEnd' => 'date|nullable',
            'project_fruitBuddingStart' => 'date|nullable',
            'project_fruitBuddingEnd' => 'date|nullable',
            'project_devFruitStart' => 'date|nullable',
            'project_devFruitEnd' => 'date|nullable',
            'project_harvestableStart' => 'date|nullable',
            'project_harvestableEnd' => 'date|nullable'
        ]);

        if(!$project){
            return response([
                'error' => 'Error while updating project'
            ], 400);
        }

        if($request->project_status_id == 3){
            $project = Project::find($id);
            $project->delete();
            Contract::find($project->contract_id)->delete();

            return response([
                'message' => 'Project Cancelled'
            ], 200);
        }

        // if(($request->project_floweringStart == null && $request->project_floweringEnd != null) 
        // || ($request->project_floweringStart != null && $request->project_floweringEnd == null)
        // || ($request->project_fruitBuddingStart == null && $request->project_fruitBuddingEnd != null)
        // || ($request->project_fruitBuddingStart != null && $request->project_fruitBudding == null)
        // || ($request->project_devFruitStart == null && $request->project_devFruitEnd != null)
        // || ($request->project_devFruitStart != null && $request->project_devFruitEnd == null)
        // || ($request->project_harvestableStart == null && $request->project_harvestableEnd != null)
        // || ($request->project_harvestableStart != null && $request->project_harvestableEnd == null)){
            
        //     return response([
        //         'error' => 'One of the dates fields has been missed out!'
        //     ], 400);

        // }

        // $commenceDate = Project::find($id)->project_commenceDate;

        // if(
        //     Carbon::create($request->project_floweringStart)->greaterThan(Carbon::create($request->project_floweringEnd))
        //     || Carbon::create($request->project_floweringEnd)->greaterThan(Carbon::create($request->project_fruitBuddingStart))
        //     || Carbon::create($request->project_fruitBuddingStart)->greaterThan(Carbon::create($request->project_fruitBuddingEnd))
        //     || Carbon::create($request->project_fruitBuddingEnd)->greaterThan(Carbon::create($request->project_devFruitStart))
        //     || Carbon::create($request->project_devFruitStart)->greaterThan(Carbon::create($request->project_devFruitEnd))
        //     || Carbon::create($request->project_devFruitEnd)->greaterThan(Carbon::create($request->project_harvestableStart))            
        //     || Carbon::create($request->project_harvestableStart)->greaterThan(Carbon::create($request->project_harvestableEnd))
        //     || Carbon::create($request->project_harvestableEnd)->greaterThan(Carbon::create($commenceDate))){
            
        //         return response([
        //             'error' => 'Invalid Date Input!'
        //         ], 400);
        // }

        Project::find($id)->update([
            'project_status_id' => $request->project_status_id,
            'project_floweringStart' => $request->project_floweringStart,
            'project_floweringEnd' => $request->project_floweringEnd,
            'project_fruitBuddingStart' => $request->project_fruitBuddingStart,
            'project_fruitBuddingEnd' => $request->project_fruitBuddingEnd,
            'project_devFruitStart' => $request->project_devFruitStart,
            'project_devFruitEnd' => $request->project_devFruitEnd,
            'project_harvestableStart' => $request->project_harvestableStart,
            'project_harvestableEnd' => $request->project_harvestableEnd
        ]);

        $updProj = Project::find($id);

        Contract::find($updProj->contract_id)->update([
            'project_status_id' => $updProj->project_status_id
        ]);

        $status = ProjectStatus::find($request->project_status_id);
        $updProj->statuses()->attach($status);

        DB::table('project_status_history')->where([['project_id', $id], ['project_status_id', $request->project_status_id]])->update([
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        

        return response([
            'message' => 'Project updated successfully'
        ], 200);

        
    }
    
    public function addPictures(Request $request){
        $pic = $request->validate([
            'project_id' => 'required',
            'stage' => 'required|string',
            'images' => 'required',              
        ]);

        if(!$pic){
            return response([
                'error' => 'Invalid image!'
            ], 400);
        }

        if(Project::find($request->project_id)){
            if($request->hasFile('images')){
                foreach($request->file('images') as $image){
                    $fileNameWithExt = $image->getClientOriginalName();
                    $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
                    $extension = $image->getClientOriginalExtension();
                    $validExts = ['jpg','png'];
                    if(in_array($extension, $validExts)){
                        $fileNameToStore = $fileName . '_' . time() . '.' . $extension;
                        $image->storeAs('public/project/progress_images', $fileNameToStore);
                        ProjectImage::create([
                            'project_id' => $request->project_id,
                            'project_image_stage' => $request->stage,
                            'project_image_path' => $fileNameToStore
                        ]);
                    }
                    else{
                        return response([
                            'error' => 'File must be an image!'
                        ], 400);
                    }
                }
            }            

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
