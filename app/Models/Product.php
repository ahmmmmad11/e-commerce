<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'options' => 'collection',
        'images' => 'collection',
    ];

    public function options():Attribute
    {
        return Attribute::set(fn($val) => collect($val));
    }

    public function images():Attribute
    {
        return Attribute::set(fn($val) => collect($val));
    }

    public function updateQuantities ($count, $options = null): void
    {
        $this->quantity -= $count;

        if ($options) {
            $this->options = $this->options->map(function ($item) use ($options, $count){
                $name = $item['name'];

                if (array_key_exists($name, $options)) {
                    $item['options'] = array_map(function ($element) use($name, $options, $count){
                        if ($element['value'] == $options[$name]) {
                            $element['quantity'] -= $count;
                        }

                        return $element;
                    }, $item['options']);
                }

                return $item;
            });
        }

        $this->save();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'variant',
            'product_id',
            'variant_id'
        );
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }
}
