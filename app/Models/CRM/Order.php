<?php
declare(strict_types=1);

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\CRM\ClientCar;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Order extends Model
{
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    public $fillable = ['check', 'pay_status', 'odometer', 'client_id', 'client_car_id',
        'worker_id', 'start_work', 'end_work', 'status', 'number', 'created_at', 'updated_at',
        'finish_work', 'type', 'note'];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d',
        'finish_work' => 'datetime:Y-m-d',
    ];

    public function car(): HasOne
    {
        return $this->hasOne(ClientCar::class, 'id', 'client_car_id');
    }

    public function client(): HasOne
    {
        return $this->hasOne(Client::class, 'id','client_id');
    }

    public function works(): HasMany
    {
        return $this->hasMany(OrderWork::class, 'order_id', 'id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'id');
    }

    public function scopeFilter($query, array $filters)
    {
        $search = $filters['search'] ?? null;
        $pdv = $filters['pdv'] ?? null;
        $pay_status = $filters['pay_status'] ?? null;

         $query->when($pdv, function ($query) use ($pdv) {
             if ($pdv == 'Без ПДВ')
                 $query->where('type', 0);
             if ($pdv == 'ПДВ')
                 $query->where('type', 1);
         });

         $query->when($pay_status, function ($query) use ($pay_status) {
            if ($pay_status == 'Неоплачено')
                $query->where('pay_status', 'Неоплачено');
        });

         
        $query->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->whereAny(['check','start_work','end_work', 'number', 'finish_work','status','pay_status'], 'like', "%$search%");
            });
            $query->orWhereHas('car', function ($query) use ($search) {
                $query->where('car_plate', 'like', "%$search%")
                    ->orWhere('vin', 'like', "%$search%")
                    ->orWhereHas('brand', function ($query) use ($search) {
                        $query->where('title', 'like', "%$search%");
                    });
            });
            $query->orWhereHas('client', function ($query) use ($search) {
                $query->whereAny(['first_name', 'last_name', 'company_iban', 'company_name', 'company_edrpu', 'phone'], 'like', "%$search%");
            });
        });
    }

}
