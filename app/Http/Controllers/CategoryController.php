<?php

namespace App\Http\Controllers;

use App\Enums\ResponseMessage;
use App\Helpers\ApiResponse;
use App\Http\Requests\CategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // Ambil parameter dari request
        $q = $request->query('q', '');
        $direction = $request->query('direction', 'asc'); // Default: ascending

        $category = Category::where('name', 'like', '%' . $q . '%')->orderBy('name', $direction)->get();

        return ApiResponse::commonResponse(CategoryResource::collection($category));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());
        return ApiResponse::commonResponse(new CategoryResource($category), ResponseMessage::CREATED, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $category = Category::findOrFail($id);
        return ApiResponse::commonResponse(new CategoryResource($category));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request): JsonResponse
    {
        $category = Category::findOrFail($request->get('id'));
        $category->update($request->validated());
        return ApiResponse::commonResponse(new CategoryResource($category), ResponseMessage::UPDATED);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return ApiResponse::commonResponse(null, ResponseMessage::DELETED);
    }
}
