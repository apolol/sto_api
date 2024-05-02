<?php
declare(strict_types=1);

namespace App\Models\CRM;

use App\Models\CRM\WorkType;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderWork extends Model
{
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    protected $guarded = [];

    protected $fillable = [
        'order_id','worker_id','work_id','count','price'
    ];

    public function work_name(): BelongsTo
    {
        return $this->belongsTo(WorkType::class,'work_id', 'id');
    }

    public function worker(): HasOne
    {
        return $this->hasOne(Worker::class, 'id', 'worker_id');
    }
}
