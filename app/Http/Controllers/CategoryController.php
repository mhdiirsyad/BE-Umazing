<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\RegisterStoreRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = Category::latest()->get();
             return response()->json([
                'message' => 'Categories retrieved successfully',
                'data' => $categories,
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
    public function store(CategoryStoreRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $newCategory = new Category();
            $newCategory['name'] = $data['name'];
            $newCategory->save();

            DB::commit();

            return response()->json([
                'message' => 'Category successfully created',
                'data' => new CategoryResource($newCategory),
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
            $category = Category::FindOrFail($id);

            return response()->json([
                'message' => 'Category retrieved successfully',
                'data' => new CategoryResource($category),
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
     * Update the specified resource in storage.
     */
    public function update(RegisterStoreRequest $request, string $id)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $category = Category::FindOrFail($id);
            $category['name'] = $data['name'];
            $category->save();

            DB::commit();

            return response()->json([
                'message' => 'Category successfully updated',
                'data' => new CategoryResource($category),
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
            $category = Category::FindOrFail($id);
            $category->delete();
            DB::commit();

            return response()->json([
                'message' => 'Category successfully deleted',
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
