<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Item;
use App\Models\ProductAttribute;
use App\Models\ItemAttributeValue;
use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductItemController extends Controller
{
    // GET /api/products/{product}/items
    public function index(Product $product)
    {
        $product->load(['items.attributeValues', 'attributes']);
        return new ItemResource(true, 'List Items of Product: ' . $product->name, $product);
    }

    public function getAllItems()
    {
        $items = \App\Models\Item::with('product')->get();

        return response()->json($items);
    }

    // POST /api/products/{product}/items
    public function store(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'sku' => 'required|string|unique:items,sku',
            'merk' => 'required|string',
            'ukuran' => 'required|integer',
            'stock' => 'required|integer',
            'attribute_values' => 'nullable|array',
            'attribute_values.*.product_attribute_id' => 'required|integer|exists:product_attributes,id',
            'attribute_values.*.value' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $item = Item::create([
                'id' => Str::uuid(),
                'sku' => $request->sku,
                'merk' => $request->merk,
                'ukuran' => $request->ukuran,
                'stock' => $request->stock,
                'product_id' => $product->id,
            ]);

            if ($request->has('attribute_values')) {
                foreach ($request->attribute_values as $attr) {
                    ItemAttributeValue::create([
                        'item_id' => $item->id,
                        'product_attribute_id' => $attr['product_attribute_id'],
                        'value' => $attr['value'],
                    ]);
                }
            }

            DB::commit();
            return new ItemResource(true, 'Item created successfully', $item->load('attributeValues'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to save item: ' . $e->getMessage()], 500);
        }
    }

    // GET /api/products/{product}/items/{item}
    public function show(Product $product, Item $item)
    {
        if ($item->product_id !== $product->id) {
            return response()->json(['error' => 'Item does not belong to the specified product'], 404);
        }

        return new ItemResource(true, 'Item detail', $item->load('attributeValues'));
    }

    // PUT /api/products/{product}/items/{item}
    public function update(Request $request, Product $product, Item $item)
    {
        if ($item->product_id !== $product->id) {
            return response()->json(['error' => 'Item does not belong to the specified product'], 404);
        }

        $validator = Validator::make($request->all(), [
            'sku' => 'required|string|unique:items,sku,' . $item->id,
            'merk' => 'required|string',
            'ukuran' => 'required|integer',
            'stock' => 'required|integer',
            'attribute_values' => 'nullable|array',
            'attribute_values.*.product_attribute_id' => 'required|integer|exists:product_attributes,id',
            'attribute_values.*.value' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $item->update($request->only('sku', 'merk', 'ukuran', 'stock'));

            // Delete old values and insert new
            $item->attributeValues()->delete();
            if ($request->has('attribute_values')) {
                foreach ($request->attribute_values as $attr) {
                    ItemAttributeValue::create([
                        'item_id' => $item->id,
                        'product_attribute_id' => $attr['product_attribute_id'],
                        'value' => $attr['value'],
                    ]);
                }
            }

            DB::commit();
            return new ItemResource(true, 'Item updated successfully', $item->load('attributeValues'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update item: ' . $e->getMessage()], 500);
        }
    }

    // DELETE /api/products/{product}/items/{item}
    public function destroy(Product $product, Item $item)
    {
        if ($item->product_id !== $product->id) {
            return response()->json(['error' => 'Item does not belong to the specified product'], 404);
        }

        $item->delete();
        return response()->json(['success' => true, 'message' => 'Item deleted successfully']);
    }
}
