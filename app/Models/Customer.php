<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function deposits()
    {
        return $this->hasMany(Deposit::class, 'customer_code', 'customer_code');
    }

}
