<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    // Non-incrementing key dan tipe UUID
    public $incrementing = false;
    protected $keyType = 'string';

    // Kolom yang boleh diisi
    protected $fillable = [
        'id',
        'name',
        'unit',
    ];

    // Generate UUID otomatis saat creating
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // Relasi one-to-many: Product has many Items
    public function items()
    {
        return $this->hasMany(Item::class);
    }

        public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }
}
