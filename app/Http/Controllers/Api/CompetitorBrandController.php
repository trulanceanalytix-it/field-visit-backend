<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompetitorBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Illuminate\Validation\Rule;

class CompetitorBrandController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('competitor_brands')
                    ->where(fn($q) => $q->where('category', $request->category))
            ],
            'category' => [
                'required',
                'string',
                Rule::in(['Leggings', 'NonLeggings', 'Innerwear', 'Mens'])
            ],
            'sub_category' => ['nullable', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $brand = CompetitorBrand::create([
            'name'         => trim($request->name),
            'category'     => $request->category,
            'sub_category' => $request->sub_category,
            'is_active'    => true,
            'sort_order'   => 999,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Brand added successfully',
            'brand'   => $brand,
        ], 201);
    }
    // Keep your index() for listing
    public function index()
    {
        return CompetitorBrand::active()
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('category');
    }
}
