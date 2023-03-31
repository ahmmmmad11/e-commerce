<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Seller extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = ['documents' => 'collection'];

    public function user(): MorphMany
    {
        return $this->morphMany(User::class, 'user');
    }

    public function address(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }
}
