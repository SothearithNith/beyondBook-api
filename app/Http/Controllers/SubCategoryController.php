<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Get all SubCategories
            $subCategories = SubCategory::all();

            $data = [];

            foreach ($subCategories as $subCategory) {
                // Find the category for this subcategory
                $category = Category::find($subCategory->category_id);

                $data[] = [
                    'id' => $subCategory->id,
                    'category_id' => $subCategory->category_id,
                    'category_kh' => $category ? $category->category_kh : null,
                    'category_en' => $category ? $category->category_en : null,
                    'sub_category_kh' => $subCategory->sub_category_kh,
                    'sub_category_en' => $subCategory->sub_category_en,
                    'created_at' => $subCategory->created_at,
                    'updated_at' => $subCategory->updated_at,
                ];
            }

            return response()->json([
                'message' => 'SubCategory list',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'category_id' => [
                    'required',
                    'exists:tbl_category,id',
                ],
                'sub_category_kh' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[\x{1780}-\x{17FF}\s]*$/u',
                    'unique:tbl_sub_category,sub_category_kh',
                ],
                'sub_category_en' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[A-Za-z\s]+$/',
                    'unique:tbl_sub_category,sub_category_en',
                ],
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'message' => 'Validation errors',
                    'errors' => $validate->errors(),
                ], 422);
            }

            $category = Category::find($validate->validated()['category_id']);

            $data = SubCategory::create([
                'category_id' => $validate->validated()['category_id'],
                'sub_category_kh' => $validate->validated()['sub_category_kh'],
                'sub_category_en' => $validate->validated()['sub_category_en'],
            ]);

            return response()->json([
                'message' => 'SubCategory created successfully',
                'data' => [
                    'id' => $data->id,
                    'category_id' => $data->category_id,
                    'category_kh' => $category->category_kh,
                    'category_en' => $category->category_en,
                    'sub_category_kh' => $data->sub_category_kh,
                    'sub_category_en' => $data->sub_category_en,
                    'crated_at' => $data->created_at,
                    'updated_at' => $data->updated_at,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
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
            $sub = SubCategory::find($id);

            if (!$sub) {
                return response()->json([
                    'message' => 'SubCategory not found',
                ], 404);
            }

            $category = Category::find($sub->category_id);

            $data = [
                'id' => $sub->id,
                'category_id' => $sub->category_id,
                'category_kh' => $category ? $category->category_kh : null,
                'category_en' => $category ? $category->category_en : null,
                'sub_category_kh' => $sub->sub_category_kh,
                'sub_category_en' => $sub->sub_category_en,
                'created_at' => $sub->created_at,
                'updated_at' => $sub->updated_at,
            ];

            return response()->json([
                'message' => 'SubCategory details',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $sub = SubCategory::find($id);
            if (!$sub) {
                return response()->json([
                    'message' => 'SubCategory not found',
                ], 404);
            }

            $validate = Validator::make($request->all(), [
                'category_id' => [
                    'sometimes',
                    'exists:tbl_category,id',
                ],
                'sub_category_kh' => [
                    'sometimes',
                    'string',
                    'max:255',
                    'regex:/^[\x{1780}-\x{17FF}\s]*$/u',
                    'unique:tbl_sub_category,sub_category_kh',
                ],
                'sub_category_en' => [
                    'sometimes',
                    'string',
                    'max:255',
                    'regex:/^[A-Za-z\s]+$/',
                    'unique:tbl_sub_category,sub_category_en',
                ],
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'message' => 'Validation errors',
                    'errors' => $validate->errors(),
                ], 422);
            }

            $sub->update($validate->validated());

            // find category
            $category = Category::find($sub->category_id);
            $db = [
                'id' => $sub->id,
                'category_id' => $sub->category_id,
                'category_kh' => $category ? $category->category_kh : null,
                'category_en' => $category ? $category->category_en : null,
                'sub_category_kh' => $sub->sub_category_kh,
                'sub_category_en' => $sub->sub_category_en,
                'created_at' => $sub->created_at,
                'updated_at' => $sub->updated_at,
            ];
            return response()->json([
                'message' => 'Update sub-category',
                'data' => $db,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }

        $sub = SubCategory::find($id);
        if (!$sub) {
            return response()->json([
                'message' => 'SubCategory not found',
            ], 404);
        }

        $sub->delete();

        return response()->json([
            'message' => 'SubCategory deleted successfully',
        ], 200);
    }
}
