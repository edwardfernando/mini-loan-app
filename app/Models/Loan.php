<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = ['amount', 'term', 'state'];

    use HasFactory;

    public function scheduledRepayments()
    {
        return $this->hasMany(ScheduledRepayment::class);
    }
}


