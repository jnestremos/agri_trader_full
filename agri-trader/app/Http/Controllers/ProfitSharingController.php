<?php

namespace App\Http\Controllers;

use App\Models\ProfitSharing;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
            'ar_ownerShare' => 'required',
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
}
