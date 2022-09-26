<?php

namespace App\Http\Controllers;

use App\Models\BidOrder;
use App\Models\BidOrderAccount;
use App\Models\Delivery;
use App\Models\Message;
use App\Models\ProduceInventory;
use App\Models\ProduceTrader;
use App\Models\ProduceYield;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    public function deliverProjectBid(Request $request, $id)
    {
        $user = User::find(auth()->id());
        if (BidOrder::find($id)) {
            $status = BidOrder::find($id)->bid_order_status_id;
            $acc = BidOrderAccount::where([['bid_order_id', '=', $id], ['bid_order_acc_type', '=', 'Final Payment']])->first();
            if ($user->hasRole('trader')) {
                if ($status == 4) {
                    $order = $request->validate([
                        'order_finalQty' => 'required|numeric',
                        'order_finalPrice' => 'required|numeric',
                        'order_finalTotal' => 'required|numeric',
                        'produce_yield_dateHarvestTo' => 'required|date',
                        'delivery_date' => 'required|date|after_or_equal:produce_yield_dateHarvestTo',
                        'produce_trader_id' => 'required|exists:produce_trader,id',
                        'order_grade' => 'nullable'
                    ]);
                    if (!$order) {
                        return response([
                            'error' => 'Bid Order Not Found!'
                        ], 400);
                    }

                    $order = BidOrder::find($id);                    
                    $order->bid_order_status_id = 5;
                    $order->produce_trader_id = $request->produce_trader_id;
                    $order->order_traderPrice = $order->project()->first()->contract()->first()->contract_estimatedPrice;
                    $order->order_grade = $request->order_grade;
                    $order->order_finalQty = $request->order_finalQty;
                    $order->order_finalPrice = $request->order_finalPrice;
                    $order->order_finalTotal = $request->order_finalTotal;
                    $order->save();

                    Delivery::create([
                        'bid_order_id' => $id,
                        'delivery_status' => 'Pending',
                        'delivery_date' => $request->delivery_date
                    ]);

                    if($order->order_grade){                    
                        $yield = ProduceYield::where([
                            ['project_id', '=', $order->project_id],
                            ['produce_trader_id', $order->produce_trader_id],
                            ['produce_yield_class', '=', $order->order_grade]
                        ])->first();
                    }
                    else{
                        $yield = ProduceYield::where([
                            ['project_id', '=', $order->project_id],
                            ['produce_trader_id', $order->produce_trader_id],
                            ['produce_yield_class', '=', 'N/A']
                        ])->first();
                    }                

                    $qty = ProduceInventory::where('produce_yield_id', $yield->id)->first()->produce_inventory_qtyOnHand;



                    ProduceInventory::where('produce_yield_id', $yield->id)->update([
                        'produce_inventory_qtyOnHand' => $qty - $order->order_finalQty
                    ]);
                    $produce_trader_qty = $order->produce_trader()->first()->prod_totalQty;
                    ProduceTrader::find($order->produce_trader_id)->update([
                        'prod_totalQty' => $produce_trader_qty - $order->order_finalQty
                    ]); 

                    $farm = $order->project()->first()->contract()->first()->farm()->first();
                    $qtyy = DB::table('farm_produce')->where([['produce_trader_id', $order->produce_trader_id], ['farm_id', $farm->id]])->first()->on_hand_qty;

                    DB::table('farm_produce')->where([['produce_trader_id', $order->produce_trader_id], ['farm_id', $farm->id]])->update([
                        'on_hand_qty' => $qtyy - $order->order_finalQty,
                        'updated_at' => Carbon::now()
                    ]);

                    Message::create([
                        'trader_id' => User::find(auth()->id())->trader()->first()->id,
                        'distributor_id' => $order->distributor_id,
                        'message_sentBy' => 'trader',
                        'message_body' => "Your order is now ready for delivery! Please confirm your order upon arrival of your delivery for the final payment.\n Your remaining balance is Php. ".number_format((float)$order->order_finalTotal - $order->order_dpAmount, 2, '.', '').""
                    ]);


                    return response([
                        'message' => 'Ready for Delivery!'
                    ], 200);
                } else if ($status == 6 && $acc) {
                    $order = BidOrder::find($id);
                    // $order->bid_order_status_id = 6;
                    // $order->save();

                    Delivery::where('bid_order_id', $id)->update([
                        'delivery_status' => 'Received',
                        'delivery_receivedDate' => $order->bid_order_account()->latest()->first()->bid_order_acc_datePaid                        
                    ]);
                    
                    if($order->order_grade){                    
                        $yield = ProduceYield::where([
                            ['project_id', '=', $order->project_id],
                            ['produce_trader_id', $order->produce_trader_id],
                            ['produce_yield_class', '=', $order->order_grade]
                        ])->first();
                    }
                    else{
                        $yield = ProduceYield::where([
                            ['project_id', '=', $order->project_id],
                            ['produce_trader_id', $order->produce_trader_id],
                            ['produce_yield_class', '=', 'N/A']
                        ])->first();
                    }                
                    

                    $inventory = ProduceInventory::where('produce_yield_id', $yield->id)->first();

                    Sale::create([
                        'bid_order_id' => $order->id,
                        'project_id' => $order->project_id,
                        'sale_type' => 'Sales (Project Bid)',
                        'sale_qty' => $order->order_finalQty,
                        'sale_stockLeft' => $inventory->produce_inventory_qtyOnHand,
                        'sale_price' => $order->order_finalPrice,
                        'sale_total' => $order->order_finalTotal
                    ]);

                    Message::create([
                        'trader_id' => User::find(auth()->id())->trader()->first()->id,
                        'distributor_id' => $order->distributor_id,
                        'message_sentBy' => 'trader',
                        'message_body' => "Final Payment for Order # ".$order->id." has now been confirmed! Thank you!"
                    ]);

                    // $order->delete();

                    return response([
                        'message' => 'Final Payment Confirmed!'
                    ], 200);
                } else if ($status == 6 && $acc) {
                    return response([
                        'error' => 'Final Payment Already Confirmed!'
                    ], 400);
                } else {
                    return response([
                        'error' => 'Error!'
                    ], 400);
                }
            } else { // distributor                
                if ($status == 5 && !$acc) {
                    $order = $request->validate([
                        'bid_order_acc_paymentMethod' => 'required|string',
                        'bid_order_acc_type' => 'required|string',
                        'bid_order_acc_amount' => 'required|numeric',
                        'bid_order_acc_bankName'  => 'required_if:bid_order_acc_paymentMethod,==,Bank',
                        'bid_order_acc_accNum' => 'required_if:bid_order_acc_paymentMethod,==,Bank',
                        'bid_order_acc_accName' => 'required',
                        'bid_order_acc_remarks' => 'nullable|string',
                        'bid_order_acc_datePaid' => 'required|date',
                        'delivery_receivedBy' => 'required',
                        'delivery_contactNum' => 'required'
                    ]);
                    if (!$order) {
                        return response([
                            'error' => 'Invalid Order!'
                        ], 400);
                    }   
                    $datePaid = new Carbon(($request->bid_order_acc_datePaid));
                    $order = BidOrder::find($id);
                    $dateDispatch = new Carbon(Delivery::where('bid_order_id', $order->id)->first()->delivery_date);
                    $datePaid->hour = 0;
                    $datePaid->minute = 0;
                    $datePaid->second = 0;
                    $dateDispatch->hour = 0;
                    $dateDispatch->minute = 0;
                    $dateDispatch->second = 0;
                    if(!$datePaid->greaterThanOrEqualTo($dateDispatch)){            
                        return response([
                            'error' => 'Invalid Date!'
                        ], 400);                            
                    }
                    Delivery::where('bid_order_id', BidOrder::find($id)->id)->update([
                        'delivery_receivedBy' => $request->delivery_receivedBy,
                        'delivery_contactNum' => $request->delivery_contactNum,                        
                    ]);

                    BidOrderAccount::create([
                        'bid_order_id' => $id,
                        'bid_order_acc_type' => $request->bid_order_acc_type,
                        'bid_order_acc_paymentMethod' => $request->bid_order_acc_paymentMethod,
                        'bid_order_acc_bankName' => $request->bid_order_acc_bankName,
                        'bid_order_acc_accNum' => $request->bid_order_acc_accNum,
                        'bid_order_acc_accName' => $request->bid_order_acc_accName,
                        'bid_order_acc_amount' => $request->bid_order_acc_amount,
                        'bid_order_acc_remarks' => $request->bid_order_acc_remarks,
                        'bid_order_acc_datePaid' => $request->bid_order_acc_datePaid
                    ]);
                    $order->bid_order_status_id = 6;
                    $order->save();
                    Message::create([
                        'trader_id' => BidOrder::find($id)->trader_id,
                        'distributor_id' => User::find(auth()->id())->distributor()->first()->id,
                        'message_sentBy' => 'distributor',
                        'message_body' => "Final Payment for Bid Order # ".$id." has been already sent! Please check and verify with your account whether the payment has been received or not."
                    ]);                    
                    return response([
                        'message' => 'Final Payment Delivered!'
                    ], 200);
                } else {
                    return response([
                        'error' => 'Final Payment Already Delivered!'
                    ], 400);
                }
            }
        } else {
            return response([
                'error' => 'Bid Order Not Found!'
            ], 400);
        }
    }

    public function deliverOnHandBid(Request $request, $id)
    {
        $user = User::find(auth()->id());
        $order = BidOrder::find($id);

        if ($order) {
            $status = BidOrder::find($id)->bid_order_status_id;
            $acc = BidOrderAccount::where([['bid_order_id', '=', $id], ['bid_order_acc_type', '=', 'Final Payment']])->first();
            if ($user->hasRole('trader')) {
                if ($status == 4) {
                    $order = $request->validate([
                        'produce_yield_dateHarvestTo' => 'required|date',
                        'delivery_date' => 'required|date|after_or_equal:produce_yield_dateHarvestTo'
                    ]);

                    if (!$order) {
                        return response([
                            'error' => 'Bid Order Not Found!'
                        ], 400);
                    }
                    $order = BidOrder::find($id);

                    $order->bid_order_status_id = 5;
                    $order->order_finalQty = $order->on_hand_bid()->first()->on_hand_bid_qty;
                    $order->order_finalPrice = $order->order_negotiatedPrice;
                    $order->order_finalTotal = $order->on_hand_bid()->first()->on_hand_bid_total;
                    $order->save();

                    Delivery::create([
                        'bid_order_id' => $id,
                        'delivery_status' => 'Pending',
                        'delivery_date' => $request->delivery_date
                    ]);
                    Message::create([
                        'trader_id' => User::find(auth()->id())->trader()->first()->id,
                        'distributor_id' => $order->distributor_id,
                        'message_sentBy' => 'trader',
                        'message_body' => "Your order is now ready for delivery! Please confirm your order upon arrival of your delivery for the final payment."
                    ]);
                    return response([
                        'message' => 'Ready for Delivery!'
                    ], 200);

                } else if ($status == 6 && $acc) {
                    // $order->bid_order_status_id = 6;
                    $order = BidOrder::find($id);
                    $order->order_finalQty = $order->on_hand_bid()->first()->on_hand_bid_qty;
                    $order->order_finalPrice = $order->order_negotiatedPrice;
                    $order->order_finalTotal = $order->on_hand_bid()->first()->on_hand_bid_total;
                    $order->save();

                    Delivery::where('bid_order_id', $id)->update([
                        'delivery_status' => 'Received',
                        'delivery_receivedDate' => Carbon::now()
                    ]);

                    if($order->order_grade){                    
                        $yield = ProduceYield::where([
                            ['project_id', '=', $order->project_id],
                            ['produce_trader_id', $order->produce_trader_id],
                            ['produce_yield_class', '=', $order->order_grade]
                        ])->first();
                    }
                    else{
                        $yield = ProduceYield::where([
                            ['project_id', '=', $order->project_id],
                            ['produce_trader_id', $order->produce_trader_id],
                            ['produce_yield_class', '=', 'N/A']
                        ])->first();
                    }

                    $qty = ProduceInventory::where('produce_yield_id', $yield->id)->first()->produce_inventory_qtyOnHand;



                    ProduceInventory::where('produce_yield_id', $yield->id)->update([
                        'produce_inventory_qtyOnHand' => $qty - $order->order_finalQty
                    ]);

                    $produce_trader_qty = $order->produce_trader()->first()->prod_totalQty;
                    ProduceTrader::find($order->produce_trader_id)->update([
                        'prod_totalQty' => $produce_trader_qty - $order->order_finalQty
                    ]); 

                    $farm = $order->project()->first()->contract()->first()->farm()->first();
                    $qtyy = DB::table('farm_produce')->where([['produce_trader_id', $order->produce_trader_id], ['farm_id', $farm->id]])->first()->on_hand_qty;

                    DB::table('farm_produce')->where([['produce_trader_id', $order->produce_trader_id], ['farm_id', $farm->id]])->update([
                        'on_hand_qty' => $qtyy - $order->order_finalQty,
                        'updated_at' => Carbon::now()
                    ]);

                    $inventory = ProduceInventory::where('produce_yield_id', $yield->id)->first();

                    Sale::create([
                        'bid_order_id' => $order->id,
                        'project_id' => $order->project_id,
                        'sale_type' => 'Sales (On-Hand Bid)',
                        'sale_qty' => $order->order_finalQty,
                        'sale_stockLeft' => $inventory->produce_inventory_qtyOnHand,
                        'sale_price' => $order->order_finalPrice,
                        'sale_total' => $order->order_finalTotal
                    ]);

                    //$order->delete();

                    // ----------------- SALES MODULE PART --------------- //

                    return response([
                        'message' => 'Final Payment Confirmed!'
                    ], 200);
                } else if ($status == 6 && $acc) {
                    return response([
                        'error' => 'Final Payment Already Confirmed!'
                    ], 400);
                } else {
                    return response([
                        'error' => 'Error!'
                    ], 400);
                }
            } else { // when status is 5
                if ($status == 5 && !$acc) {
                    $order = $request->validate([
                        'bid_order_acc_paymentMethod' => 'required|string',
                        'bid_order_acc_type' => 'required|string',
                        'bid_order_acc_amount' => 'required|numeric',
                        'bid_order_acc_bankName'  => 'required_if:bid_order_acc_paymentMethod,==,Bank',
                        'bid_order_acc_accNum' => 'required_if:bid_order_acc_paymentMethod,==,Bank',
                        'bid_order_acc_accName' => 'required',
                        'bid_order_acc_remarks' => 'nullable|string',
                        'bid_order_acc_datePaid' => 'required|date',
                        'delivery_receivedBy' => 'required',
                        'delivery_contactNum' => 'required'
                    ]);

                    if (!$order) {
                        return response([
                            'error' => 'Invalid Order!'
                        ], 400);
                    }

                    $datePaid = new Carbon(($request->bid_order_acc_datePaid));
                    $order = BidOrder::find($id);
                    $dateDispatch = new Carbon(Delivery::where('bid_order_id', $order->id)->first()->delivery_date);
                    $datePaid->hour = 0;
                    $datePaid->minute = 0;
                    $datePaid->second = 0;
                    $dateDispatch->hour = 0;
                    $dateDispatch->minute = 0;
                    $dateDispatch->second = 0;
                    if(!$datePaid->greaterThanOrEqualTo($dateDispatch)){            
                        return response([
                            'error' => 'Invalid Date!'
                        ], 400);                            
                    }

                    Delivery::where('bid_order_id', BidOrder::find($id)->id)->update([
                        'delivery_receivedBy' => $request->delivery_receivedBy,
                        'delivery_contactNum' => $request->delivery_contactNum
                    ]);
                    
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

                    $order->bid_order_status_id = 6;
                    $order->save();

                    Message::create([
                        'trader_id' => BidOrder::find($id)->trader_id,
                        'distributor_id' => User::find(auth()->id())->distributor()->first()->id,
                        'message_sentBy' => 'distributor',
                        'message_body' => "Final Payment for Bid Order # ".$id." has been already sent! Please check and verify with your account whether the payment has been received or not."
                    ]); 

                    return response([
                        'message' => 'Final Payment Delivered!'
                    ], 200);
                } else {
                    return response([
                        'error' => 'Final Payment Already Delivered!'
                    ], 400);
                }
            }
        } else {
            return response([
                'error' => 'Bid Order Not Found!'
            ], 400);
        }
    }
}
