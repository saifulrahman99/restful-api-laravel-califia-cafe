<?php

namespace App\Http\Controllers;

use App\Enums\ResponseMessage;
use App\Exceptions\Handler;
use App\Helpers\ApiResponse;
use App\Http\Requests\ToppingRequest;
use App\Http\Requests\UpdateToppingRequest;
use App\Http\Resources\ToppingResource;
use App\Models\Topping;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ToppingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $direction = $request->query('direction', 'asc');

        $topping = Topping::orderBy('name', $direction)->get();
        return ApiResponse::commonResponse(ToppingResource::collection($topping));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ToppingRequest $request): JsonResponse
    {
        $topping = Topping::create($request->validated());
        return ApiResponse::commonResponse(new ToppingResource($topping), ResponseMessage::CREATED, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $topping = Topping::findOrFail($id);
        return ApiResponse::commonResponse(new ToppingResource($topping));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateToppingRequest $request): JsonResponse
    {
        $category = Topping::findOrFail($request->get('id'));
        $category->update($request->validated());
        return ApiResponse::commonResponse(new ToppingResource($category), ResponseMessage::UPDATED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $topping = Topping::findOrFail($id);
        $topping->delete();
        return ApiResponse::commonResponse(null, ResponseMessage::DELETED);
    }

    public function updateStock(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'qty' => 'required|integer|min:0',
            'action' => 'required|string|in:add,subtract',
        ]);
        $topping = Topping::findOrFail($id);

        if ($request->get('action') === 'subtract') {
            $topping->stock -= $request->get('qty');
            if ($topping->stock < 0) {
                throw ValidationException::withMessages([
                    'stock' => ['stock tidak boleh kurang dari 0'],
                ]);
            }
            $topping->save();
        } else {
            $topping->stock += $request->get('qty');
            $topping->save();
        }
        return ApiResponse::commonResponse(new ToppingResource($topping), ResponseMessage::UPDATED);
    }
}
