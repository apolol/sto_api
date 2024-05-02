<?php
declare(strict_types=1);

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientCar extends Model
{
    use HasFactory;
    use HasUuid;

    protected $guarded = [];

    protected $fillable = ['client_id','vin','odometer','year','engine_type','car_plate','brand_id', 'engine_value','description'];

    public string $label = 'vin';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_ad' => 'datetime',
        'update_at' => 'datetime'
    ];

    public function client(): HasOne
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function brand(): HasOne
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }

    public function orders() : HasMany
    {
        return $this->hasMany(Order::class,'client_car_id', 'id');
    }
}
