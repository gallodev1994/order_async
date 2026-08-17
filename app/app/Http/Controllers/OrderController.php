<?php

namespace App\Http\Controllers;

use App\DTOs\CreateOrderDto;
use App\enums\OrderStatus;
use Illuminate\Http\Request;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function index()
    {
        $orders = App(OrderService::class)->list();
        return response()->json($orders, 200);
    }

    public function processed()
    {
        $orders = App(OrderService::class)->listByStatus(OrderStatus::SUCCESS);
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $data = new CreateOrderDto(
            customer: $request->customer,
            price: (float) $request->price
        );

        $order = App(OrderService::class)->store($data);

        return response()->json(['success' => true, 'order' => $order], 201);
    }
}
