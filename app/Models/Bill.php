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
 * @property string $invoice_no
 * @property string $customer_name
 * @property string $trans_date
 * @property string|null $table
 * @property string $order_type
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BillDetail> $billDetails
 * @property-read int|null $bill_details_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bill newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bill newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bill onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bill query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bill whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bill whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bill whereInvoiceNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bill whereOrderType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bill whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bill whereTable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bill whereTransDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bill whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bill withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bill withoutTrashed()
 * @mixin \Eloquent
 */
class Bill extends Model
{
    use HasFactory, softDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['customer_name', 'trans_date', 'invoice_no', 'table', 'order_type', 'status', 'final_price', 'phone_number'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string)Str::uuid();
            }
        });
    }

    public function billDetails()
    {
        return $this->hasMany(BillDetail::class, 'bill_id');
    }
}
