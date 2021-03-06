<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\ProjectBidController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\FarmOwnerController;
use App\Http\Controllers\FarmPartnerController;
use App\Http\Controllers\OnHandBidController;
use App\Http\Controllers\ProduceController;
use App\Http\Controllers\ProduceYieldController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RefundController;
use App\Models\Contract;
use App\Models\Farm;
use App\Models\FarmOwner;
use App\Models\Produce;
use App\Models\ProduceTrader;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Trader;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::get('/', function(){
//     return response([
//         'message' => 'I\'m here'
//     ], 200);
// });

// -------------------- PUBLIC ROUTES ----------------------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// -------------------- END OF PUBLIC ROUTES ----------------

// ------------------- PROTECTED ROUTES ----------------------

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('farm/images/{path}', function($fileName) {            
        $path = public_path().'/storage/farms/'.$fileName;
        return response()->download($path, 'Farm Image');        
    });

    Route::group(['middleware' => ['role:trader']], function () {

        Route::get('/dashboard', function(){
            return response([
                'name' => Trader::where('user_id',auth()->id())->first()->trader_firstName . " " . 
                Trader::where('user_id',auth()->id())->first()->trader_lastName
            ], 200);
        });

        

        Route::prefix('farm')->group(function () {

            Route::get('/', function (){
                $farm_produces = [];
                $trader = Trader::where('user_id',auth()->id())->first();
                $farms = Farm::where('trader_id',$trader->id);
                foreach($farms->get() as $farm){
                    $farm_produce = [
                        'id' => $farm->id,
                        'name' => $farm->farm_name,
                        'produces' => DB::table('farm_produce')->where('farm_id', $farm->id)->get()
                    ]; 
                    array_push($farm_produces, $farm_produce);
                }               
                if(count($farms->get()) > 6){
                    return response([   
                        'farm_produces' => $farm_produces,
                        'farms' => $farms->paginate(6)
                    ], 200);
                }
           
                return response([  
                    'farm_produces' => $farm_produces,                                                          
                    'farms' => $farms->get()
                ], 200);
                              
            });

            Route::get('/all', function(){
                $farm_produce = [];
                $owner_names = [];
                $farms = [];                                             
                $trader = Trader::where('user_id', auth()->id())->first();
                foreach(Farm::where('trader_id', $trader->id)->get() as $farm){                   
                    if(count(DB::table('farm_produce')->where('farm_id', $farm->id)->get()) > 0){
                        array_push($farms, $farm);
                        array_push($owner_names, $farm->farm_owner()->first());
                    }
                }                                
                return response([
                    'owners' => $owner_names,                
                    'farms' => $farms,                                                                           
                ], 200);
            });

            Route::get('/produces/all/{farm_id}', function($farm_id){            
                return response([                    
                    'produces' => DB::table('farm_produce')->where('farm_id', $farm_id)->get()
                ], 200);
            });

            Route::get('/details/{id}', function ($id){
                return response([
                    'owner' => Farm::find($id)->farm_owner()->first(),
                    'farm' => Farm::find($id),                    
                    'produces' => DB::table('farm_produce')->where('farm_id', $id)->get(),
                    'farm_address' => DB::table('farm_addresses')->where('farm_id', $id)->first(),
                    'farm_partners' =>DB::table('farm_to_farm_partner_assignment')->where('farm_id', $id)->get(),                    
                ], 200);
            });

            Route::controller(FarmController::class)->group(function () {
                Route::post('/add', 'add');
                Route::post('/add/produce', 'addProduce');
            });

            Route::get('/owners', function (){
               $farmOwners = []; 
               $trader = Trader::where('user_id', auth()->id())->first();        
               $owners = DB::table('owner_trader')->where('trader_id', $trader->id)->get();

            //    return response([
            //     'id' => auth()->id(),
            //    ], 200);

               foreach($owners as $owner){
                $farmOwner = [
                    'id' => $owner->farm_owner_id,
                    'owner_firstName' => FarmOwner::find($owner->farm_owner_id)->owner_firstName,
                    'owner_lastName' => FarmOwner::find($owner->farm_owner_id)->owner_lastName,
                ];
                array_push($farmOwners, $farmOwner);               
               }
               return response([                
                'owners' => $farmOwners
                ], 200);
              
            });

            Route::controller(FarmOwnerController::class)->group(function () {
                Route::post('/owner/add', 'add');
            });

            Route::controller(FarmPartnerController::class)->group(function () {
                Route::post('/partner/add', 'add');
                Route::post('/partner/assignToFarm', 'assignToFarm');
            });
        });
        
        Route::get('/produces/all', function(){
            return response([
                'produces' => Produce::all()
            ]);
        });

        Route::get('/produce/list', function (){
            $trader = Trader::where('user_id', auth()->id())->first();
            $produces = ProduceTrader::where('trader_id', $trader->id)->get();
            if(count($produces) > 6){
                return response([
                    'produces' => ProduceTrader::where('trader_id', $trader->id)->paginate(6)
                ], 200);
            }
            else{
                return response([
                    'produces' => $produces
                ], 200);
            }
            
        });

        Route::get('/produces/{farm_id}', function ($farm_id){            
            return response([
                'produces' => DB::table('farm_produce')->where('farm_id', $farm_id)->get()
            ], 200);
        });
        
        Route::get('/producess/{farm_id}', function ($farm_id){
            $trader = Trader::where('user_id', auth()->id())->first();
            $produces = ProduceTrader::where('trader_id', $trader->id)->get();   //1, 2, 3
            $farm_produces = DB::table('farm_produce')->where('farm_id', $farm_id)->get();     //1, 2      
            $filteredProduces = [];
            for($i = 0; $i < count($produces); $i++){
                $check = true;
                for($ii = 0; $ii < count($farm_produces); $ii++){
                    if($produces[$i]->id == $farm_produces[$ii]->produce_trader_id){
                        $check = false;
                        break;
                    }
                    else{
                        $check = true;
                    }
                }
                if($check){
                    array_push($filteredProduces, $produces[$i]);
                }
            }
            return response([
                'produces' => $filteredProduces
            ], 200);
        });

        Route::get('/produce/details/{id}', function ($id){
            $trader = Trader::where('user_id', auth()->id())->first();
            return response([
                'produce' => Produce::find($id),
                'grades' => ProduceTrader::where([['trader_id', $trader->id], ['produce_id', $id]])->first()->produce_numOfGrades,
                'farms' => ProduceTrader::where([['trader_id', $trader->id], ['produce_id', $id]])->first()->prod_numOfFarms,
                'dateOfHarvest' => ProduceTrader::where([['trader_id', $trader->id], ['produce_id', $id]])->first()->produce_yield_dateHarvestTo,
            ], 200);
        });

        Route::prefix('produce')->group(function () {            
            Route::post('/add', [ProduceController::class, 'assignProduceToTrader']);
        });

        Route::prefix('yield')->group(function () {
            Route::post('/add', [ProduceYieldController::class, 'add']);
        });


        Route::prefix('project')->group(function () {
            Route::controller(ProjectController::class)->group(function () {
                Route::post('/add', 'add');
                Route::post('/add/pictures/{id}', 'addPictures');
            });
            Route::put('/refund/approve/{id}', [RefundController::class, 'approveRefund']);            
        });

        Route::prefix('projects')->group(function (){            

            Route::get('/', function () {       
                $farms = [];
                $farm_owners = [];
                $produces = [];
                $shares = [];
                $start_dates = [];
                $trader = Trader::where('user_id', auth()->id())->first();
                $contracts = Contract::where('trader_id', $trader->id);    

                foreach($contracts->get() as $contract){
                    $farm = $contract->farm()->first();
                    $farm_owner = $farm->farm_owner()->first();
                    $produce = $contract->produce_trader()->first();
                    $share = $contract->contract_share()->first();
                    $start_date = $contract->project()->first();
                    array_push($farms, $farm);
                    array_push($farm_owners, $farm_owner);
                    array_push($produces, $produce);
                    array_push($shares, $share);
                    array_push($start_dates, $start_date);
                }                        
                if(count($contracts->get()) > 6){
                    return response([
                        'projects' => $contracts->paginate(6),
                        'farms' => $farms,
                        'farm_owners' => $farm_owners,
                        'produces' => $produces,
                        'shares' => $shares,
                        'start_dates' => $start_dates,
                    ], 200);
                } 
                return response([
                    'projects' => $contracts->get(),
                    'farms' => $farms,
                    'farm_owners' => $farm_owners,
                    'produces' => $produces,
                    'shares' => $shares,
                    'start_dates' => $start_dates,
                ], 200);   
            });

            Route::get('/{id}', function ($id){                
                return response([
                    'farm' => Project::find($id)->contract()->first()->farm()->first(),
                    'contract' => Project::find($id)->contract()->first(),
                    'project' => Project::find($id),
                    'share' => Project::find($id)->contract()->first()->contract_share()->first(),
                    'farm_owner' => Project::find($id)->contract()->first()->farm()->first()->farm_owner()->first(),
                    'produce' => Project::find($id)->contract()->first()->produce_trader()->first(),
                    'history' => DB::table('project_status_history')->where('project_id', $id)->get()                  
                ], 200);
            });
            
            Route::patch('/{id}', [ProjectController::class, 'update']);

        });





        //Route::put('/bid/{id}', [ProjectBidController::class, 'approveProjectBid']);        
    });

    Route::group(['middleware' => ['role:distributor|trader']], function () {
        Route::prefix('bid/project/{id}')->group(function () {
            Route::controller(ProjectBidController::class)->group(function () {
                Route::put('/approve', 'approveProjectBid');
                Route::post('/payment', 'paymentProjectBid');
            });
            Route::put('/deliver', [DeliveryController::class, 'deliverProjectBid']);
        });

        Route::prefix('bid/onhand/{id}')->group(function () {
            Route::controller(OnHandBidController::class)->group(function () {
                Route::put('/approve', 'approveOnHandBid');
                Route::post('/payment', 'paymentOnHandBid');
            });
            Route::put('/deliver', [DeliveryController::class, 'deliverOnHandBid']);
        });
    });

    Route::group(['middleware' => ['role:distributor']], function () {

        Route::get('/catalog', function () {
            $filteredContracts = Contract::select('*')->whereNotIn('project_status_id', [1, 3])->groupBy('produce_id');
            $contracts = Contract::all();
            $projects = [];
            $produces = [];
            foreach($contracts as $contract){
                $project = $contract->project()->first();
                $produce = $contract->produce()->first();
                array_push($projects, $project);
                array_push($produces, $produce);
            }
            if(count($filteredContracts->get()) > 8){
                return response([
                    'filteredContracts' => $filteredContracts->paginate(8),
                    'contracts' => $contracts,
                    'projects' => $projects,
                    'produces' => $produces
                ], 200);
            }
            else{
                return response([
                    'filteredContracts' => $filteredContracts->get(),
                    'contracts' => $contracts,
                    'projects' => $projects,
                    'produces' => $produces
                ], 200);
            }
        });


        Route::prefix('bid/project')->group(function () {
            Route::controller(ProjectBidController::class)->group(function () {
                Route::post('/add', 'addProjectBid');
            });

            Route::prefix('refund')->group(function () {
                Route::controller(RefundController::class)->group(function () {
                    Route::post('/request/{id}', 'requestRefund');
                    Route::put('/confirm/{id}', 'confirmRefund');
                });
            });
        });

        Route::prefix('bid/onhand')->group(function () {
            Route::controller(OnHandBidController::class)->group(function () {
                Route::post('/add', 'addOnHandBid');
            });

            Route::prefix('refund')->group(function () {
                Route::controller(RefundController::class)->group(function () {
                    Route::post('/request/{id}', 'requestRefund');
                    Route::put('/confirm/{id}', 'confirmRefund');
                });
            });
        });
    });
});

// --------------------- END OF PROTECTED ROUTES ----------------------

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
