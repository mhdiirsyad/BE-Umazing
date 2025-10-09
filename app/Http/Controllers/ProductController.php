<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Product::query();
            if($request->has('search')){
                $query->where('name', 'like', '%'.$request->get('search').'%');
            }

            if($request->has('category')){
                $query->where('category_id', 'like', '%'.$request->get('category').'%');
            }

            $products = $query->get();

            return response()->json([
                'message' => 'Product retrieved successfully',
                'data' => ProductResource::collection($products),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errors occurred',
                'error' => $e->getMessage(),
            ], 500);
        }


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();

        try {
            $newProduct = new Product();
            $newProduct['name'] = $data['name'];
            $newProduct['price'] = $data['price'];
            $newProduct['stock'] = $data['stock'];
            $newProduct['description'] = $data['description'];
            $newProduct['is_active'] = filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN);
            $newProduct['category_id'] = $data['category_id'];

            if($request->hasFile('image')){
                $image_path = $request->file('image')->store('products', 'public');
                $newProduct['image'] = $image_path;
            }
            $newProduct->save();

            DB::commit();

            return response()->json([
                'message' => 'Successfully add new Product',
                'data' => new ProductResource($newProduct),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Errors occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $product = Product::query()->find($id);
            if(!$product){
                return response()->json([
                    'message' => 'Product not Found',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'message' => 'Successfully find a Produt',
                'data' => new  ProductResource($product),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errors occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductStoreRequest $request, string $id)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $product = Product::query()->find($id);
            if(!$product){
                return response()->json([
                    'message' => 'Product not Found',
                    'data' => null,
                ], 404);
            }

            $product['name'] = $request['name'];
            $product['price'] = $request['price'];
            $product['stock'] = $request['stock'];
            $product['description'] = $request['description'];
            $product['is_active'] = $request['is_active'];
            $product['category_id'] = $request['category_id'];

            if($request->hasFile('image')){
                Storage::delete($product['image']);
                $image_path = $request->file('image')->store('products', 'public');
                $product['image'] = $image_path;
            }
            $product->save();

            DB::commit();

            return response()->json([
                'message' => 'Product updated',
                'data' => new ProductResource($product),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Errors occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $product = Product::query()->find($id);
            if(!$product){
                return response()->json([
                    'message' => 'Product not Found',
                    'data' => null,
                ], 404);
            } 

            Storage::delete($product['image']);
            $product->delete();
            DB::commit();

            return response()->json([
                'message' => 'Product deleted',
                'data' => null,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Errors occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
