<?php

namespace App\Http\Controllers;

use App\Models\Distributor;
use App\Models\Message;
use App\Models\Trader;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function readMessages($id){
        $user = User::find(auth()->id());        
        if($user->hasRole('distributor') && $user->distributor()->first() && $user->distributor()->first()->id == $id){
            $messages = Message::where('distributor_id', $user->distributor()->first()->id)->oldest()->get();
            $traders = [];
            foreach($messages as $message){                
                array_push($traders, Trader::find($message->trader_id));                                
            }
            return response([
                'traders' => $traders,
                'messages' => $messages
            ], 200);
        }
        else if($user->hasRole('trader') && $user->trader()->first() && $user->trader()->first()->id == $id){
            $messages = Message::where('trader_id', $user->trader()->first()->id)->oldest()->get();
            $distributors = [];
            foreach($messages as $message){                
                array_push($distributors, Distributor::find($message->distributor_id));                              
            }
            return response([
                'distributors' => $distributors,
                'messages' => $messages
            ], 200);
        } 
        else{
            return response([
                'error' => 'You have no access rights to this page!'
            ], 400);
        }       
        

    }
    public function addMessage(Request $request){
        $message = $request->validate([
            'trader_id' => 'required|exists:traders,id',
            'distributor_id' => 'required|exists:distributors,id',
            'message_sentBy' => 'required|string',
            'message_body' => 'required|string',
        ]);

        if(!$message){
            return response([
                'error' => 'Message Error!'
            ], 400);
        }

        $user = User::find(auth()->id());
        if($user->hasRole('distributor')){
            Message::create([
                'trader_id' => $request->trader_id,
                'distributor_id' => User::find(auth()->id())->distributor()->first()->id,
                'message_sentBy' => $request->message_sentBy,
                'message_body' => $request->message_body,
                'created_at' => Carbon::now()->format('Y-m-d g:i A'),
                'updated_at' => Carbon::now()->format('Y-m-d g:i A'),
            ]);   
        }
        else if($user->hasRole('trader')){
            Message::create([
                'trader_id' => User::find(auth()->id())->trader()->first()->id,
                'distributor_id' => $request->distributor_id,
                'message_sentBy' => $request->message_sentBy,
                'message_body' => $request->message_body,
                'created_at' => Carbon::now()->format('Y-m-d g:i A'),
                'updated_at' => Carbon::now()->format('Y-m-d g:i A'),
            ]); 
        }


        return response([
            'message' => 'Message Sent Successfully!'
        ], 200);
    }
}
