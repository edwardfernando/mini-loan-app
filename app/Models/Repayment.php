<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repayment extends Model
{
    use HasFactory;

    public function scheduledRepayment()
    {
        return $this->belongsTo(ScheduledRepayment::class);
    }
}
