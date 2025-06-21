<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    // GET /api/products
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $products = Product::with('attributes')->latest()->paginate($perPage);
        return new ProductResource(true, 'List Data Products', $products);
    }

    // POST /api/products
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'attributes' => 'nullable|array',
            'attributes.*.name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $product = Product::create([
                'name' => $request->name,
                'unit' => $request->unit,
            ]);

            if ($request->has('attributes')) {
                foreach ($request->input('attributes', []) as $attr) {
                    ProductAttribute::create([
                        'product_id' => $product->id,
                        'name' => $attr['name'],
                    ]);
                }                
            }

            DB::commit();
            return new ProductResource(true, 'Product Created Successfully', $product->load('attributes'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to save product: ' . $e->getMessage()], 500);
        }
    }

    // GET /api/products/{id}
    public function show($id)
    {
        $product = Product::with('attributes')->find($id);

        if (!$product) {
            return new ProductResource(false, 'Product Not Found', null);
        }

        return new ProductResource(true, 'Product Detail', $product);
    }

    // PUT /api/products/{id}
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return new ProductResource(false, 'Product Not Found', null);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'attributes' => 'nullable|array',
            'attributes.*.name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $product->update([
                'name' => $request->name,
                'unit' => $request->unit,
            ]);

            $product->attributes()->delete();

            if ($request->has('attributes')) {
                foreach ($request->attributes as $attr) {
                    ProductAttribute::create([
                        'product_id' => $product->id,
                        'name' => $attr['name'],
                    ]);
                }
            }

            DB::commit();
            return new ProductResource(true, 'Product Updated Successfully', $product->load('attributes'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update product: ' . $e->getMessage()], 500);
        }
    }

    // DELETE /api/products/{id}
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return new ProductResource(false, 'Product Not Found', null);
        }

        $product->delete();

        return new ProductResource(true, 'Product Deleted Successfully', null);
    }
}
