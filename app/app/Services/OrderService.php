<?php

namespace App\Services;

use App\DTOs\CreateOrderDto;
use App\enums\OrderStatus;
use App\Models\Order;
use App\Services\RabbitMQService;
use Illuminate\Support\Facades\DB;


class OrderService
{
   public function list()
   {
      return DB::table('orders')->get();
   }

   public function listByStatus(OrderStatus $status)
   {
      return DB::table('orders')->get()->where('status', '=', $status);
   }

   public function store(CreateOrderDto $data): Order
   {

      $order = Order::create([
         'customer' => $data->customer,
         'price' => $data->price,
         'status' => OrderStatus::PENDING,
      ]);

      app(RabbitMQService::class)->publish(
         'orders',
         [
            'event' => 'order.created',
            'order_id' => $order->id,
            'customer' => $order->customer,
            'price' => $order->price
         ]
      );

      return $order;
   }

   public function process(int $orderId): Order
   {
      // test ACK
      //throw new \Exception('Simulated processing error');

      $order = Order::findOrFail($orderId);

      if ($order->status !== OrderStatus::PENDING) {
         return $order;
      }

      $order->update([
         'status' => OrderStatus::SUCCESS,
      ]);

      return $order;
   }
}
