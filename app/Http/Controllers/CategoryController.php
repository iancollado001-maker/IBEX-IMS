<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    public function index()
    {
        return response()->json(Category::orderBy('category_name')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:100|unique:categories,category_name',
        ]);

        $category = Category::create([
            'category_name' => trim($request->category_name),
        ]);

        return response()->json([
            'success' => true,
            'id'      => $category->id,
            'name'    => $category->category_name,
            'message' => 'Category added successfully.',
        ]);
    }

    public function destroy(Request $request, Category $category)
    {
        // Block deletion if any assets use this category
        if ($category->assets()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete \"{$category->category_name}\" — it is assigned to {$category->assets()->count()} asset(s). Remove those assets first.",
            ], 422);
        }

        $name = $category->category_name;
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => "Category \"{$name}\" deleted.",
        ]);
    }
}
