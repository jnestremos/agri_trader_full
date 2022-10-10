<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\ExpenditureController;
use App\Http\Controllers\ProjectBidController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\FarmOwnerController;
use App\Http\Controllers\FarmPartnerController;
use App\Http\Controllers\OnHandBidController;
use App\Http\Controllers\ProduceController;
use App\Http\Controllers\ProduceYieldController;
use App\Http\Controllers\ProfitSharingController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReceivingReportController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\SupplyInventoryController;
use App\Http\Controllers\SupplyOrderRefundController;
use App\Http\Controllers\SupplyOrderReturnController;
use App\Http\Controllers\SupplyPurchaseOrderController;
use App\Models\BidOrder;
use App\Models\Contract;
use App\Models\Delivery;
use App\Models\Distributor;
use App\Models\Expenditure;
use App\Models\Farm;
use App\Models\FarmOwner;
use App\Models\OnHandBid;
use App\Models\Produce;
use App\Models\ProduceInventory;
use App\Models\ProduceTrader;
use App\Models\ProduceYield;
use App\Models\ProfitSharing;
use App\Models\Project;
use App\Models\ProjectBid;
use App\Models\ProjectStatus;
use App\Models\ReceivingReport;
use App\Models\Refund;
use App\Models\Sale;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Supplier;
use App\Models\Supply;
use App\Models\SupplyInventory;
use App\Models\SupplyOrderReturn;
use App\Models\SupplyPurchaseOrder;
use App\Models\Trader;
use App\Models\TraderContactNumber;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\PseudoTypes\False_;
use Ramsey\Collection\Set;

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

    Route::group(['middleware' => ['role:farm_owner']], function (){

        Route::get('project/owner/{id}', function ($id){                
            return response([
                'farm' => Project::find($id)->contract()->first()->farm()->first(),
                'contract' => Project::find($id)->contract()->first(),
                'project' => Project::find($id),
                'share' => Project::find($id)->contract()->first()->contract_share()->first(),
                'farm_owner' => Project::find($id)->contract()->first()->farm()->first()->farm_owner()->first(),
                'produce' => Project::find($id)->contract()->first()->produce()->first(),
                'history' => DB::table('project_status_history')->where('project_id', $id)->get(),
                'profit_sharing' => ProfitSharing::where('project_id', $id)->first(),
                'produce_yield' => ProduceYield::where('project_id', $id)->get()              
            ], 200);
        });

        Route::patch('project/owner/{id}', [ProjectController::class, 'update']);


        Route::get('/projects/owner/all', function (){
            $user = User::find(auth()->id())->farm_owner()->first();
            $farms = $user->farm()->get();
            $trader = [];
            // make sure the owner can remove his partnership with trader
            $contracts = null;
            $produces = [];
            $shares = [];
            $start_dates = [];
            foreach($farms as $farm){
                if(!in_array($farm->trader()->first(), $trader)){
                    array_push($trader, $farm->trader()->first());
                }
                $contracts = Contract::where([
                    ['farm_id', $farm->id],                
                ]);  
                foreach($contracts->get() as $contract){
                    if(!in_array($contract->produce()->first(), $produces)){
                        array_push($produces, $contract->produce()->first());
                    }
                    array_push($shares, $contract->contract_share()->first());             
                    array_push($start_dates, $contract->project()->first());             
                }               
            }
           
            if(count($contracts->get()) > 6){
                return response([
                    'projects' => $contracts->paginate(6),
                    'trader' => $trader,
                    'produces' => $produces,
                    'shares' => $shares,
                    'start_dates' => $start_dates,
                    'farms' => $farms,
                ]);
            }
            else{
                return response([
                    'projects' => $contracts->get(),
                    'trader' => $trader,
                    'produces' => $produces,
                    'shares' => $shares,
                    'start_dates' => $start_dates,
                    'farms' => $farms,
                ]);
            }
        });

        Route::get('/farms/owner/all', function (){
            $user = User::find(auth()->id())->farm_owner()->first();
            $farms = Farm::where('farm_owner_id', $user->id);
            $contracts = [];
            $projects = [];
            $farm_produces = [];
            $traders = [];
            foreach($farms->get() as $farm){
                if(!in_array($farm->trader()->first(), $traders)){
                    array_push($traders, $farm->trader()->first());
                }
                $contractss = Contract::where('farm_id', $farm->id)->get();
                foreach($contractss as $contract){
                    array_push($contracts, $contract);
                    array_push($projects, $contract->project()->first());
                }
                $produces = DB::table('farm_produce')->where('farm_id', $farm->id)->get();
                foreach($produces as $produce){
                    array_push($farm_produces, $produce);
                }
            }
            if(count($farms->get()) > 6){
                return response([
                    'contracts' => $contracts,
                    'projects' => $projects,
                    'traders' => $traders,
                    'farm_produces' => $farm_produces,
                    'farms' => $farms->paginate(6)
                ]);
            }
            else{
                return response([
                    'contracts' => $contracts,
                    'projects' => $projects,
                    'traders' => $traders,
                    'farm_produces' => $farm_produces,
                    'farms' => $farms->get()
                ]);
            }        
        });

        Route::get('/farm/owner/{id}', function ($id){
            $farm_produces = DB::table('farm_produce')->where('farm_id', $id)->get();
            $farm = Farm::find($id);
            $farm_address = $farm->farm_address()->first();
            $owner = $farm->farm_owner()->first();
            $contracts = Contract::where('farm_id', $farm->id)->get();
            $projects = [];
            foreach($contracts as $contract){
                if(!$contract->project()->first()->project_completionDate){
                    array_push($projects, $contract->project()->first());
                }
            }

            return response([
                'farm_produces' => $farm_produces,
                'farm' => $farm,
                'farm_address' => $farm_address,
                'owner' => $owner,
                'projects' => $projects
            ]);
        });

        Route::get('/produces/owner/all', function (){
            $user = User::find(auth()->id())->farm_owner()->first();
            $farms = Farm::where('farm_owner_id', $user->id)->get();            
            $farm_ids = [];
            $produce_trader_ids = [];
            $produce_list = Produce::all();
            foreach($farms as $farm){
                array_push($farm_ids, $farm->id);
                $farm_produces = DB::table('farm_produce')->where('farm_id', $farm->id)->get();
                foreach($farm_produces as $produce){
                    if(!in_array($produce->produce_trader_id, $produce_trader_ids)){
                        array_push($produce_trader_ids, $produce->produce_trader_id);                        
                    }
                }
            }
            $produces = ProduceTrader::whereIn('id', $produce_trader_ids);
            if(count($produces->get()) > 6){
                return response([
                    'produces' => $produces->paginate(6),
                    'produce_list' => $produce_list,
                ]);
            }
            else{
                return response([
                    'produces' => $produces->get(),
                    'produce_list' => $produce_list,
                ]);
            }
        }); 
        
        
        Route::get('/produce/owner/{id}', function ($id) {
            $user = User::find(auth()->id())->farm_owner()->first();
            $farms = Farm::where('farm_owner_id', $user->id)->get();
            $produce_trader = ProduceTrader::find($id);
            $produce_history = [];
            $projects = [];
            $contractss = [];
            foreach($farms as $farm){
               $contracts = Contract::where('farm_id', $farm->id)->latest()->get();
               foreach($contracts as $contract){
                array_push($contractss, $contract);
                $project = $contract->project()->first();
                array_push($projects, $project);
                foreach($project->produce_yield()->orderBy('produce_yield_class', 'desc')->get() as $yield){
                    array_push($produce_history, $yield);
                }                
               }                
            }
           $produce = ProduceTrader::find($id)->produce()->first();

            return response([
                'produce_history' => $produce_history,
                'produce' => $produce,
                'produce_trader' => $produce_trader,
                'projects' => $projects,
                'contracts' => $contractss,
                'farms' => $farms,
            ]);

        });

        Route::prefix('project/profit/sharing/owner')->group(function () {
            Route::patch('/{id}', [ProfitSharingController::class, 'updateAR']);
            Route::delete('/{id}', [ProfitSharingController::class, 'deleteAR']);
        });

    });

    Route::group(['middleware' => ['role:trader']], function () {

        Route::controller(SupplierController::class)->prefix('supplier')->group(function () {
            Route::get('/', 'fetchSuppliers');
            Route::get('/{id}', 'fetchSupplier');
            Route::patch('/{id}', 'updateSupplier');
            Route::post('/add', 'addSupplier');
        });


        Route::controller(SupplyController::class)->prefix('supply')->group(function () {
            Route::get('/', 'formForAddSupply');
            Route::patch('/{id}', 'editSupply');
            Route::get('/list', 'getSupplyList');
            Route::post('/add', 'addSupply');
        });

        Route::controller(SupplyPurchaseOrderController::class)->prefix('supplyOrder')->group(function (){
            Route::get('/dashboard', 'setDashboard');
            Route::get('/', 'formForAddPO');
            Route::post('/add', 'addPO');
            Route::patch('/{id}', 'updateStatus');
            Route::patch('/{id}/payment', 'updatePayment');

        });

        Route::controller(ReceivingReportController::class)->prefix('receiving/report')->group(function () {
            Route::get('/{id}', 'getPOForRR');
            Route::get('/{id}/update', 'updateRR');
            Route::post('/add', 'addReceivingReport');
        });

        Route::controller(SupplyOrderReturnController::class)->prefix('supplyReturn')->group(function (){
            Route::get('/dashboard', 'setReturnDashboard');
            Route::post('/add', 'addSupplyReturn');
        });
        Route::controller(SupplyOrderRefundController::class)->prefix('supplyRefund')->group(function (){
            Route::get('/', 'getSupplyRefunds');
            Route::post('/add', 'addSupplyRefund');
            Route::patch('/{id}', 'confirmSupplyRefund');
        });
        
        Route::controller(SupplyInventoryController::class)->prefix('supply/inventory')->group(function (){
            Route::get('/', 'getSupplyInventory');
        });
        
        Route::controller(ExpenditureController::class)->prefix('project/expenditures')->group(function (){
            Route::get('/{id}', 'getProjectExpenditures');
            Route::post('/add', 'addProjectExpenditure');
        });

        Route::get('/stockIn/history', function (){
            $user = User::find(auth()->id())->trader()->first();
            $orders = SupplyPurchaseOrder::where('trader_id', $user->id)->groupBy('purchaseOrder_num')->get();
            $stockIn_history = [];
            $suppliers = [];
            $supplies = [];            
            foreach($orders as $order){
                $stocks = StockIn::where('purchaseOrder_num', $order->purchaseOrder_num)->get();
                foreach($stocks as $stock){
                    array_push($stockIn_history, $stock);
                    if(!in_array($stock->supply()->first(), $supplies)){
                        array_push($supplies, $stock->supply()->first());
                    }
                    if(!in_array($stock->supply()->first()->supplier()->first(), $suppliers)){
                        array_push($suppliers, $stock->supply()->first()->supplier()->first());
                    }
                }
            }
            $produces = Produce::groupBy('prod_type')->get();
            return response([
                'stockIn_history' => $stockIn_history,
                'suppliers' => $suppliers,
                'supplies' => $supplies,
                'produces' => $produces,
            ]);

        });

        Route::get('/stockOut/history', function (){
            $user = User::find(auth()->id())->trader()->first();
            $contracts = Contract::where('trader_id', $user->id)->get();
            $projects = [];
            foreach($contracts as $contract){                
                array_push($projects, $contract->project()->first());
            }
            $stockOut_history = [];
            $suppliers = [];
            $supplies = [];
            foreach($projects as $project){
                $stockOuts = StockOut::where('project_id', $project->id)->get();
                foreach($stockOuts as $stockOut){
                    array_push($stockOut_history, $stockOut);
                    if(!in_array($stockOut->supplier()->first(), $suppliers)){
                        array_push($suppliers, $stockOut->supplier()->first());
                    }
                    if(!in_array($stockOut->supply()->first(), $supplies)){
                        array_push($supplies, $stockOut->supply()->first());
                    }
                }
            }
            $produces = Produce::groupBy('prod_type')->get();
            return response([
                'stockOut_history' => $stockOut_history,
                'suppliers' => $suppliers,
                'supplies' => $supplies,
                'produces' => $produces,
            ]);
        });

        Route::get('/project/stockOut/{id}', function ($id){
            if(!Project::find($id)){
                return response([
                    'error' => 'Project not found!'
                ], 400);
            }
            $user = User::find(auth()->id())->trader()->first();            
            $suppliers = Supplier::where('trader_id', $user->id)->get();  
            $supplier_ids = [];   
            $supplies = [];                       
            foreach($suppliers as $supplier){                
                array_push($supplier_ids, $supplier->id);                               
            }
            foreach($suppliers as $supplier){
                foreach($supplier->supply()->get() as $supply){
                    array_push($supplies, $supply);
                }
            }  
            // $suppliers = [];          
            $inventory = SupplyInventory::whereIn('supplier_id', $supplier_ids)->where('supply_qty', '>', 0)->get();
            // foreach($inventory as $item){
            //     if(!in_array($item->supplier()->first(), $suppliers)){
            //         array_push($suppliers, $item->supplier()->first());
            //     }
            //     array_push($supplies, $item->supply()->first());
            // }
            $stockOut = StockOut::where('project_id', $id)->get(); 
            $produces = Produce::groupBy('prod_type')->get();           

            return response([
                'inventory' => $inventory,
                'supplies' => $supplies,
                'suppliers' => $suppliers,
                'stockOut' => $stockOut,
                'produces' => $produces,
            ]);
            
        });
        Route::post('/project/stockOut/{id}', [StockOutController::class, 'addStockOut']);

        Route::prefix('project/profit/sharing')->group(function () {
            Route::get('/{id}', function ($id){
                $project = Project::find($id);
                if(!$project){
                    return response([
                        'error' => 'Project not found',
                    ], 400);
                }
                $contract = $project->contract()->first();
                $produce = $contract->produce()->first();
                $farm_owner = $contract->farm()->first()->farm_owner()->first();
                $sales = Sale::where('project_id', $id)->get();
                $stockOut = StockOut::where('project_id', $id)->get();
                $supplies = [];
                foreach($stockOut as $stock){
                    if(!in_array($stock->supply()->first(), $supplies)){
                        array_push($supplies, $stock->supply()->first());
                    }
                }
                $expenditures = Expenditure::where('project_id', $id)->get();
                $contract_share = $contract->contract_share()->first();
                $profit_sharing = $project->profit_sharing()->first();
                return response([
                    'project' => $project,
                    'contract' => $contract,
                    'sales' => $sales,
                    'produce' => $produce,
                    'stockOut' => $stockOut,
                    'farm_owner' => $farm_owner,
                    'supplies' => $supplies,
                    'expenditures' => $expenditures,
                    'contract_share' => $contract_share,
                    'profit_sharing' => $profit_sharing,
                ]);
            });
            Route::post('/add', [ProfitSharingController::class, 'createAR']);
        });

        Route::post('/refund/all/orders/{id}', [ProfitSharingController::class, 'refundAllOrders']);

        Route::get('/reports/otherExpenditures', function (){
            $user = User::find(auth()->id())->trader()->first();
            $contracts = Contract::where('trader_id', $user->id)->get();
            $projects = [];
            foreach($contracts as $contract){
                array_push($projects, $contract->project()->first());
            }       
            $expenditures = [];
            foreach($projects as $project){                
                foreach($project->expenditure()->get() as $expenditure){
                    array_push($expenditures, $expenditure);
                }
            }
            return response([
                'expenditures' => $expenditures,
                'contracts' => $contracts,
                'projects' => $projects,
            ]);
        });
        Route::get('/reports/supplyExpenditures', function (){
            $user = User::find(auth()->id())->trader()->first();
            $contracts = Contract::where('trader_id', $user->id)->get();
            $projects = [];
            foreach($contracts as $contract){
                array_push($projects, $contract->project()->first());
            }       
            $stockOut = [];
            $suppliers = [];
            $supplies = [];
            foreach($projects as $project){                
                foreach($project->stock_out()->get() as $stock){
                    array_push($stockOut, $stock);
                    if(!in_array($stock->supplier()->first(), $suppliers)){
                        array_push($suppliers, $stock->supplier()->first());
                    }
                    if(!in_array($stock->supply()->first(), $supplies)){
                        array_push($supplies, $stock->supply()->first());
                    }
                }
            }
            return response([
                'stockOut' => $stockOut,
                'suppliers' => $suppliers,
                'supplies' => $supplies,
                'contracts' => $contracts,
                'projects' => $projects,
            ]);
        });

        Route::get('/reports/profitSharing', function (){
            $user = User::find(auth()->id())->trader()->first();
            $contracts = Contract::where('trader_id', $user->id)->get();
            $projects = [];
            $farms = [];
            $farm_owners = [];
            $produces = [];
            foreach($contracts as $contract){
                array_push($projects, $contract->project()->first());
                if(!in_array($contract->farm()->first(), $farms)){
                    array_push($farms, $contract->farm()->first());
                    array_push($farm_owners, $contract->farm()->first()->farm_owner()->first());
                }
                if(!in_array($contract->produce()->first(), $farms)){
                    array_push($produces, $contract->produce()->first());
                }
            }       
            $profit_sharing = [];           
            foreach($projects as $project){                
                array_push($profit_sharing, $project->profit_sharing()->first());
            }
            return response([
                'profit_sharing' => $profit_sharing,                
                'farms' => $farms,                
                'farm_owners' => $farm_owners,                
                'produces' => $produces,                
                'contracts' => $contracts,
                'projects' => $projects,
            ]);
        });

        Route::get('/reports/projects', function (){
            $user = User::find(auth()->id())->trader()->first();
            $contracts = Contract::where('trader_id', $user->id)->get();
            $projects = [];
            $produces = [];
            $farms = [];
            $farm_owners = [];
            $contract_shares = [];
            foreach($contracts as $contract){
                if(!in_array($contract->produce()->first(), $produces)){
                    array_push($produces, $contract->produce()->first());
                }
                if(!in_array($contract->farm()->first(), $farms)){
                    array_push($farms, $contract->farm()->first());
                }
                if(!in_array($contract->farm()->first()->farm_owner()->first(), $farm_owners)){
                    array_push($farm_owners, $contract->farm()->first()->farm_owner()->first());
                }
                array_push($projects, $contract->project()->first());
                array_push($contract_shares, $contract->contract_share()->first());
            }
            
            return response([
                'contracts' => $contracts,
                'projects' => $projects,
                'produces' => $produces,
                'farms' => $farms,
                'farm_owners' => $farm_owners,
                'contract_shares' => $contract_shares,
            ]);
        });

        Route::get('/reports/bid/orders', function (){
            $user = User::find(auth()->id())->trader()->first();
            $orders = BidOrder::where('trader_id', $user->id)->get();
            $accs = [];
            $project_bids = [];
            $on_hand_bids = [];
            $distributors = [];
            $produce_traders = [];
            $produces = [];
            foreach($orders as $order){
                if($order->project_bid()->first()){
                    array_push($project_bids, $order->project_bid()->first());
                }
                else if($order->on_hand_bid()->first()){
                    array_push($on_hand_bids, $order->on_hand_bid()->first());
                }
                if(!in_array($order->distributor()->first(), $distributors)){
                    array_push($distributors, $order->distributor()->first());
                }
                if(!in_array($order->produce_trader()->first(), $produce_traders)){
                    array_push($produce_traders, $order->produce_trader()->first());
                }
                if(!in_array($order->produce_trader()->first()->produce()->first(), $produces)){
                    array_push($produces, $order->produce_trader()->first()->produce()->first());
                }
                foreach($order->bid_order_account()->get() as $acc){
                    array_push($accs, $acc);
                }
            }

            return response([
                'orders' => $orders,
                'accs' => $accs,
                'project_bids' => $project_bids,
                'on_hand_bids' => $on_hand_bids,
                'distributors' => $distributors,
                'produce_traders' => $produce_traders,
                'produces' => $produces,
            ]);
        });

        Route::get('/reports/farms', function (){
            $user = User::find(auth()->id())->trader()->first();
            $farms = Farm::where('trader_id', $user->id)->get();
            $farm_owners = [];
            $farm_produces = [];
            $projects = [];
            foreach($farms as $farm){
                if(!in_array($farm->farm_owner()->first(), $farm_owners)){
                    array_push($farm_owners, $farm->farm_owner()->first());
                }
                foreach(DB::table('farm_produce')->where('farm_id', $farm->id)->get() as $farm_produce){
                    array_push($farm_produces, $farm_produce);
                }                
                foreach($farm->contract()->get() as $contract){
                    array_push($projects, $contract->project()->where('project_completionDate', null)->first());
                }                
            }

            return response([
                'farms' => $farms,
                'farm_owners' => $farm_owners,
                'farm_produces' => $farm_produces,
                'projects' => $projects,
            ]);
            
        });
        
        Route::get('/reports/farms/owners', function (){
            $user = User::find(auth()->id())->trader()->first();
            $farm_owners = [];
            $farm_owner_addresses = [];
            $farm_owner_contact_numbers = [];
            foreach(DB::table('owner_trader')->where('trader_id', $user->id)->get() as $owner){
                array_push($farm_owners, FarmOwner::find($owner->farm_owner_id));
                array_push($farm_owner_addresses, FarmOwner::find($owner->farm_owner_id)->farm_owner_address()->first());
                array_push($farm_owner_contact_numbers, FarmOwner::find($owner->farm_owner_id)->farm_owner_contactNum()->first());
            }
            $farms = [];
            foreach($farm_owners as $owner){
                foreach($owner->farm()->get() as $farm){
                    array_push($farms, $farm);
                }
            }
            return response([
                'farms' => $farms,
                'farm_owners' => $farm_owners,
                'farm_owner_addresses' => $farm_owner_addresses,
                'farm_owner_contact_numbers' => $farm_owner_contact_numbers,
            ]);
        });

        Route::get('/dashboard', function(){

            $user = User::find(auth()->id());
            $contracts = $user->trader()->first()->contract()->get();
            $project_ids = [];
            foreach($contracts as $contract){
                array_push($project_ids, $contract->project()->first()->id);
            }            
            $startMonth = Carbon::now()->firstOfMonth();
            $endMonth = Carbon::now()->endOfMonth();
            $totalSales = Sale::select(DB::raw('sum(sale_total), created_at'))->whereIn('project_id', $project_ids)
            ->whereBetween('created_at', [$startMonth, $endMonth])->groupBy(DB::raw('cast(created_at as DATE)'))->orderBy('created_at')->get();
            $bidOrders = BidOrder::where('trader_id', $user->trader()->first()->id)->get();
            $incomeSumm = [];
            foreach($bidOrders as $bidOrder){
                foreach($bidOrder->bid_order_account()
                ->select(DB::raw('sum(bid_order_acc_amount), created_at'))->groupBy('bid_order_id')->get() as $acc){
                    array_push($incomeSumm, $acc);
                }
            }
            return response([
                'totalSales' => $totalSales,
                'name' => Trader::where('user_id',auth()->id())->first()->trader_firstName . " " . 
                Trader::where('user_id',auth()->id())->first()->trader_lastName,
                'incomeSumm' => $incomeSumm
            ], 200);
        });

        Route::get('/income/summary', function (){
            $trader = User::find(auth()->id())->trader()->first();
            $bidOrders = BidOrder::where('trader_id', $trader->id)->get();            
            $incomeSumm = [];
            foreach($bidOrders as $bidOrder){                
                foreach($bidOrder->bid_order_account()
                ->select(DB::raw('id, bid_order_id, sum(bid_order_acc_amount), created_at'))->groupBy('bid_order_id')->orderBy('created_at')->get() as $acc){
                    array_push($incomeSumm, $acc);
                }
            }                       
            $bid_order_accounts = [];
            $produce_trader = [];
            $distributors = [];
            $produces = [];
            $contracts = [];
            $projects = [];
            $project_bids = [];
            $on_hand_bids = [];
            foreach($bidOrders as $bidOrder){
                if(!in_array($bidOrder->project_bid()->first(), $project_bids) && $bidOrder->project_bid()->first()){
                    array_push($project_bids, $bidOrder->project_bid()->first());
                }   
                else if(!in_array($bidOrder->on_hand_bid()->first(), $on_hand_bids) && $bidOrder->on_hand_bid()->first()){
                    array_push($on_hand_bids, $bidOrder->on_hand_bid()->first());
                }   
                if(!in_array($bidOrder->produce_trader()->first(), $produce_trader)){
                    array_push($produce_trader, $bidOrder->produce_trader()->first());
                }
                if(!in_array($bidOrder->distributor()->first(), $distributors)){
                    array_push($distributors, $bidOrder->distributor()->first());   
                }
                if(!in_array($bidOrder->project()->first()->contract()->first(), $contracts)){
                    array_push($contracts, $bidOrder->project()->first()->contract()->first());   
                }         
                if(!in_array($bidOrder->project()->first(), $projects)){
                    array_push($projects, $bidOrder->project()->first());                    
                }         
                foreach($bidOrder->bid_order_account()->get() as $acc){
                    array_push($bid_order_accounts, $acc);
                }
            }
            foreach($produce_trader as $p){
                if(!in_array($p->produce()->first(), $produces)){
                    array_push($produces, $p->produce()->first());
                }
            }   
            
            return response([
                'bidOrders' => $bidOrders,
                'incomeSumm' => $incomeSumm,
                'bid_order_accounts' => $bid_order_accounts,
                'produce_trader' => $produce_trader,
                'distributors' => $distributors,
                'produces' => $produces,
                'contracts' => $contracts,
                'projects' => $projects,
                'project_bids' => $project_bids,
                'on_hand_bids' => $on_hand_bids,
            ]);
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
                $farm_produces = DB::table('farm_produce')->where('farm_id', $farm_id);
                $produces = [];
                foreach($farm_produces->groupBy('produce_id')->get() as $produce){
                    array_push($produces, Produce::find($produce->produce_id));
                }
                // foreach($farm_produces as $farm_produce){
                //     $produce = ProduceTrader::find($farm_produce->produce_trader_id)->produce()->first();
                //     array_push($produces, $produce);
                // }
                return response([                    
                    'farm_produces' => $farm_produces->get(),
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
            //    $farmOwners = FarmOwner::all();; 
               $trader = Trader::where('user_id', auth()->id())->first();        
               $owners = DB::table('owner_trader')->where('trader_id', $trader->id)->get();
            //    $owners = FarmOwner::all();
           
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
            $types = [];
            foreach($produces as $produce){
                if(!in_array($produce->produce()->first(), $types)){
                    array_push($types, $produce->produce()->first());
                }
            }
            if(count($produces) > 6){
                return response([
                    'produces' => ProduceTrader::where('trader_id', $trader->id)->paginate(6),
                    'types' => $types
                ], 200);
            }
            else{
                return response([
                    'produces' => $produces,
                    'types' => $types
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
            $produces = ProduceTrader::where('trader_id', $trader->id)->groupBy('produce_id')->get();   //1, 2, 3
            $container = [];
            foreach($produces as $produce){
                array_push($container, $produce->produce()->first());
            }
            $filteredProduces = [];
            $farm_produces = DB::table('farm_produce')->where('farm_id', $farm_id)->get();
            foreach($container as $producee){
                $check = true;
                foreach($farm_produces as $farm_produce){
                    if($farm_produce->produce_id == $producee->id){
                        $check = false;
                        break 1;
                    }
                    else{
                        $check = true;
                    }
                }
                if($check){
                    array_push($filteredProduces, $producee);
                }
            }
            $produce_trader = [];
            foreach($filteredProduces as $produceee){
                array_push($produce_trader, ProduceTrader::where([['produce_id', $produceee->id],['trader_id', User::find(auth()->id())->trader()->first()->id]])->first());
            }
            // $farm_produces = DB::table('farm_produce')->where('farm_id', $farm_id)->whereIn('produce_id', $container)->groupBy('produce_id')->get();     //1, 2      
            // $filteredProduces = [];
            // for($i = 0; $i < count($produces); $i++){
            //     $check = true;
            //     for($ii = 0; $ii < count($farm_produces); $ii++){
            //         if($produces[$i]->produce_id == $farm_produces[$ii]->produce_id){
            //             $check = false;
            //             break;
            //         }
            //         else{
            //             $check = true;
            //         }
            //     }
            //     if($check){
            //         array_push($filteredProduces, $produces[$i]);
            //     }
            // }
            return response([
                'produces' => $filteredProduces,
                'produce_trader' => $produce_trader
            ], 200);
        });

        Route::get('/produce/details/{id}', function ($id){
            $trader = Trader::where('user_id', auth()->id())->first();
            $produce_trader = Produce::find($id)->produce_trader()->where('trader_id', $trader->id)->get();
            $produce_ids = [];            
            foreach($produce_trader as $produce){
                array_push($produce_ids, $produce->id);
            }
            $produce_yields = ProduceYield::whereIn('produce_trader_id', $produce_ids)->latest()->get();
            $projects = [];
            $farms = [];
            $farm_owners = [];
            $contracts = [];
            foreach($produce_yields as $yield){
                if(!in_array($yield->project()->first(), $projects)){
                    array_push($projects, $yield->project()->first());
                    array_push($contracts, $yield->project()->first()->contract()->first());
                }
                $farm = $yield->project()->first()->contract()->first()->farm()->first();
                if(!in_array($farm, $farms)){
                    array_push($farms, $farm);
                    array_push($farm_owners, $farm->farm_owner()->first());
                }

            }
            return response([
                'produce' => Produce::find($id),
                'produce_trader' => $produce_trader,
                'produce_yields' => $produce_yields,
                'projects' => $projects,
                'contracts' => $contracts,
                'farms' => $farms,
                'farm_owners' => $farm_owners,
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
                $projects = [];
                foreach($contracts->get() as $contract){
                    $project = $contract->project()->first();                    
                    $farm = $contract->farm()->first();
                    $farm_owner = $farm->farm_owner()->first();
                    $produce = $contract->produce()->first();
                    $share = $contract->contract_share()->first();
                    $start_date = $contract->project()->first();
                    array_push($projects, $project);
                    array_push($farms, $farm);
                    array_push($farm_owners, $farm_owner);
                    array_push($produces, $produce);
                    array_push($shares, $share);
                    array_push($start_dates, $start_date);
                }                        
                if(count($contracts->get()) > 6){
                    return response([
                        'projects' => $contracts->paginate(6),
                        'projectss' => $projects,
                        'farms' => $farms,
                        'farm_owners' => $farm_owners,
                        'produces' => $produces,
                        'shares' => $shares,
                        'start_dates' => $start_dates,
                    ], 200);
                } 
                return response([
                    'projects' => $contracts->get(),
                    'projectss' => $projects,
                    'farms' => $farms,
                    'farm_owners' => $farm_owners,
                    'produces' => $produces,
                    'shares' => $shares,
                    'start_dates' => $start_dates,
                ], 200);   
            });

            Route::get('/{id}', function ($id){          
                $stockOut = StockOut::where('project_id', $id)->get();
                $supply_ids = [];
                foreach($stockOut as $stock){
                    if(!in_array($stock->supply_id, $supply_ids)){
                        array_push($supply_ids, $stock->supply_id);
                    }
                }
                $supplies = Supply::whereIn('id', $supply_ids)->get();
                $produce_yields = ProduceYield::where('project_id', $id)->get();
                $produce_inventory = [];
                foreach($produce_yields as $yield){
                    array_push($produce_inventory, $yield->produce_inventory()->first());
                }
                return response([
                    'farm' => Project::find($id)->contract()->first()->farm()->first(),
                    'contract' => Project::find($id)->contract()->first(),
                    'project' => Project::find($id),
                    'share' => Project::find($id)->contract()->first()->contract_share()->first(),
                    'farm_owner' => Project::find($id)->contract()->first()->farm()->first()->farm_owner()->first(),
                    'produce' => Project::find($id)->contract()->first()->produce()->first(),
                    'history' => DB::table('project_status_history')->where('project_id', $id)->get(),
                    'expenditures' => Expenditure::where('project_id', $id)->get(),
                    'stockOut' => StockOut::where('project_id', $id)->get(),
                    'supplies' => $supplies,
                    'profit_sharing' => ProfitSharing::where('project_id', $id)->first(),
                    'refunds' => Refund::where('project_id', $id)->get(),
                    'produce_yields' => $produce_yields,
                    'produce_inventory' => $produce_inventory,
                ], 200);
            });
            
            Route::patch('/{id}', [ProjectController::class, 'update']);

        });

        Route::prefix('bid/orders')->group(function (){
            Route::get('/', function() {
                $trader = Trader::where('user_id', auth()->id())->first();
                $orders = BidOrder::where('trader_id', $trader->id);
                $idssss = [];
                $idsss = [];
                foreach($orders->get() as $order){
                    array_push($idssss, $order->id);
                    array_push($idsss, $order->produce_trader_id);
                }
                $project_bids = ProjectBid::whereIn('bid_order_id', $idssss)->get();
                $on_hand_bids = OnHandBid::whereIn('bid_order_id', $idssss)->get();
                $contracts = Contract::where('trader_id', $trader->id)->get();
                $ids = [];                
                foreach($contracts as $contract){
                    array_push($ids, $contract->id);
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

            Route::get('/filter', function (){
                $trader = Trader::where('user_id', auth()->id())->first();
                $orders = BidOrder::select(DB::raw('distributor_id, count(id), max(created_at)'))->where([
                    ['trader_id', $trader->id],                    
                ])->groupBy('distributor_id');
                $distributors = [];
            
                foreach($orders->get() as $order){
                    array_push($distributors, $order->distributor()->first());
                }                

                if(count($orders->get()) > 6){
                    return response([
                        'orders' => $orders->paginate(6),
                        'distributors' => $distributors,
                    ]);
                }
                else{
                    return response([
                        'orders' => $orders->get(),
                        'distributors' => $distributors,            
                    ]);
                }            
            });

            Route::get('/{id}', function($id){
                $bidOrder = BidOrder::find($id);
                $produce = $bidOrder->produce_trader()->first()->produce()->first();
                $distributor = $bidOrder->distributor()->first();
                $delivery = Delivery::where('bid_order_id', $id)->first();
                $contract = $bidOrder->project()->first()->contract()->first();
                $project_bid = null;
                $on_hand_bid = null;
                // $bid_order_acc = $bidOrder->bid_order_account()->latest()->first();
                $bid_order_acc = $bidOrder->bid_order_account()->latest()->get();
                $produce_yield = null;
                $refund = null;
                $dist_address = $distributor->distributor_address()->first();
                if($bidOrder->project_bid()->first()){
                    $project_bid = $bidOrder->project_bid()->first();
                }
                else if($bidOrder->on_hand_bid()->first()){
                    $on_hand_bid = $bidOrder->on_hand_bid()->first();
                    // $produce_yield = $bidOrder->project()->first()->produce_yield()->first();
                }
                if(ProduceYield::where([['project_id', $bidOrder->project_id], ['produce_trader_id', $bidOrder->produce_trader_id]])->first()){
                    $produce_yield = ProduceYield::where([['project_id', $bidOrder->project_id], ['produce_trader_id', $bidOrder->produce_trader_id]])->first();
                    // $produce_yield = $bidOrder->project()->first()->produce_yield()->first();
                }
                if($bidOrder->refund()->first()){
                    $refund = $bidOrder->refund()->first();
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
                    'distributor_contactNum' => $distributor->distributor_contactNum()->get(),
                    'produce_yield' => $produce_yield,
                    'refund' => $refund,
                    'dist_address' => $dist_address,
                    'delivery' => $delivery
                ], 200);
            });


            Route::get('/{dist_id}/bids', function ($dist_id) {
                $orders = BidOrder::where('distributor_id', $dist_id)->get();
                $on_hand_bids = [];
                $project_bids = [];
                $produce_trader = [];
                foreach($orders as $order){
                    if($order->on_hand_bid()->first()){
                        array_push($on_hand_bids, $order->on_hand_bid()->first());
                    }
                    else if($order->project_bid()->first()){
                        array_push($project_bids, $order->project_bid()->first());
                    }
                    array_push($produce_trader, $order->produce_trader()->first());
                }

                $distributor = Distributor::find($dist_id);
                return response([
                    'orders' => $orders,
                    'on_hand_bids' => $on_hand_bids,
                    'project_bids' => $project_bids,
                    'distributor' => $distributor,
                    'produce_trader' => $produce_trader,                    
                ]);
            });


        });

        Route::get('/harvest/{id}/details', function($id) {
            $yield = ProduceYield::where('project_id', $id)->get();
            $bidOrders = BidOrder::where([['project_id', $id], ['bid_order_status_id', 4]])->get(); 
            $distributors = [];
            $project_bids = [];
            $on_hand_bids = [];
            $contactNums = [];   
            $produce_trader = [];         
            foreach(BidOrder::where('project_id', $id)->get() as $bidOrder) {
                array_push($distributors, $bidOrder->distributor()->first());
                array_push($contactNums, $bidOrder->distributor()->first()->distributor_contactNum()->first());                
                if($bidOrder->project_bid()->first()){
                    array_push($project_bids, $bidOrder->project_bid()->first());
                }
                else if($bidOrder->on_hand_bid()->first()){
                    array_push($on_hand_bids, $bidOrder->on_hand_bid()->first());
                }                
            }
            $produce = null;
            $farm = null;
            if(count($yield) > 0){
                $produce = $yield[0]->produce_trader()->first()->produce()->first();
                $farm = $yield[0]->project()->first()->contract()->first()->farm()->first();
            }
            else if(count($bidOrders) > 0){
                $produce = $bidOrders[0]->produce_trader()->first()->produce()->first();
                $farm = $bidOrders[0]->project()->first()->contract()->first()->farm()->first();
            }
            else{
                $produce = Project::find($id)->contract()->first()->produce()->first();
                $farm = Project::find($id)->contract()->first()->farm()->first();
            }  
            $trader = Trader::where('user_id', auth()->id())->first();                 
            
            foreach(ProduceTrader::where([
                ['produce_id', $produce->id],
                ['trader_id', $trader->id]                    
            ])->get() as $p){
                array_push($produce_trader, $p); 
            }  
            $container = $produce->produce_trader()->where('trader_id', Trader::where('user_id', auth()->id())->first()->id)->get();
            $produce_list = [];
            foreach($container as $p){
                // if($p->produce_yield()->where([['project_id', $id]])->first()->produce_yield_qtyHarvested > 0){
                    array_push($produce_list, $p);
                // }
            }           
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
                    'on_hand_bids' => $on_hand_bids,
                    'contract' => Project::find($id)->contract()->first(),
                    'produce_list' => $produce_list
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
                'on_hand_bids' => $on_hand_bids,
                'contract' => Project::find($id)->contract()->first(),
                'produce_list' => $produce_list
            ], 200);             
                                    
        });

        Route::get('/delivery/form/{id}/produce/{produce_id}', function($id, $produce_id){
            $bid_order = BidOrder::find($id);
            $project_bid = $bid_order->project_bid()->first();
            $on_hand_bid = $bid_order->on_hand_bid()->first();
            $distributor = $bid_order->distributor()->first();
            $dist_contactNum = $distributor->distributor_contactNum()->first();            
            $contract = $bid_order->project()->first()->contract()->first();
            $produce_trader = ProduceTrader::find($produce_id);
            $yield = $bid_order->project()->first()->produce_yield()->first();            
            $farm = $contract->farm()->first();
            $dist_address = $distributor->distributor_address()->first();
            $produce = $produce_trader->produce()->first();
            $bid_order_acc = $bid_order->bid_order_account()->latest()->first();

            return response([
                'bid_order' => $bid_order,
                'project_bid' => $project_bid,
                'on_hand_bid' => $on_hand_bid,
                'distributor' => $distributor,
                'dist_contactNum' => $dist_contactNum,
                'produce_trader' => $produce_trader,
                'contract' => $contract,
                'yield' => $yield,
                'farm' => $farm,
                'dist_address' => $dist_address,
                'produce' => $produce,
                'bid_order_acc' => $bid_order_acc
            ]);
        });

        
        Route::get('/harvest/{id}/inventory', function($id){
            $project = Project::find($id);
            $contract = $project->contract()->first();
            $produce = $contract->produce()->first();            
            $farm = Project::find($id)->contract()->first()->farm()->first();
            $farm_owner = $farm->farm_owner()->first();
            $produce_yields = $project->produce_yield()->get();
            $produce_inventories = [];            
            foreach($produce_yields as $yield){
                array_push($produce_inventories, $yield->produce_inventory()->first());                
            }           
            $sales = $project->sale()->get();
            $bid_orders = Project::find($id)->bid_order()->get();

            return response([
                'project' => $project,
                'contract' => $contract,
                'produce' => $produce,
                'farm' => $farm,
                'farm_owner' => $farm_owner,
                'produce_yields' => $produce_yields,
                'produce_inventories' => $produce_inventories,
                'sales' => $sales,
                'bid_orders' => $bid_orders,
            ], 200);
        });







        //Route::put('/bid/{id}', [ProjectBidController::class, 'approveProjectBid']);        
    });

    Route::group(['middleware' => ['role:distributor|trader']], function () {
        Route::prefix('messages/{id}')->group(function (){
            Route::controller(ChatController::class)->group(function (){
                Route::get('/', 'readMessages');
                Route::post('/add', 'addMessage');
            });
        });
 
        Route::prefix('bid/project/{id}')->group(function () {
            Route::controller(ProjectBidController::class)->group(function () {
                Route::put('/approve', 'approveProjectBid');
                Route::post('/cancel', 'cancelProjectBid');
                Route::post('/payment', 'paymentProjectBid');
            });
            Route::put('/deliver', [DeliveryController::class, 'deliverProjectBid']);
        });

        Route::prefix('bid/onhand/{id}')->group(function () {
            Route::controller(OnHandBidController::class)->group(function () {
                Route::put('/approve', 'approveOnHandBid');
                Route::post('/cancel', 'cancelOnHandBid');
                Route::post('/payment', 'paymentOnHandBid');
            });
            Route::put('/deliver', [DeliveryController::class, 'deliverOnHandBid']);
        });
    });

    Route::group(['middleware' => ['role:distributor']], function () {



        Route::get('/catalog', function () {
            //$filteredContracts = Contract::select('*')->whereNotIn('project_status_id', [1, 3])->groupBy('produce_id');
            $contracts = Contract::latest()->get();
            $projects = [];
            $produces = Produce::paginate(8);
            $farm_produces = DB::table('farm_produce')->get();
            $produce_yields = [];
            $produce_inventories = [];
            $traders = [];
            $produce_trader = [];
            foreach($contracts as $contract){               
                // $project = $contract->project()->where('project_floweringStart', '<=', Carbon::now())->first();
                $project = $contract->project()->where([['project_harvestableEnd', '>=', Carbon::now()->addMonths(1)], ['project_commenceDate', '<=', Carbon::now()]])->first();                
                // $project = $contract->project()->first();   
                $trader = $contract->trader()->first();             
                if($project){                  
                    if(count($project->produce_yield()->orderBy('produce_yield_dateHarvestTo', 'desc')->get()) > 0){
                        array_push($produce_yields, $project->produce_yield()->orderBy('produce_yield_dateHarvestTo', 'desc')->get());                        
                    }                
                    foreach($project->produce_yield()->orderBy('produce_yield_dateHarvestTo', 'desc')->get() as $yield){
                        if($yield->produce_inventory()->first()->produce_inventory_qtyOnHand > 0){
                            array_push($produce_inventories, $yield->produce_inventory()->first());
                        }                        
                    }                    
                    if($project->project_status_id == '2' && !$project->produce_yield()->first()){
                        array_push($projects, $project);
                    }
                }
                foreach($farm_produces as $p){
                    $check = true;
                    foreach($produce_trader as $pp){
                        if($p->produce_trader_id == $pp->id){
                            $check = false;
                            break;
                        }
                        else{
                            $check = true;
                        }
                    }
                    if($check){
                        array_push($produce_trader, ProduceTrader::find($p->produce_trader_id));
                    }                        
                }
                $check = true;
                foreach($traders as $t){
                    if($t->id == $trader->id){
                        $check = false;
                        break;
                    }
                    else{
                        $check = true;
                    }
                }
                if($check){
                    array_push($traders, $trader);
                }
                //$produce = $contract->produce()->first();
                //array_push($produces, $produce);
            }
            return response([
                'produces' => $produces,                
                'produce_trader' => $produce_trader,                
                'contracts' => $contracts,
                'projects' => $projects,                
                'farm_produce' => $farm_produces,
                'produce_yields' => $produce_yields,
                'produce_inventories' => $produce_inventories,
                'traders' => $traders,
            ], 200);
        });

        Route::get('/bid/history/{email}', function($email){
            $distributor = Distributor::where('distributor_email', $email)->first();
            $orders = BidOrder::where('distributor_id', $distributor->id)->get();
            $produce_trader = [];
            $produces = [];
            $farms = [];
            foreach($orders as $order){
                array_push($produce_trader, $order->produce_trader()->first());
                array_push($produces, $order->produce_trader()->first()->produce()->first());
                array_push($farms, $order->project()->first()->contract()->first()->farm()->first());
            }
            $ids = [];
            $idss = [];
            foreach($produce_trader as $p){
                if(!in_array($p->id, $ids)){
                    array_push($ids, $p->id);
                }                
            }
            foreach($farms as $farm){
                if(!in_array($farm->id, $idss)){
                    array_push($idss, $farm->id);
                }                
            }            
            $farm_produce = [];
            foreach($ids as $id){
                foreach($idss as $i){
                    array_push($farm_produce, DB::table('farm_produce')->where([['farm_id', $i], ['produce_trader_id', $id]])->first());
                }
            }
            $produce_yields = [];
            // foreach($farm_produce as $p){
            //     if(ProduceInventory::find($p->produce_inventory_id)){
            //         array_push($produce_yields, ProduceInventory::find($p->produce_inventory_id)->produce_yield()->first());
            //     }                
            // }
            $project_bids = [];
            $on_hand_bids = [];
            $projects = [];
            $contracts = [];
            $traders = [];
            $trader_contactNums = [];
            $bid_order_accs = [];
            $deliveries = [];
            $refunds = [];
            foreach($orders as $order){        
                $project_bid = $order->project_bid()->first();
                $on_hand_bid = $order->on_hand_bid()->first();
                $project = $order->project()->first();
                $contract = $project->contract()->first();
                $trader = $order->trader()->first();
                $refund = $order->refund()->first();
                // $produce = $contract->produce_trader()->first();
                $contactNums = $trader->trader_contactNum()->get();
                $bid_order_acc = $order->bid_order_account()->latest()->first();
            
                $delivery = Delivery::where('bid_order_id', $order->id)->first();
                // array_push($produces, $produce);
                if($project_bid){
                    array_push($project_bids, $project_bid);
                }
                if($on_hand_bid){                
                    array_push($on_hand_bids, $on_hand_bid);
                }                                
                if($refund){                
                    array_push($refunds, $refund);
                }
                if($bid_order_acc){
                    array_push($bid_order_accs, $bid_order_acc);
                }                                
                array_push($projects, $project);
                array_push($contracts, $contract);
                array_push($traders, $trader);
                array_push($trader_contactNums, $contactNums);
                // array_push($bid_order_accs, $bid_order_acc);
                if($delivery){
                    array_push($deliveries, $delivery);
                }                
            }
      
            return response([
                'orders' => BidOrder::where('distributor_id', $distributor->id)->get(),
                'produce_trader' => $produce_trader,
                'project_bids' => $project_bids,
                'on_hand_bids' => $on_hand_bids,
                'projects' => $projects,
                'contracts' => $contracts,
                'traders' => $traders,                
                'trader_contactNums' => $trader_contactNums,
                'bid_order_accs' => $bid_order_accs,
                'produces' => $produces,
                'deliveries' => $deliveries,
                'farm_produce' => $farm_produce,
                'produce_yields' => $produce_yields,
                'refunds' => $refunds
            ], 200);
        });

        Route::get('/project/{id}', function($id){
            $produce_id = Contract::find($id)->produce_id;
            return response([
                'contract' => Contract::find($id),
                'project' => Contract::find($id)->project()->first(),
                'allProjects' => Contract::where('produce_id', $produce_id)->get()
            ], 200);
        });


        Route::prefix('bid/project')->group(function () {
            Route::get('/{id}', function($id){          
                $contract = Contract::find($id);
                $trader = $contract->trader()->first();
                $project = $contract->project()->first();
                $produce_trader = ProduceTrader::where([
                    ['produce_id', $contract->produce_id],
                    ['trader_id', $contract->trader_id]
                ])->get();
                $chart_data = DB::table('bid_orders')->select(DB::raw('avg(order_negotiatedPrice), order_grade, created_at'))->where([
                    ['project_id', $id],
                    ['order_negotiatedPrice', '!=', null],                    
                ])->groupBy(DB::raw('cast(created_at as DATE), order_grade'))->get();
                return response([
                    'trader_id' => $trader->id,
                    'trader_name' => $trader->trader_firstName . ' ' . $trader->trader_lastName,
                    'prod_name' => $contract->produce()->first()->prod_name,
                    'prod_type' => $contract->produce()->first()->prod_type,
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
                    'farm_name' => $contract->farm()->first()->farm_name,
                    'produce_trader' => $produce_trader,
                    'chart_data' => $chart_data
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
            Route::get('/{farm_id}/{produce_trader_id}', function($farm_id, $produce_trader_id){
                $selectedProduce = DB::table('farm_produce')->where([['farm_id', $farm_id], ['produce_trader_id', $produce_trader_id]])->first();
                if($selectedProduce->on_hand_qty <= 0){
                    return response([
                        'error' => 'Produce Out of Stock!'
                    ], 400);
                }
                $chart_data = ProduceYield::where([                
                    ['produce_trader_id', $produce_trader_id]
                ])->get();
                $farm_produce = DB::table('farm_produce')->where([['farm_id', $farm_id], ['produce_id', $selectedProduce->produce_id]])->get();
                $contracts = Contract::where([['farm_id', $farm_id], ['produce_id', $selectedProduce->produce_id]])->oldest()->get();
                // $contract = ProduceInventory::find($selectedProduce->produce_inventory_id)->produce_yield()->first()->project()->first()->contract()->first(); 
                $contract = null;
                $produce_yields = [];
                $container = [];
                $produce_inventories = [];  
                $trader = ProduceTrader::find($produce_trader_id)->trader()->first();
                $trader_contactNum = $trader->trader_contactNum()->first();     
                $numOfGrades = ProduceTrader::find($produce_trader_id)->produce_numOfGrades;
                $classes = ['A', 'B', 'C'];                                                          
                foreach($contracts as $c){
                    $contract = $c;                
                    $trader = $contract->trader()->first();                                   
                    foreach(ProduceYield::where('project_id', (int)$c->project()->first()->id)->get() as $yield){                        
                        if($numOfGrades == 3){                           
                            for($i = 0; $i < count($classes); $i++){
                                if($yield->produce_yield_class == $classes[$i] && $yield->produce_inventory()->first()->produce_inventory_qtyOnHand > 0){
                                    array_push($produce_yields, $yield);
                                    array_push($produce_inventories, $yield->produce_inventory()->first());                                
                                    unset($classes[$i]);
                                    $classes = array_values($classes);                                                                    
                                }
                            }
                            if(count($produce_yields) == 3){     
                                for($ii = 0; $ii < count($produce_yields); $ii++){
                                    if($produce_yields[$ii]->produce_yield_class == 'A'){
                                        array_push($container, $produce_yields[$ii]);
                                        break;
                                    }                                    
                                }                                                       
                                for($ii = 0; $ii < count($produce_yields); $ii++){
                                    if($produce_yields[$ii]->produce_yield_class == 'B'){
                                        array_push($container, $produce_yields[$ii]);
                                        break;
                                    }                                    
                                }                                                       
                                for($ii = 0; $ii < count($produce_yields); $ii++){
                                    if($produce_yields[$ii]->produce_yield_class == 'C'){
                                        array_push($container, $produce_yields[$ii]);
                                        break;
                                    }                                    
                                }                                                                                      
                                $produce_yields = array_unique($container);                 
                            }                            
                        }
                        else{
                            if($yield->produce_inventory()->first()->produce_inventory_qtyOnHand > 0){
                                array_push($produce_yields, $yield);
                                array_push($produce_inventories, $yield->produce_inventory()->first());
                            }                            
                        }                     
                    }
                }                                                           
                $produce = Produce::find($selectedProduce->produce_id);                
                
                return response([
                    'selectedProduce' => $selectedProduce,
                    'farm_produce' => $farm_produce,
                    'contract' => $contract,
                    'trader' => $trader,
                    'produce_yields' => $produce_yields,
                    'trader_contactNum' => $trader_contactNum,
                    'produce' => $produce,
                    'produce_inventories' => $produce_inventories,
                    'chart_data' => $chart_data
                ], 200);
            });
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
