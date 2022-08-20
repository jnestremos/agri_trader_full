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
use App\Models\BidOrder;
use App\Models\Contract;
use App\Models\Distributor;
use App\Models\Farm;
use App\Models\FarmOwner;
use App\Models\OnHandBid;
use App\Models\Produce;
use App\Models\ProduceTrader;
use App\Models\ProduceYield;
use App\Models\Project;
use App\Models\ProjectBid;
use App\Models\ProjectStatus;
use App\Models\Trader;
use App\Models\TraderContactNumber;
use App\Models\User;
use Carbon\Carbon;
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
                $farm_produces = DB::table('farm_produce')->where('farm_id', $farm_id)->get();
                $produces = [];
                foreach($farm_produces as $farm_produce){
                    $produce = ProduceTrader::find($farm_produce->produce_trader_id)->produce()->first();
                    array_push($produces, $produce);
                }
                return response([                    
                    'farm_produces' => $farm_produces,
                    'produces' => $produces
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

        Route::prefix('bid/orders')->group(function (){
            Route::get('/', function() {
                $trader = Trader::where('user_id', auth()->id())->first();
                $orders = BidOrder::where('trader_id', $trader->id);
                $idssss = [];
                foreach($orders->get() as $order){
                    array_push($idssss, $order->id);
                }
                $project_bids = ProjectBid::whereIn('bid_order_id', $idssss)->get();
                $on_hand_bids = OnHandBid::whereIn('bid_order_id', $idssss)->get();
                $contracts = Contract::where('trader_id', $trader->id)->get();
                $ids = [];
                $idsss = [];
                foreach($contracts as $contract){
                    array_push($ids, $contract->id);
                    array_push($idsss, $contract->produce_trader_id);
                }
                $projects = Project::whereIn('id', $ids)->get();           
                if(count($orders->get()) > 6){
                    return response([
                        'orders' => $orders->paginate(6),
                        'distributors' => Distributor::all(),
                        'contracts' => $contracts,
                        'projects' => $projects,            
                        'produces' => ProduceTrader::whereIn('id', $idsss)->get(),
                        'project_bids' => $project_bids,
                        'on_hand_bids' => $on_hand_bids,
                    ]);
                }
                else{
                    return response([
                        'orders' => $orders->get(),
                        'distributors' => Distributor::all(),
                        'contracts' => $contracts,
                        'projects' => $projects,            
                        'produces' => ProduceTrader::whereIn('id', $idsss)->get(),
                        'project_bids' => $project_bids,
                        'on_hand_bids' => $on_hand_bids,
                    ]);
                }                
            });

            Route::get('/{id}', function($id){
                $bidOrder = BidOrder::find($id);
                $produce = $bidOrder->project()->first()->contract()->first()->produce_trader()->first();
                $distributor = $bidOrder->distributor()->first();
                $contract = $bidOrder->project()->first()->contract()->first();
                $project_bid = null;
                $on_hand_bid = null;
                $bid_order_acc = $bidOrder->bid_order_account()->latest()->first();
                if($bidOrder->project_bid()->first()){
                    $project_bid = $bidOrder->project_bid()->first();
                }
                else if($bidOrder->on_hand_bid()->first()){
                    $on_hand_bid = $bidOrder->on_hand_bid()->first();
                }
                return response([
                    'produce' => $produce,
                    'project' => $bidOrder->project()->first(),
                    'bidOrder' => $bidOrder,
                    'distributor' => $distributor,
                    'contract' => $contract,
                    'project_bid' => $project_bid,
                    'on_hand_bid' => $on_hand_bid,
                    'bid_order_acc' => $bid_order_acc,
                    'distributor_contactNum' => $distributor->distributor_contactNum()->get()
                ], 200);
            });
        });

        Route::get('/harvest/{id}/details', function($id) {
            $yield = ProduceYield::where('project_id', $id)->get();
            $bidOrders = BidOrder::where([['project_id', $id], ['bid_order_status_id', 4 ]])->get(); 
            $distributors = [];
            $project_bids = [];
            $on_hand_bids = [];
            $contactNums = [];
            foreach($bidOrders as $bidOrder) {
                array_push($distributors, $bidOrder->distributor()->first());
                array_push($contactNums, $bidOrder->distributor()->first()->distributor_contactNum()->first());                
                if($bidOrder->project_bid()->first()){
                    array_push($project_bids, $bidOrder->project_bid()->first());
                }
                if($bidOrder->on_hand_bid()->first()){
                    array_push($on_hand_bids, $bidOrder->on_hand_bid()->first());
                } 
            }
            $produce = $bidOrders[0]->project()->first()->contract()->first()->produce_trader()->first()->produce()->first();
            $produce_trader = $bidOrders[0]->project()->first()->contract()->first()->produce_trader()->first();
            $farm = $bidOrders[0]->project()->first()->contract()->first()->farm()->first();
            $farm_owner = $farm->farm_owner()->first();
            if(count($yield) > 0){
                return response([
                    'project_harvestableEnd' => $yield[0]->project()->first()->project_harvestableEnd,
                    'farm_owner' => $farm_owner,
                    'yield' => $yield,
                    'produce' => $produce,
                    'produce_trader' => $produce_trader,
                    'farm' => $farm,
                    'bid_orders' => $bidOrders,
                    'distributors' => $distributors,
                    'dist_contactNums' => $contactNums,
                    'project_bids' => $project_bids,
                    'on_hand_bids' => $on_hand_bids
                ], 200);
            } 
            return response([
                'project_harvestableEnd' => Project::find($id)->project_harvestableEnd,
                'farm_owner' => $farm_owner,
                'yield' => $yield,
                'produce' => $produce,
                'produce_trader' => $produce_trader,
                'farm' => $farm,
                'bid_orders' => $bidOrders,
                'distributors' => $distributors,
                'dist_contactNums' => $contactNums,
                'project_bids' => $project_bids,
                'on_hand_bids' => $on_hand_bids
            ], 200);             
                                    
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
            //$filteredContracts = Contract::select('*')->whereNotIn('project_status_id', [1, 3])->groupBy('produce_id');
            $contracts = Contract::all();
            $projects = [];
            $produces = Produce::paginate(8);
            $farm_produces = DB::table('farm_produce')->get();
            $produce_yields = ProduceYield::all();
            foreach($contracts as $contract){
                $project = $contract->project()->where('project_floweringStart', '<=', Carbon::now())->first();
                if($project){
                    if($project->project_status_id != '1' && $project->project_status_id != '3' && $project->project_status_id != '4' 
                    && $project->project_status_id != '5'){
                        array_push($projects, $project);
                    }
                }
                //$produce = $contract->produce()->first();
                //array_push($produces, $produce);
            }
            return response([
                'produces' => $produces,                
                'produce_trader' => ProduceTrader::all(),                
                'contracts' => $contracts,
                'projects' => $projects,                
                'farm_produce' => $farm_produces,
                'produce_yields' => $produce_yields,
            ], 200);
        });

        Route::get('/bid/history/{email}', function($email){
            $distributor = Distributor::where('distributor_email', $email)->first();
            $orders = BidOrder::where('distributor_id', $distributor->id)->get();
            $produces = [];
            $project_bids = [];
            $on_hand_bids = [];
            $projects = [];
            $contracts = [];
            $traders = [];
            $trader_contactNums = [];
            $bid_order_accs = [];
            foreach($orders as $order){
                $project_bid = $order->project_bid()->first();
                $on_hand_bid = $order->on_hand_bid()->first();
                $project = $order->project()->first();
                $contract = $project->contract()->first();
                $trader = $order->trader()->first();
                $produce = $contract->produce_trader()->first();
                $contactNums = $trader->trader_contactNum()->get();
                $bid_order_acc = $order->bid_order_account()->latest()->first();
                array_push($produces, $produce);
                array_push($project_bids, $project_bid);
                array_push($on_hand_bids, $on_hand_bid);
                array_push($projects, $project);
                array_push($contracts, $contract);
                array_push($traders, $trader);
                array_push($trader_contactNums, $contactNums);
                array_push($bid_order_accs, $bid_order_acc);
            }
            return response([
                'orders' => BidOrder::where('distributor_id', $distributor->id)->get(),
                'produces' => $produces,
                'project_bids' => $project_bids,
                'on_hand_bids' => $on_hand_bids,
                'projects' => $projects,
                'contracts' => $contracts,
                'traders' => $traders,                
                'trader_contactNums' => $trader_contactNums,
                'bid_order_accs' => $bid_order_accs
            ], 200);
        });

        Route::get('/project/{id}', function($id){
            $produce_trader_id = Contract::find($id)->produce_trader_id;
            return response([
                'contract' => Contract::find($id),
                'project' => Contract::find($id)->project()->first(),
                'allProjects' => Contract::where('produce_trader_id', $produce_trader_id)
            ], 200);
        });


        Route::prefix('bid/project')->group(function () {
            Route::get('/{id}', function($id){          
                $contract = Contract::find($id);
                $trader = $contract->trader()->first();
                $project = $contract->project()->first();
                return response([
                    'trader_id' => $trader->id,
                    'trader_name' => $trader->trader_firstName . ' ' . $trader->trader_lastName,
                    'prod_name' => $contract->produce_trader()->first()->prod_name,
                    'trader_contactNum' => $trader->trader_contactNum()->first()->trader_contactNum,
                    'project_completionDate' => $project->project_completionDate,
                    'project_commenceDate' => $project->project_commenceDate,
                    'trader_email' => $contract->trader->first()->user()->first()->email,
                    'project_floweringStart' => $project->project_floweringStart,
                    'project_floweringEnd' => $project->project_floweringEnd,
                    'project_fruitBuddingStart' => $project->project_fruitBuddingStart,
                    'project_fruitBuddingEnd' => $project->project_fruitBuddingEnd,
                    'project_devFruitStart' => $project->project_devFruitStart,
                    'project_devFruitEnd' => $project->project_devFruitEnd,
                    'project_harvestableStart' => $project->project_harvestableStart,
                    'project_harvestableEnd' => $project->project_harvestableEnd,
                    'contract_estimatedHarvest' => $contract->contract_estimatedHarvest,
                    'contract_estimatedPrice' => $contract->contract_estimatedPrice,
                    'project_images' => $project->project_image()->get(),
                    'farm_name' => $contract->farm()->first()->farm_name
                ], 200);
            });
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
