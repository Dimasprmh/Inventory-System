<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemAttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'product_attribute_id',
        'value',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id');
    }
}
