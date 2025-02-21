<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Bill extends Model
{
    use HasFactory, softDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['customer_name', 'trans_date', 'invoice_no', 'table', 'order_type', 'status'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string)Str::uuid();
            }
        });
    }

    public function billDetails(){
        return $this->hasMany(BillDetail::class, 'bill_id');
    }
}
