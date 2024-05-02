<?php
declare(strict_types=1);

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Traits\HasUuid;

class Brand extends Model
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
        'created_ad' => 'datetime',
        'update_at' => 'datetime'
    ];

    public function parent(): HasOne
    {
        return $this->hasOne(Brand::class, 'id' , 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Brand::class, 'parent_id', 'id');
    }

    public function getFullNameAttribute()
    {
        if($this->parent_id != null)
            return $this->parent->title . ' '. $this->title;
        return $this->title;
    }
}
