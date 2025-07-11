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
                $category = Category::find($subCategory->id_category);

                $data[] = [
                    'id' => $subCategory->id,
                    'id_category' => $subCategory->id_category,
                    'category' => $category ? $category->category : null,
                    'sub_category' => $subCategory->sub_category,
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
                'id_category' => [
                    'required',
                    'exists:tbl_category,id',
                ],
                'sub_category' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[\x{1780}-\x{17FF}\s]*$/u',
                    'unique:tbl_sub_category,sub_category',
                ],
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'message' => 'Validation errors',
                    'errors' => $validate->errors(),
                ], 422);
            }

            $id_category = $validate->validated()['id_category'];

            $category = Category::find($id_category);

            $data = SubCategory::create([
                'id_category' => $validate->validated()['id_category'],
                'sub_category' => $validate->validated()['sub_category'],
            ]);

            return response()->json([
                'message' => 'SubCategory created successfully',
                'data' => [
                    'id' => $data->id,
                    'id_category' => $data->id_category,
                    'category' => $category->category,
                    'sub_category' => $data->sub_category,
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

            $category = Category::find($sub->id_category);

            $data = [
                'id' => $sub->id,
                'id_category' => $sub->id_category,
                'category' => $category ? $category->category : null,
                'sub_category' => $sub->sub_category,
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
                'id_category' => [
                    'sometimes',
                    'exists:tbl_category,id',
                ],
                'sub_category' => [
                    'sometimes',
                    'string',
                    'max:255',
                    'regex:/^[\x{1780}-\x{17FF}\s]*$/u',
                    'unique:tbl_sub_category,sub_category',
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
            $category = Category::find($sub->id_category);
            $db = [
                'id' => $sub->id,
                'id_category' => $sub->id_category,
                'category' => $category ? $category->category : null,
                'sub_category' => $sub->sub_category,
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
