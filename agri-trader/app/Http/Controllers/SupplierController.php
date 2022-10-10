<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierAddress;
use App\Models\SupplierContact;
use App\Models\SupplierContactPerson;
use App\Models\Trader;
use App\Models\User;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function addSupplier(Request $request){
        $supplier = $request->validate([
            'supplier_name' => 'required',
            'supplier_phoneNumber' => 'required',
            'supplier_telNumber' => 'nullable',
            'supplier_email' => 'required|email|unique:supplier_contacts,supplier_email',
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

        $supplier = Supplier::where([
            ['supplier_name', $request->supplier_name],
            ['trader_id', Trader::where('user_id', auth()->id())->first()->id]
        ])->first();

        if($supplier){
            return response([
                'error' => 'Supplier already exists!'
            ], 400);
        }

        $supplier = Supplier::create([
            'trader_id' => User::find(auth()->id())->trader()->first()->id,
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

    public function fetchSuppliers () {       
        $suppliers = Supplier::where('trader_id', Trader::where('user_id', auth()->id())->first()->id)->get();
        $supplier_addresses = [];
        foreach($suppliers as $supplier){
            array_push($supplier_addresses, $supplier->supplier_address()->first());
        }
        return response([
            'suppliers' => $suppliers,
            'supplier_addresses' => $supplier_addresses
        ]);
    }

    public function fetchSupplier($id){
        $supplier = Supplier::find($id);
        if($supplier->trader_id != Trader::where('user_id', auth()->id())->first()->id){
            return response([
                'error' => 'Unauthorized Access!'
            ], 400);
        }
        $supplier_address = $supplier->supplier_address()->first();
        $supplier_contact = $supplier->supplier_contact()->first();
        $supplier_contact_person = $supplier->supplier_contact_person()->first();

        return response([
            'supplier' => $supplier,
            'supplier_address' => $supplier_address,
            'supplier_contact' => $supplier_contact,
            'supplier_contact_person' => $supplier_contact_person
        ]);
    }

    public function updateSupplier(Request $request, $id){
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
                'error' => 'Error updating Supplier!'
            ], 400);
        }

        $supplier = Supplier::find($id);
        $supplier->supplier_name = $request->supplier_name;
        $supplier->supplier_contact()->update([
            'supplier_phoneNumber' => $request->supplier_phoneNumber,
            'supplier_telNumber' => $request->supplier_telNumber,
            'supplier_email' => $request->supplier_email,
        ]);
        $supplier->supplier_contact_person()->update([
            'contact_firstName' => $request->contact_firstName,
            'contact_middleName' => $request->contact_middleName,
            'contact_lastName' => $request->contact_lastName,
            'contact_suffix' => $request->contact_suffix,
            'contact_position' => $request->contact_position,
        ]);
        $supplier->supplier_address()->update([
            'address_zipcode' => $request->address_zipcode,
            'address_street' => $request->address_street,
            'address_town' => $request->address_town,
            'address_province' => $request->address_province,
        ]);     
        return response([
            'message' => 'Supplier Updated Successfully!'
        ]);
    }
}
