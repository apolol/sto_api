<?php
declare(strict_types=1);

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderProduct extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $guarded = [];

    public $fillable = ['order_id','title','where_get','price_for_client','real_price','count','discount','articul','brand','uktz'];
}
