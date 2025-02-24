<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * 
 *
 * @property string $id
 * @property string $bill_id
 * @property string $menu_id
 * @property int $qty
 * @property int $price
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Bill $bill
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BillDetailTopping> $billDetailToppings
 * @property-read int|null $bill_detail_toppings_count
 * @property-read \App\Models\Menu $menu
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillDetail whereBillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillDetail whereMenuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillDetail whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillDetail wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillDetail whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillDetail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BillDetail extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['bill_id', 'menu_id', 'qty', 'price', 'note'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string)Str::uuid();
            }
        });
    }

    public function bill(){
        return $this->belongsTo(Bill::class, 'bill_id');
    }

    public function menu(){
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function billDetailToppings()
    {
        return $this->hasMany(BillDetailTopping::class, 'bill_detail_id');
    }
}
