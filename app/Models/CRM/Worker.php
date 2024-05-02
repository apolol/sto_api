<?php
declare(strict_types=1);

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasUuid;

class Worker extends Model
{
    use HasFactory;
    use HasUuid;

    protected $guarded = [];
}
