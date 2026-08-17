<?php

namespace App\Console\Commands;

use App\Services\RabbitMQService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;
use App\Services\OrderService;

#[Signature('app:consume-orders')]
#[Description('Command description')]
class ConsumeOrders extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(RabbitMQService $rabbitMQ, OrderService $orderService): int
    {
        $this->info('Waiting for new orders ...');

        $rabbitMQ->consume(
            'orders',
            function (AMQPMessage $message, AMQPChannel $channel) use ($orderService) {
                try {
                    $data = json_decode(
                        $message->getBody(),
                        true
                    );

                    $order = $orderService->process(
                        $data['order_id']
                    );

                    $this->info(
                        "Order {$order->id} processed successfully"
                    );

                    // ACK - process OK
                    $channel->basic_ack(
                        $message->delivery_info['delivery_tag']
                    );
                } catch (\Throwable $e) {
                    logger()->error(
                        'Error processing order',
                        [
                            'order_id' => $data['order_id'] ?? null,
                            'error' => $e->getMessage(),
                        ]
                    );

                    // ACK
                    $channel->basic_nack(
                        $message->delivery_info['delivery_tag'],
                        false,
                        true
                    );

                    throw $e;
                }
            }
        );

        return self::SUCCESS;
    }
}
