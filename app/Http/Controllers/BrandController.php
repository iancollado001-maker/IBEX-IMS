<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class BrandController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    public function index()
    {
        return response()->json(Brand::orderBy('brand_name')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'brand_name' => 'required|string|max:100|unique:brands,brand_name',
        ]);

        $brand = Brand::create([
            'brand_name' => trim($request->brand_name),
        ]);

        return response()->json([
            'success' => true,
            'id'      => $brand->id,
            'name'    => $brand->brand_name,
            'message' => 'Brand added successfully.',
        ]);
    }

    public function destroy(Request $request, Brand $brand)
    {
        if ($brand->assets()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete \"{$brand->brand_name}\" — it is assigned to {$brand->assets()->count()} asset(s). Remove those assets first.",
            ], 422);
        }

        $name = $brand->brand_name;
        $brand->delete();

        return response()->json([
            'success' => true,
            'message' => "Brand \"{$name}\" deleted.",
        ]);
    }
}
