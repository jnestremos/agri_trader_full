<?php

namespace App\Http\Controllers;

use App\Models\BidOrder;
use App\Models\BidOrderAccount;
use App\Models\BidOrderStatus;
use App\Models\Message;
use App\Models\OnHandBid;
use App\Models\ProduceTrader;
use App\Models\ProduceYield;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OnHandBidController extends Controller
{
    public function addOnHandBid(Request $request)
    {
        $order = $request->validate([
            'trader_id' => 'required|exists:traders,id',
            'project_id' => 'required|exists:projects,id',
            'produce_trader_id' => 'required|exists:produce_trader,id',
            'order_grade' => 'string|nullable',
            'order_traderPrice' => 'required|numeric',
            'order_dateNeededFrom' => 'required|date:after:now',
            'order_dateNeededTo' => 'required|date|after:order_dateNeededFrom',
            'order_initialPrice' => 'required|numeric',
            'on_hand_bid_qty' => 'required|numeric',
            'on_hand_bid_total' => 'required|numeric'
        ]);

        // $yield = ProduceYield::where([['project_id', $request->project_id], ['produce_yield_class', '=', $request->order_grade]])->first();
        if (!$order) {
            return response([
                'error' => 'Invalid Order!'
            ], 400);
        }

        $farm_produce = DB::table('farm_produce')->where([
            ['farm_id', Project::find($request->project_id)->contract()->first()->farm()->first()->id],
            ['produce_trader_id', $request->produce_trader_id]
        ])->first();

        if($farm_produce->on_hand_qty <= 0){
            return response([
                'error' => 'Produce Out of Stock!'
            ], 400);
        }
        else{
            if($request->on_hand_bid_qty > $farm_produce->on_hand_qty){
                return response([
                    'error' => 'Produce Invalid Quantity! Please refresh the page for updated available quantity!'
                ], 400);
            }
        }

        $newOrder = BidOrder::create([
            'trader_id' => $request->trader_id,
            'distributor_id' => User::find(auth()->id())->distributor()->first()->id,
            'produce_trader_id' => $request->produce_trader_id,
            'bid_order_status_id' => 1,
            'order_grade' => $request->order_grade,
            'order_traderPrice' => $request->order_traderPrice,
            'project_id' => $request->project_id,
            'order_dateNeededFrom' => $request->order_dateNeededFrom,
            'order_dateNeededTo' => $request->order_dateNeededTo,
            'order_initialPrice' => $request->order_initialPrice
        ]);

        OnHandBid::create([
            'bid_order_id' => $newOrder->id,
            'on_hand_bid_qty' => $request->on_hand_bid_qty,
            'on_hand_bid_total' => $request->on_hand_bid_total,
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
    public function approveOnHandBid(Request $request, $id)
    {
        $user = User::find(auth()->id());
        if (BidOrder::find($id)) {
            $bidOrder = BidOrder::find($id);
            if ($user->hasRole('trader')) {
                $order = $request->validate([
                    'order_negotiatedPrice' => 'required|numeric|gt:0',
                    'order_datePlaced' => 'required|date', //from created_at table
                    'on_hand_bid_total' => 'required|numeric',
                    'order_dpDueDate' => 'required|date|after:order_datePlaced',
                    'order_dpAmount' => 'required|numeric'
                ]);

                if (!$order) {
                    return response([
                        'error' => 'Invalid Order Approval!'
                    ], 400);
                }

                OnHandBid::where('bid_order_id', $id)->update([
                    'on_hand_bid_total' => $request->on_hand_bid_total
                ]);
                if ($bidOrder->bid_order_status_id == 2) {
                    return response([
                        'error' => 'Order Already Approved!'
                    ], 400);
                } else {
                    $bidOrder->order_dpDueDate = $request->order_dpDueDate;
                    $bidOrder->order_negotiatedPrice = $request->order_negotiatedPrice;
                    $bidOrder->order_finalPrice = $request->order_negotiatedPrice;
                    $bidOrder->bid_order_status_id = 2;
                    $bidOrder->order_dpAmount = $request->order_dpAmount;
                    $bidOrder->save();
                }
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

    public function cancelOnHandBid($id){
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
                $refund->update([
                    'refund_amount' => null
                ]);
                $bidOrder->bid_order_account()->latest()->first()->delete();
                $bidOrder->update([
                    'bid_order_status_id' => 7
                ]);                
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

    public function paymentOnHandBid(Request $request, $id)
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
                    if($datePaid->isBefore($datePlaced) || $datePaid->isAfter($dpDueDate)){
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
            } else {
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
