<?php

namespace App\Http\Controllers\API;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Services\FileUploadService;

class CategoryController extends Controller
{
    use ApiResponse;

    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }


        public function index(Request $request)
        {
            $query = Category::query();

            // Search
            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Filter by Status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $categories = $query
                ->latest()
                ->paginate(10);

            return $this->success(
                'Categories retrieved successfully.',
                [
                    'categories' => CategoryResource::collection($categories),
                    'pagination' => [
                        'current_page' => $categories->currentPage(),
                        'last_page' => $categories->lastPage(),
                        'per_page' => $categories->perPage(),
                        'total' => $categories->total(),
                    ]
                ]
            );
        }

        public function store(StoreCategoryRequest $request)
    {
        $imagePath = null;

        if ($request->hasFile('image')) {
        $image = $this->fileUploadService->upload(
            $request->file('image'),
            'categories'
        );
    }


        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'image' => $imagePath,
            'status' => $request->boolean('status', true),
        ]);

        return $this->success(
            'Category created successfully.',
            new CategoryResource($category),
            201
        );
    }
    public function show(Category $category)
{
    return $this->success(
        'Category retrieved successfully.',
        new CategoryResource($category)
    );
}

  public function update(UpdateCategoryRequest $request, Category $category)
{
    $image = $category->image;

    if ($request->hasFile('image')) {

        $image = $this->fileUploadService->replace(
            $request->file('image'),
            $category->image,
            'categories'
        );
    }

    $category->update([
        'name' => $request->name,
        'slug' => Str::slug($request->name),
        'description' => $request->description,
        'image' => $image,
        'status' => $request->boolean('status'),
    ]);

    return $this->success(
        'Category updated successfully.',
        new CategoryResource($category->fresh())
    );
}
   public function destroy(Category $category)
{
    $category->delete();

    return $this->success(
        'Category deleted successfully.'
    );
}
public function forceDelete($id)
{
    $category = Category::onlyTrashed()->findOrFail($id);

    $this->fileUploadService->delete($category->image);

    $category->forceDelete();

    return $this->success(
        'Category permanently deleted.'
    );
}
    public function restore($id)
{
    $category = Category::onlyTrashed()->findOrFail($id);

    $category->restore();

    return $this->success(
        'Category restored successfully.',
        new CategoryResource($category)
    );
}

}
