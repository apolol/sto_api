<?php
declare(strict_types=1);

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasUuid;

class Event extends Model
{
    use HasFactory;
    use HasUuid;

    protected $guarded = [];
    public $filable = ['first_name', 'last_name', 'phone', 'time','date'];

    protected $casts = [
        'created_ad' => 'datetime',
    ];
}
