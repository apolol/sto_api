<?php
namespace App\Models\CRM;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;
    use HasUuid;

    protected $appends = ['full_name'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function cars(): HasMany
    {
        return $this->hasMany(ClientCar::class, 'client_id', 'id');
    }

    public function getFullNameAttribute()
    {
        if ($this->first_name != null)
            return $this->first_name . ' ' . $this->last_name;

        return $this->company_name. ' (ПДВ!!!)';
    }

    public function scopeFilter($query, array $filters)
    {
        $search = $filters['search'] ?? null;

        $query->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->whereAny(['first_name', 'last_name', 'company_iban', 'company_name', 'company_edrpu', 'phone'], 'like', "%$search%");
            });
            $query->orWhereHas('cars', function ($query) use ($search) {
                $query->where('car_plate', 'like', "%$search%")
                    ->orWhere('vin', 'like', "%$search%")
                    ->orWhereHas('brand', function ($query) use ($search) {
                        $query->where('title', 'like', "%$search%");
                    });
            });
        });
    }
}
