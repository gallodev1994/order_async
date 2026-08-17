<?php

namespace App\Http\Controllers;

use App\enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = DB::table('orders')->get();
        return response()->json($orders, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $order = Order::create([
            'customer' => $request->customer,
            'price' => $request->price,
            'status' => OrderStatus::PENDING,
        ]);

        $order->save();
        return response()->json(['success' => true], 200);
    }
}
