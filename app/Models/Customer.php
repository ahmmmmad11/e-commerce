<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = ['documents' => 'collection'];

    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'user');
    }

    public function address(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }
}
