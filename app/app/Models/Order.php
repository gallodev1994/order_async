<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\enums\OrderStatus;

#[Fillable(['customer', 'price', 'status'])]
class Order extends Model
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    protected $casts = [
        'status' => OrderStatus::class,
    ];
}
