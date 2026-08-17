<?php

namespace App\DTOs;

readonly class CreateOrderDto
{
   public function __construct(
      public string $customer,
      public float $price,
   ) {}
}
