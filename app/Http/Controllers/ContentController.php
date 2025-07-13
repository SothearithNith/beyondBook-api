<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Content;
use App\Models\ContentImage;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;


class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $contents = Content::all();
            $source = [];

            foreach ($contents as $item) {
                $category = Category::find($item->category_id);
                $sub_category = SubCategory::find($item->sub_category_id);

                $images = ContentImage::where('content_id', $item->id)
                    ->get()
                    ->map(function ($img) {
                        return url($img->image_path); // or asset() if stored in /public
                    });

                $source[] = [
                    'id' => $item->id,
                    'category_id' => $item->category_id,
                    'category_kh' => $category?->category_kh,
                    'category_en' => $category?->category_en,
                    'sub_category_id' => $item->sub_category_id,
                    'sub_category_kh' => $sub_category?->sub_category_kh,
                    'sub_category_en' => $sub_category?->sub_category_en,
                    'title_kh' => $item->title_kh,
                    'title_en' => $item->title_en,
                    'description_kh' => $item->description_kh,
                    'description_en' => $item->description_en,
                    'image_path' => $images,
                ];
            }

            return response()->json([
                'message' => 'List Content',
                'data' => $source
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
                'category_id' => ['required', 'exists:tbl_category,id'],
                'sub_category_id' => ['required', 'exists:tbl_sub_category,id'],
                'title_kh' => [
                    'required',
                    'string',
                    'regex:/^[\x{1780}-\x{17FF}\s]*$/u',
                    'unique:tbl_content,title_kh',
                ],
                'title_en' => [
                    'required',
                    'string',
                    'regex:/^[A-Za-z\s]+$/',
                    'unique:tbl_content,title_en',
                ],
                'description_kh' => [
                    'required',
                    'string',
                    'regex:/^[\x{1780}-\x{17FF}\s]*$/u',
                ],
                'description_en' => [
                    'required',
                    'string',
                    'regex:/^[A-Za-z\s]+$/',
                ],
                'image' => ['nullable', 'array', 'min:2'],
                'image.*' => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'message' => 'Validation errors',
                    'errors' => $validate->errors(),
                ], 422);
            }

            $data = $validate->validated();

            DB::beginTransaction();

            // Save content
            $created = Content::create([
                'category_id' => $data['category_id'],
                'sub_category_id' => $data['sub_category_id'],
                'title_kh' => $data['title_kh'],
                'title_en' => $data['title_en'],
                'description_kh' => $data['description_kh'],
                'description_en' => $data['description_en'],
            ]);

            $uploadedPaths = [];

            // Save images if provided
            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = public_path('uploads/content');
                    $file->move($path, $filename);

                    $imagePath = 'uploads/content/' . $filename;
                    $uploadedPaths[] = $imagePath;

                    $image = ContentImage::create([
                        'content_id' => $created->id,
                        'image_path' => $imagePath,
                    ]);

                    if (!$image) {
                        throw new \Exception('Failed to save image to database.');
                    }
                }
            }

            DB::commit();

            $category = Category::find($created->category_id);
            $sub_category = SubCategory::find($created->sub_category_id);

            $images = ContentImage::where('content_id', $created->id)
                ->get()
                ->map(function ($img) {
                    return url($img->image_path); // or asset() if stored in /public
                });

            $source[] = [
                'id' => $created->id,
                'category_id' => $created->category_id,
                'category_kh' => $category?->category_kh,
                'category_en' => $category?->category_en,
                'sub_category_id' => $created->sub_category_id,
                'sub_category_kh' => $sub_category?->sub_category_kh,
                'sub_category_en' => $sub_category?->sub_category_en,
                'title_kh' => $created->title_kh,
                'title_en' => $created->title_en,
                'description_kh' => $created->description_kh,
                'description_en' => $created->description_en,
                'image_path' => $images,
            ];

            return response()->json([
                'message' => 'Content created successfully',
                'content' => $source,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded images
            if (!empty($uploadedPaths)) {
                foreach ($uploadedPaths as $path) {
                    $fullPath = public_path($path);
                    if (File::exists($fullPath)) {
                        File::delete($fullPath);
                    }
                }
            }

            // Delete the created content if exists
            if (isset($created)) {
                $created->delete();
            }

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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
