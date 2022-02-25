<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\DishCreateRequest;

class DishesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dishes = Dish::all();
        return view('kitchen.dish', compact('dishes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('kitchen.dish_create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DishCreateRequest $request)
    {
        //dd($request->all());
        $dish = new Dish();
        $dish->name = $request->name;
        $dish->category_id = $request->category;
        //$dish->image = $request->dish_image;

        //upload image
        $imageName = date('YmdHis'). ".". request()->dish_image->getClientOriginalExtension();
        request()->dish_image->move(public_path('images'), $imageName);

        $dish->image = $imageName;
        $dish->save();

        return redirect('dish')->with('message','Dish created successfully');


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Dish $dish)
    {
       //dd($dish);
       $categories = Category::all();
       return view('kitchen.dish_edit', compact('dish','categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dish $dish)
    {
        $validatedData = request()->validate([
            'name' => 'required',
            'category' => 'required',
        ]);
        $dish->name = $request->name;
        $dish->category_id = $request->category;
        if ($request->dish_image)
        {
            $imageName = date('YmdHis'). ".". request()->dish_image->getClientOriginalExtension();
            request()->dish_image->move(public_path('images'), $imageName);
            $dish_image = $imageName;
        }

        $dish->save();

        return redirect('dish')->with('message','Dish updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dish $dish)
    {
        $dish->delete();
        return redirect('dish')->with('message','Dish removed successfully');
    }

    public function order()
    {
        //{{Request::segment(1)}}
    
        $rawstatus = config('res.order_status');
        $status = array_flip($rawstatus);
        $orders = Order::whereIn('status',[1,2])->get();

       return view('kitchen.order', compact('orders','status'));
    }
    

    public function approve(Order $order)
    {
        //dd($order);
        $order->status = config('res.order_status.processing');
        $order->save();

        return redirect('order')->with('message','Order Approved!!!!!!!!!!!!!!');
    }


    public function cancel(Order $order)
    {
        //dd($order);
        $order->status = config('res.order_status.cancel');
        $order->save();

        return redirect('order')->with('message','Order Rejected!');
    }


    public function ready(Order $order)
    {
        //dd($order);
        $order->status = config('res.order_status.ready');
        $order->save();

        return redirect('order')->with('message','Order Ready!!!!!!!!!!!!!!');
    }
}
