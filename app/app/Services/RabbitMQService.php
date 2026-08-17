<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
   public function publish(string $queue, array $data): void
   {
      $connection = new AMQPStreamConnection(
         env('RABBITMQ_HOST', 'localhost'),
         env('RABBITMQ_PORT', 5672),
         env('RABBITMQ_USER', 'admin'),
         env('RABBITMQ_PASSWORD', 'admin')
      );

      $channel = $connection->channel();

      $channel->queue_declare(
         $queue,
         false,
         true,
         false,
         false
      );

      $message = new AMQPMessage(
         json_encode($data),
         [
            'content_type' => 'application/json',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
         ]
      );

      $channel->basic_publish(
         $message,
         '',
         $queue
      );

      $channel->close();
      $connection->close();
   }

   public function consume(string $queue, callable $callback): void
   {
      $connection = new AMQPStreamConnection(
         env('RABBITMQ_HOST', 'localhost'),
         env('RABBITMQ_PORT', 5672),
         env('RABBITMQ_USER', 'admin'),
         env('RABBITMQ_PASSWORD', 'admin')
      );

      $channel = $connection->channel();

      $channel->queue_declare(
         $queue,
         false,
         true,
         false,
         false
      );

      $channel->basic_qos(
         null,
         1,
         null
      );

      $channel->basic_consume(
         $queue,
         '',
         false,
         false,
         false,
         false,
         function (AMQPMessage $message) use ($callback, $channel) {
            $callback($message, $channel);
         }
      );

      while (true) {
         $channel->wait();
      }
   }
}
