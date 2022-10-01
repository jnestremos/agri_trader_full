<?php

namespace App\Http\Controllers;

use App\Models\FarmOwner;
use App\Models\FarmOwnerAddress;
use App\Models\FarmOwnerContactNumber;
use App\Models\Trader;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class FarmOwnerController extends Controller
{
    public function add(Request $request)
    {
        $user = $request->validate([
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'gender' => 'required|string',
            'contactNum' => 'required|array',
            'birthDate' => 'required|date',
            'email' => 'required|email|unique:users,email',
            'province' => 'required|string',
            'address' => 'required|string',
            'zipcode' => 'required|string',
            'role' => 'required|int',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        if (!$user) {
            return response([
                'error' => 'Invalid credentials',
            ], 401);
        }

       

        $newUser = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $checkOwner = FarmOwner::where([
            ['owner_firstName', $request->firstName],
            ['owner_lastName', $request->lastName]
        ])->first();

        if($checkOwner){
            return response([
                'error' => 'Farm Owner has been added already by another trader!'
            ], 400);
        }

        $farmOwner = FarmOwner::create([
            'user_id' => $newUser->id,
            'owner_firstName' => $request->firstName,
            'owner_lastName' => $request->lastName,
            'owner_gender' => $request->gender,
            'owner_birthDate' => $request->birthDate,
            'owner_email' => $request->email,
        ]);


        FarmOwnerAddress::create([
            'farm_owner_id' => $farmOwner->id,
            'owner_province' => $request->province,
            'owner_address' => $request->address,
            'owner_address' => $request->address,
            'owner_zipcode' => $request->zipcode,
        ]);
        $newUser->attachRole('3');
        if (gettype($request->contactNum) == 'array') {
            for ($i = 0; $i < count($request->contactNum); $i++) {
                FarmOwnerContactNumber::create([
                    'farm_owner_id' => $farmOwner->id,
                    'owner_contactNum' => $request->contactNum[$i]
                ]);
            }
        } else {
            FarmOwnerContactNumber::create([
                'farm_owner_id' => $farmOwner->id,
                'owner_contactNum' => $request->contactNum
            ]);
        }
        return response([
            'message' => 'Farm Owner Added!',
        ], 200);
    }
}
