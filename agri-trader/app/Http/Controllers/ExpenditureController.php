<?php

namespace App\Http\Controllers;

use App\Models\Expenditure;
use App\Models\Project;
use Illuminate\Http\Request;

class ExpenditureController extends Controller
{
    public function getProjectExpenditures($id){
        if(!Project::find($id)){
            return response([
                'error' => 'Project not found'
            ], 400);
        }
        return response([
            'project_commenceDate' => Project::find($id)->project_commenceDate,
            'expenditures' => Expenditure::where('project_id', $id)->get()
        ]);
    }


    public function addProjectExpenditure(Request $request){
        $expenditure = $request->validate([        
            'project_id' => 'required',
            'exp_ORNum' => 'required',
            'exp_type' => 'required',
            'project_commenceDate' => 'required|date',
            'exp_dateFrom' => 'required|date|after_or_equal:project_commenceDate',
            'exp_dateTo' => 'required|date|after:exp_dateFrom',
            'exp_paymentType' => 'required',
            'exp_accNum' => 'required_if:exp_paymentType,==,Bank',
            'exp_bankName' => 'required_if:exp_paymentType,==,Bank',
            'exp_accName' => 'required',
            'exp_amount' => 'required',
            'exp_remark' => 'required',
        ]);

        if(!$expenditure){
            return response([
                'error' => 'Error adding expenditure to project!'
            ], 400);
        }

        $expenditure = Expenditure::where('exp_ORNum', $request->exp_ORNum)->first();
        if($expenditure){
            return response([
                'error' => 'Expenditure already added!'
            ], 400);
        }

        Expenditure::create([
            'project_id' => $request->project_id,
            'exp_ORNum' => $request->exp_ORNum,
            'exp_type' => $request->exp_type,
            'exp_dateFrom' => $request->exp_dateFrom,
            'exp_dateTo' => $request->exp_dateTo,
            'exp_paymentType' => $request->exp_paymentType,
            'exp_accNum' => $request->exp_accNum,
            'exp_bankName' => $request->exp_bankName,
            'exp_accName' => $request->exp_accName,
            'exp_amount' => $request->exp_amount,
            'exp_remark' => $request->exp_remark,
        ]);

        return response([
            'message' => 'Expenditure added successfully!'
        ]);
    }
}
