<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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
