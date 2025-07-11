<?php

namespace App\Http\Controllers;

use App\Models\Category;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Category::all();
        return response()->json([
            'message' => 'Category list',
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category_kh' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[\x{1780}-\x{17FF}\s]*$/u',
                    'unique:tbl_category,category_kh',
                ],
                'category_en' => [
                    'required',
                    'string',
                    'max:255','regex:/^[A-Za-z\s]+$/',
                    'unique:tbl_category,category_en',
                ],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation errors',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = Category::create([
                'category_kh' => $validator->validated()['category_kh'],
                'category_en' => $validator->validated()['category_en'],
            ]);

            return response()->json([
                'message' => 'Category created successfully',
                'data' => [
                    'id' => $data->id,
                    'category_kh' => $data->category_kh,
                    'category_en' => $data->category_en,
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
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Category details',
            'data' => $category
        ], 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'category_kh' => [
                'sometimes',
                'string',
                'max:50',
                'regex:/^[\x{1780}-\x{17FF}\s]*$/u',
                'unique:tbl_category,category_kh,' . $id,
            ],
            'category_en' => [
                'sometimes',
                'string',
                'max:50',
                'regex:/^[A-Za-z\s]+$/',
                'unique:tbl_category,category_en,' . $id,
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $category->update($validator->validated());

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ], 200);
    }
}
