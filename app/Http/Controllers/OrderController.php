<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Order;
use App\User;
use App\Cup;
use Carbon\Carbon;
use App\Helper;

class OrderController extends Controller
{
    //index orders
    public function index(Request $req){
        checkPermission('read', $req);

        $orders = Order::with(['owner' => function ($query){
            $query->select('id', 'name');
        }])->get();

    // return $orders;
        return view('orders/index', compact('orders'));
    }


    //show
    public function show($id, Request $req){
        $order = Order::with(['owner' => function ($query){
            $query->select('id', 'name');
        }])->where('id', '=', $id)->firstOrFail();

        //order can only be viewed by the owner or a admin.
        if (!(Auth::user()->id == $order->user_id)) {
            checkPermission('read', $req);
        }

        return view('orders/show', compact('order'));
    }

    //create
    public function create(){

        return view('orders/place');
    }


    public function test(){
        session_start();
        session()->put('key', "oke leuk dit");
        $test = session('key');
        dd($test);
    }

    public function test2(){
        session_start();
        $test = session('key');
        dd($test);
    }


    //store
    public function store(Request $req){
        session_start();
        $dateTime = Carbon::now();
        //create a cup for the order.
        $cup = new Cup;
        $cup->coffee_ordered = 0;
        $cup->created_at = $dateTime;
        $cup->user_id = Auth::user()->id;
        $cup->save();

        if(session('engraving') == null){

        //create a order
        $order = new Order;
        $order->clip = $req->clip;
        $order->engraving = 0;
        $order->front_img = 'nope';
        $order->back_img = 'nope';
        $order->ordered_at = $dateTime;
        $order->location = $req->location;
        $order->status = "not payed";
        //give the cup and user_id
        $order->cup_id = $cup->id;
        $order->user_id = Auth::user()->id;
        $order->save();

        session()->put('newcupid', $cup->id);

        } else {

            $order = new Order;
            $order->clip = $req->clip;
            $order->engraving = 1;
            $order->front_img = session('engraving');
            $order->back_img = 'nope';
            $order->ordered_at = $dateTime;
            $order->location = $req->location;
            $order->status = "not payed";
            //give the cup and user_id
            $order->cup_id = $cup->id;
            $order->user_id = Auth::user()->id;
            $order->save();

            session()->put('newcupid', $cup->id);

        }

        $test = session('newcupid');
        return redirect()->route('orders.show', ['id' => $order->id]);
    }



    //edit
    public function edit($id, Request $req){
        checkPermission('write', $req);

        $order = Order::findOrFail($id);

        return view('orders.edit', [
            'order' => $order,
        ]);
    }

    //update
    public function update(Request $req ,$id){
        checkPermission('write', $req);

        //find corresponding order.
        $order = Order::findOrFail($id);

        //update data
        $order->clip = $req->clip;
        $order->engraving = $req->engraving;
        $order->front_img = $req->front_img;
        $order->back_img = $req->back_img;
        $order->location = $req->location;
        $order->cup_id = $req->cup_id;
        $order->status = $req->status;
        $order->user_id = $req->user_id;

        $order->save();

        return redirect()->route('orders.show', ['id' => $id]);
    }

    //delete
    public function delete($id, Request $req){
        checkPermission('delete', $req);

        $deletedOrder = Order::destroy($id);

        if ($deletedOrder){
            return redirect()->route('orders.index');
        }
        else{
            return "Oops something went wrong";
        }
    }
}
