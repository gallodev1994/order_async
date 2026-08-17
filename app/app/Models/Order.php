<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\enums\OrderStatus;

/**
 * @property int $id
 * @property string $customer
 * @property float $price
 * @property OrderStatus $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
#[Fillable(['customer', 'price', 'status'])]
class Order extends Model
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    protected $casts = [
        'status' => OrderStatus::class,
    ];
}
