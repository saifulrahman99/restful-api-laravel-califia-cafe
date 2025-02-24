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
 * @property string $topping_id
 * @property string $bill_detail_id
 * @property int $qty
 * @property int $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BillDetail $billDetail
 * @property-read \App\Models\Topping $topping
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillDetailTopping newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillDetailTopping newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillDetailTopping query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillDetailTopping whereBillDetailId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillDetailTopping whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillDetailTopping whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillDetailTopping wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillDetailTopping whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillDetailTopping whereToppingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillDetailTopping whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BillDetailTopping extends Model
{
   use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['topping_id','bill_detail_id','qty','price'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string)Str::uuid();
            }
        });
    }
    public function topping(){
        return $this->belongsTo(Topping::class, 'topping_id');
    }
    public function billDetail(){
        return $this->belongsTo(BillDetail::class, 'bill_detail_id');
    }
}
