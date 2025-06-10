<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Item extends Model
{
    // UUID sebagai primary key
    public $incrementing = false;
    protected $keyType = 'string';

    // Kolom yang bisa diisi mass-assignment
    protected $fillable = [
        'id',
        'sku',
        'merk',
        'ukuran',
        'stock',
        'product_id',
    ];

    // Generate UUID otomatis saat membuat item
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // Relasi: Item belongs to a Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi: Item memiliki banyak attribute values
    public function attributeValues()
    {
        return $this->hasMany(ItemAttributeValue::class);
    }
}
