<?php

namespace App\Http\Controllers;

use App\Models\BidOrder;
use App\Models\BidOrderAccount;
use App\Models\BidOrderStatus;
use App\Models\Distributor;
use App\Models\Message;
use App\Models\ProduceTrader;
use App\Models\ProduceYield;
use App\Models\Project;
use App\Models\ProjectBid;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProjectBidController extends Controller
{
    public function addProjectBid(Request $request)
    {        
        $order = $request->validate([
            'trader_id' => 'required|exists:traders,id',
            'project_id' => 'required|exists:projects,id',
            'produce_trader_id' => 'required|exists:produce_trader,id',
            'order_grade' => 'string|nullable',
            'exp_dateHarvest' => 'date|required',
            'order_dateNeededFrom' => 'required|date|after:exp_dateHarvest',
            'order_dateNeededTo' => 'required|date|after:order_dateNeededFrom',
            'order_initialPrice' => 'required|numeric',
            'project_bid_minQty' => 'required|numeric|lt:project_bid_maxQty',
            'project_bid_maxQty' => 'required|numeric',
            'project_bid_total' => 'required'
        ]);

        //$yield = ProduceYield::where([['project_id', $request->project_id], ['produce_yield_class', '=', $request->order_grade]])->first();
        if (!$order) {
            return response([
                'error' => 'Invalid Order!'
            ], 400);
        }

        $order = BidOrder::where([
            ['trader_id', $request->trader_id],
            ['distributor_id', Distributor::where('user_id', auth()->id())->first()->id], 
            ['project_id', $request->project_id],
            ['produce_trader_id', $request->produce_trader_id],                    
        ])->first();

        if ($order) {
            return response([
                'error' => 'Order has already been sent!'
            ], 400);
        }
        // $produces = ProduceTrader::find($request->produce_trader_id)->produce_trader()->get();        
        // if(count($produces) > 0){
        //    for($i = 0; $i < count($produces); $i){
        //         if($request->produce_trader_id == $produces[$i]->id){
        //             if($i == 0){
        //                 $request->order_grade = 'A';
        //                 break;
        //             }
        //             else if($i == 1){
        //                 $request->order_grade = 'B';
        //                 break;
        //             }
        //             else if($i == 2){
        //                 $request->order_grade = 'C';
        //                 break;
        //             }
        //         }
        //     }
        // }
        $newOrder = BidOrder::create([
            'trader_id' => $request->trader_id,
            'distributor_id' => User::find(auth()->id())->distributor()->first()->id,
            'bid_order_status_id' => 1,
            'order_grade' => $request->order_grade,
            'project_id' => $request->project_id,
            'produce_trader_id' => $request->produce_trader_id,
            'order_dateNeededFrom' => $request->order_dateNeededFrom,
            'order_dateNeededTo' => $request->order_dateNeededTo,
            'order_initialPrice' => $request->order_initialPrice,
            'order_traderPrice' => Project::find($request->project_id)->contract()->first()->contract_estimatedPrice
        ]);

        ProjectBid::create([
            'bid_order_id' => $newOrder->id,
            'project_bid_minQty' => $request->project_bid_minQty,
            'project_bid_maxQty' => $request->project_bid_maxQty,
            'project_bid_total' => $request->project_bid_total,
        ]);

        Message::create([
            'trader_id' => $request->trader_id,
            'distributor_id' => User::find(auth()->id())->distributor()->first()->id,
            'message_sentBy' => 'distributor',
            'message_body' => "Hello there!\nI am interested in bidding your ".ProduceTrader::find($request->produce_trader_id)->prod_name." for ".$request->project_bid_maxQty." kg/kgs\nfrom ".Project::find($request->project_id)->contract()->first()->farm_name.". Please consider this offer and we're hoping for bidding negotiations here. Thank you!"
        ]);

        return response([
            'message' => 'Order Successful!'
        ], 200);
    }

    public function approveProjectBid(Request $request, $id)
    {
        $user = User::find(auth()->id());
        if (BidOrder::find($id)) {
            $bidOrder = BidOrder::find($id);
            if ($user->hasRole('trader')) {
                $order = $request->validate([
                    'order_negotiatedPrice' => 'required|numeric|gt:0',
                    'order_datePlaced' => 'required|date', //from created_at table
                    'project_bid_total' => 'required',
                    'order_dpDueDate' => 'required|date|after:order_datePlaced',
                    'order_dpAmount' => 'required|numeric'
                ]);

                if (!$order) {
                    return response([
                        'error' => 'Invalid Order Approval!'
                    ], 400);
                }
                ProjectBid::where('bid_order_id', $id)->update([
                    'project_bid_total' => explode(' - ', $request->project_bid_total)[1]
                ]);
                if ($bidOrder->bid_order_status_id == 2) {
                    return response([
                        'error' => 'Order Already Approved!'
                    ], 400);
                } else {
                    $bidOrder->order_dpDueDate = $request->order_dpDueDate;
                    $bidOrder->order_negotiatedPrice = $request->order_negotiatedPrice;
                    $bidOrder->bid_order_status_id = 2;
                    $bidOrder->order_dpAmount = $request->order_dpAmount;
                    $bidOrder->save();
                }  
                Message::create([
                    'trader_id' => User::find(auth()->id())->trader()->first()->id,
                    'distributor_id' => $bidOrder->distributor_id,
                    'message_sentBy' => 'trader',
                    'message_body' => "Order # ".$bidOrder->id." has now been approved! Please confirm the negotiated price and quantity in your order history page."
                ]);              

                return response([
                    'message' => 'Order Approved!'
                ], 200);
            } else { // Distributor Role     
                if ($bidOrder->bid_order_status_id == 3) {
                    return response([
                        'error' => 'Order Already Confirmed!'
                    ], 400);
                } else {
                    $bidOrder->bid_order_status_id = 3;
                    $bidOrder->save();
                }

                Message::create([
                    'trader_id' => $bidOrder->trader_id,
                    'distributor_id' => User::find(auth()->id())->distributor()->first()->id,
                    'message_sentBy' => 'distributor',
                    'message_body' => "Order # ".$bidOrder->id." has now been confirmed! Please wait for the payment details to sent."
                ]); 

                return response([
                    'message' => 'Order Confirmed!'
                ], 200);
            }
        } else {
            return response([
                'error' => 'Bid Order Not Found!'
            ], 400);
        }
    }

    public function cancelProjectBid($id){
        $user = User::find(auth()->id());
        $trader = $user->trader()->first();
        $distributor = $user->distributor()->first();
        $bidOrder = BidOrder::find($id);
        $status = $bidOrder->bid_order_status_id;
        if($trader){
            if($status == 1){
                $bidOrder->delete();
                return response([
                    'message' => "Bid Order Cancelled!"
                ]);
            }
            else if($status == 3){
                $acc = $bidOrder->bid_order_account()->latest()->first();
                if($acc){
                    $acc->delete();
                    return response([
                        'message' => "Payment Cancelled!"
                    ]);
                }
            } 
            else if($status == 6){
                $acc = $bidOrder->bid_order_account()->latest()->first();
                if($acc){
                    $acc->delete();
                    $bidOrder->delivery()->update([
                        'delivery_receivedBy' => null,
                        'delivery_contactNum' => null
                    ]);
                    $bidOrder->update([
                        'bid_order_status_id' => 5
                    ]);
                    return response([
                        'message' => "Payment Cancelled!"
                    ]);
                }
            }
            else if($status == 7){                                
                $refund = $bidOrder->refund()->first();    
                $bidOrderStatus = BidOrderStatus::where('bid_order_status', $refund->refund_statusFrom)->first();
                $bidOrder->update([
                    'bid_order_status_id' => $bidOrderStatus->id
                ]);            
                $refund->delete();                
                return response([
                    'message' => "Refund Denied!"
                ]);
            }
        }
        else if($distributor){
            if($status == 2){
                $bidOrder->delete();
                return response([
                    'message' => "Bid Order Cancelled!"
                ]);
            }
            else if($status == 8){                
                $refund = $bidOrder->refund()->first();
                $bidOrder->bid_order_account()->latest()->first()->delete();
                $dateRefundPlaced = Carbon::create($refund->created_at);
                $dateRefundPlaced->hour = 0;
                $dateRefundPlaced->minute = 0;
                $dateRefundPlaced->second = 0;
                $dateHarvest = Carbon::create($bidOrder->project()->first()->project_harvestableEnd);
                $dateHarvest->hour = 0;
                $dateHarvest->minute = 0;
                $dateHarvest->second = 0;
                if($dateRefundPlaced->greaterThanOrEqualTo($dateHarvest)){                    
                    $refund->update([
                        'refund_amount' => null
                    ]);                    
                }               
                if($bidOrder->project()->first()->project_status_id == 5){
                    $bidOrder->refund()->delete();
                    $bidOrder->update([
                        'bid_order_status_id' => 4
                    ]);
                }
                else{
                    $bidOrder->update([
                        'bid_order_status_id' => 7
                    ]);
                }
                
                return response([
                    'message' => "Refund Denied!"
                ]);
            }
        }
        else {
            return response([
                'error' => 'Unauthorized Access!'
            ], 400);
        }
    }

    public function paymentProjectBid(Request $request, $id)
    {
        $user = User::find(auth()->id());
        if (BidOrder::find($id)) {
            $acc = BidOrderAccount::where([['bid_order_id', '=', $id], ['bid_order_acc_type', '=', 'First Payment']])->first();
            $status = BidOrder::find($id)->bid_order_status_id;
            if ($user->hasRole('distributor')) {
                if ($status == 3 && !$acc) {
                    $order = $request->validate([
                        'bid_order_acc_paymentMethod' => 'required|string',
                        'bid_order_acc_type' => 'required|string',
                        'bid_order_acc_amount' => 'required|numeric',
                        'bid_order_acc_bankName'  => 'required_if:bid_order_acc_paymentMethod,==,Bank',
                        'bid_order_acc_accNum' => 'required_if:bid_order_acc_paymentMethod,==,Bank',
                        'bid_order_acc_accName' => 'required',
                        'bid_order_acc_remarks' => 'nullable|string',
                        'bid_order_acc_datePaid' => 'required|date'
                    ]);

                    if (!$order) {
                        return response([
                            'error' => 'Invalid Order!'
                        ], 400);
                    }
                    $datePaid = new Carbon($request->bid_order_acc_datePaid);
                    $datePlaced = new Carbon(BidOrder::find($id)->created_at);
                    $dpDueDate = new Carbon(BidOrder::find($id)->order_dpDueDate);
                    $datePlaced->hour = 0;
                    $datePlaced->minute = 0;
                    $datePlaced->second = 0;
                    $dpDueDate->hour = 0;
                    $dpDueDate->minute = 0;
                    $dpDueDate->second = 0;             
                    if($datePaid->isBefore($datePlaced) || $datePaid->isAfter($dpDueDate) || $datePaid->equalTo($dpDueDate)){
                        return response([
                            'error' => 'Invalid Date!'
                        ], 400);
                    }                    

                    BidOrderAccount::create([
                        'bid_order_id' => $id,
                        'bid_order_acc_type' => $request->bid_order_acc_type,
                        'bid_order_acc_paymentMethod' => $request->bid_order_acc_paymentMethod,
                        'bid_order_acc_bankName' => $request->bid_order_acc_bankName,
                        'bid_order_acc_accNum' => $request->bid_order_acc_accNum,
                        'bid_order_acc_accName' => $request->bid_order_acc_accName,
                        'bid_order_acc_amount' => $request->bid_order_acc_amount,
                        'bid_order_acc_remarks' => $request->bid_order_acc_remarks,
                        'bid_order_acc_datePaid' => $request->bid_order_acc_datePaid,
                    ]);
                    $trader = BidOrder::find($id)->project()->first()->contract()->first()->trader()->first();
                    Message::create([
                        'trader_id' => $trader->id,
                        'distributor_id' => User::find(auth()->id())->distributor()->first()->id,
                        'message_sentBy' => 'distributor',
                        'message_body' => "First Payment for Bid Order # ".$id." has been already sent! Please check and verify with your account whether the payment has been received or not."                        
                    ]);

                    return response([
                        'message' => 'First Payment Delivered!'
                    ], 200);
                } else if ($status == 3 && $acc) {
                    return response([
                        'error' => 'First Payment Already Delivered!'
                    ], 400);
                } else {
                    return response([
                        'error' => 'Error!'
                    ], 400);
                }
            } else { // trader
                $order = BidOrder::find($id);
                if ($order->bid_order_status_id == 4) {
                    return response([
                        'error' => 'First Payment Already Confirmed!'
                    ], 400);
                } else if ($order->bid_order_status_id < 4) {
                    $order->bid_order_status_id = 4;
                    $order->save();
                } else {
                    return response([
                        'error' => 'Error!'
                    ], 400);
                }

                Message::create([
                    'trader_id' => User::find(auth()->id())->trader()->first()->id,
                    'distributor_id' => $order->distributor_id,
                    'message_sentBy' => 'trader',
                    'message_body' => "First Payment for Bid Order # ".$id." has been already confirmed! Please wait for actual harvest to take place and further instructions will be given."                        
                ]);

                return response([
                    'message' => 'First Payment Confirmed!'
                ], 200);
            }
        } else {
            return response([
                'error' => 'Bid Order Not Found!'
            ], 400);
        }
    }
}
