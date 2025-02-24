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
 * @property int $amount
 * @property string $start_date
 * @property string $end_date
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Menu|null $menu
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Discount whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Discount extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['name', 'amount', 'start_date', 'end_date', 'is_active'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string)Str::uuid();
        });
    }

    public function menu()
    {
        return $this->hasOne(Menu::class, 'discount_id');
    }
}
