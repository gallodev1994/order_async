<?php

namespace App\Console\Commands;

use App\Services\RabbitMQService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;

#[Signature('app:consume-orders')]
#[Description('Command description')]
class ConsumeOrders extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(RabbitMQService $rabbitMQ): int
    {
        $this->info('Waiting for new orders ...');

        $rabbitMQ->consume(
            'orders',
            function (AMQPMessage $message, AMQPChannel $channel) {
                $data = json_decode(
                    $message->getBody(),
                    true
                );

                $this->info('Order received:' . $data['order_id']);

                $channel->basic_ack(
                    $message->delivery_info['delivery_tag']
                );
            }
        );

        return self::SUCCESS;
    }
}
