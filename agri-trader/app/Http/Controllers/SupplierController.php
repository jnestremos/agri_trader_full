<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierAddress;
use App\Models\SupplierContact;
use App\Models\SupplierContactPerson;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function addSupplier(Request $request){
        $supplier = $request->validate([
            'supplier_name' => 'required',
            'supplier_phoneNumber' => 'required',
            'supplier_telNumber' => 'nullable',
            'supplier_email' => 'required|email',
            'contact_firstName' => 'required',
            'contact_middleName' => 'required',
            'contact_lastName' => 'required',
            'contact_suffix' => 'nullable',
            'contact_position' => 'required',
            'address_zipcode' => 'required',
            'address_street' => 'required',
            'address_town' => 'required',
            'address_province' => 'required',
        ]);

        if(!$supplier){
            return response([
                'error' => 'Error adding Supplier!'
            ], 400);
        }

        $supplier = Supplier::create([
            'supplier_name' => $request->supplier_name
        ]);

        SupplierAddress::create([
            'supplier_id' => $supplier->id,
            'address_zipcode' => $request->address_zipcode,
            'address_street' => $request->address_street,
            'address_town' => $request->address_town,
            'address_province' => $request->address_province,
        ]);
        SupplierContact::create([
            'supplier_id' => $supplier->id,
            'supplier_phoneNumber' => $request->supplier_phoneNumber,
            'supplier_telNumber' => $request->supplier_telNumber,
            'supplier_email' => $request->supplier_email,
        ]);
        SupplierContactPerson::create([
            'supplier_id' => $supplier->id,
            'contact_firstName' => $request->contact_firstName,
            'contact_middleName' => $request->contact_middleName,
            'contact_lastName' => $request->contact_lastName,
            'contact_suffix' => $request->contact_suffix,
            'contact_position' => $request->contact_position,            
        ]);

        return response([
            'message' => 'Supplier Added Successfully!'
        ]);

    }
}
