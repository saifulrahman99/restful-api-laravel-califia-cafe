<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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
