<?php

namespace App\Http\Controllers;

use App\Models\BidOrderAccount;
use App\Models\BidOrderStatus;
use App\Models\Message;
use App\Models\ProfitSharing;
use App\Models\Project;
use App\Models\Refund;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfitSharingController extends Controller
{
    public function createAR(Request $request){
        $ar = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'farm_id' => 'required|exists:farms,id',
            'farm_owner_id' => 'required|exists:farm_owners,id',
            'produce_id' => 'required|exists:produces,id',
            'ar_totalSales' => 'required',
            'ar_totalExpenses' => 'required',
            'ar_ownerShare' => 'required|gt:0',
            'ar_ownerShareType' => 'required',
            'ar_ownerPercentage' => 'required_if:ar_ownerShareType,==,Percentage',
            'ar_profit' => 'required',
            'ar_paymentMethod' => 'required',
            'ar_accName' => 'required',
            'ar_accNum' => 'required_if:ar_paymentMethod,==,Bank',
            'ar_bankName' => 'required_if:ar_paymentMethod,==,Bank',
            'ar_datePaid' => 'required|date|after_or_equal:ar_dateTerminate',            
            'ar_remark' => 'required_if:newShare,==,true',
            'ar_dateTerminate' => 'required|date',
            'newShare' => 'required'                    
        ]);

        if(!$ar){
            return response([
                'error' => 'Error adding Acknowledgement request'
            ], 400);
        }

        ProfitSharing::create([
            'project_id' => $request->project_id,
            'farm_id' => $request->farm_id,
            'farm_owner_id' => $request->farm_owner_id,
            'produce_id' => $request->produce_id,
            'ar_totalSales' => $request->ar_totalSales,
            'ar_totalExpenses' => $request->ar_totalExpenses,
            'ar_ownerShare' => $request->ar_ownerShare,
            'ar_ownerShareType' => $request->ar_ownerShareType,
            'ar_ownerPercentage' => $request->ar_ownerPercentage,
            'ar_profit' => $request->ar_profit,
            'ar_paymentMethod' => $request->ar_paymentMethod,
            'ar_accName' => $request->ar_accName,
            'ar_accNum' => $request->ar_accNum,
            'ar_bankName' => $request->ar_bankName,
            'ar_datePaid' => $request->ar_datePaid,
            'ar_remark' => $request->ar_remark,
        ]);

       Project::find($request->project_id)->update([
            'project_status_id' => $request->newShare ? 5 : 4
        ]);

        DB::table('project_status_history')->insert([
            'project_id' => Project::find($request->project_id)->id,
            'project_status_id' => Project::find($request->project_id)->project_status_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return response([
            'message' => 'Acknowledgement Report added successfully'
        ]);
    }

    public function updateAR($id){
        $ar = Project::find($id)->profit_sharing()->first();
        if(!$ar){
            return response([
                'error' => 'Error updating Acknowledgement Report'
            ], 400);
        }
        Project::find($id)->profit_sharing()->update([
            'ar_approvedOn' => Carbon::now()
        ]);

        Project::find($id)->update([            
            'project_completionDate' => Carbon::now()
        ]);

        return response([
            'message' => 'Acknowledgement Report updated successfully'
        ]);
    }

    public function refundAllOrders(Request $request, $id){
        $acc = $request->validate([
            'bid_order_acc_paymentMethod' => 'required|string',            
            'bid_order_acc_bankName'  => 'required_if:bid_order_acc_paymentMethod,==,Bank',
            'bid_order_acc_accNum' => 'required_if:bid_order_acc_paymentMethod,==,Bank',
            'bid_order_acc_accName' => 'required',
            'bid_order_acc_remarks' => 'nullable|string',
            'bid_order_acc_datePaid' => 'required|date'
        ]);

        if (!$acc) {
            return response([
                'error' => 'Invalid Refund!'
            ], 400);
        }

        $project = Project::find($id);
        foreach($project->bid_order()->get() as $order){
            $amount = $order->bid_order_account()->first()->bid_order_acc_amount;
            
            Message::create([
                'trader_id' => $order->trader_id,
                'distributor_id' => $order->distributor_id,
                'message_sentBy' => 'trader',
                'message_body' => "Refund for Order # ".$order->id." has been created and approved! The amount to be refunded is Php. ".number_format((float)$amount, 2, '.', '')."."
            ]);

            Refund::create([
                'project_id' => $order->project_id,
                'bid_order_id' => $order->id,
                // 'refund_numOfDays' => $request->refund_numOfDays,
                // 'refund_percentage' => $request->refund_percentage,
                'refund_amount' => $amount,
                'refund_statusFrom' => BidOrderStatus::find($order->bid_order_status_id)->bid_order_status
            ]);

            BidOrderAccount::create([
                'bid_order_id' => $order->id,
                'bid_order_acc_type' => 'Refund',
                'bid_order_acc_paymentMethod' => $request->bid_order_acc_paymentMethod,
                'bid_order_acc_bankName' => $request->bid_order_acc_bankName,
                'bid_order_acc_accNum' => $request->bid_order_acc_accNum,
                'bid_order_acc_accName' => $request->bid_order_acc_accName,
                'bid_order_acc_amount' => $amount * -1,
                'bid_order_acc_remarks' => $request->bid_order_acc_remarks,
                'bid_order_acc_datePaid' => $request->bid_order_acc_datePaid,
            ]);

            $order->bid_order_status_id = 8;
            $order->save();

            return response([
                'message' => 'Refund for all Orders Sent!'
            ], 200);
        }
    }

    public function deleteAR($id){
        $ar = Project::find($id)->profit_sharing()->first();

        Project::find($id)->update([
            'project_status_id' => 2
        ]);

        DB::table('project_status_history')
        ->where('project_id', $id)->whereIn('project_status_id', [4, 5])->delete();

        if(!$ar){
            return response([
                'error' => 'Error updating Acknowledgement Report'
            ], 400);
        }

        Project::find($id)->profit_sharing()->delete();        

        return response([
            'message' => 'Acknowledgement Report cancelled'
        ]);
    }
}
