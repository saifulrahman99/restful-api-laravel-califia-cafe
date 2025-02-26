<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 *
 *
 * @property string $id
 * @property string $name
 * @property int $price
 * @property string|null $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BillDetailTopping> $billDetailsToppings
 * @property-read int|null $bill_details_toppings_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topping newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topping newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topping query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topping whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topping whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topping whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topping wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topping whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Topping whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Topping extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['name', 'type', 'price', 'stock'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string)Str::uuid();
        });
    }

    public function billDetailsToppings()
    {
        return $this->hasMany(BillDetailTopping::class, 'topping_id');
    }
}
