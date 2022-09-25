<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function addSupplier(Request $request){
        $supplier = $request->validate([            
            'supplier_name' => 'required',
            'supplier_phoneNumber' => 'required',
            'supplier_telNumber' => 'required',
            'supplier_email' => 'required|email',
            'address_zipcode' => 'required',
            'address_street' => 'required',
            'address_town' => 'required',
            'address_province' => 'required',
            'contact_firstName' => 'required',
            'contact_middleName' => 'required',
            'contact_lastName' => 'required',
            'contact_suffix' => 'required',
            'contact_position' => 'required',
        ]);
    }
}
