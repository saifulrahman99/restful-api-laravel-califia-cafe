<?php

namespace App\Http\Controllers;

use App\Enums\ResponseMessage;
use App\Helpers\ApiResponse;
use App\Http\Requests\MenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $q = $request->query('q', '');
        $direction = $request->query('direction', 'asc');
        $sortBy = $request->query('sortBy', 'name');
        $perPage = $request->query('perPage', 10);
        $page = $request->query('page', 1);
        $menus = Menu::with(['category', 'discount'])->where('name', 'like', "%$q%")->orderBy($sortBy, $direction)->paginate($perPage, ['*'], 'page', $page);

        return ApiResponse::commonResponse(MenuResource::collection($menus)->response()->getData(true));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MenuRequest $request): JsonResponse
    {
        $data = $request->validated();
        $imagePath = $request->file('image')->store('menus', 'public');
        $data['image_path'] = $imagePath;

        $menu = Menu::create($data);

        return ApiResponse::commonResponse(new MenuResource($menu), ResponseMessage::CREATED, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $menu = Menu::with(['category', 'discount'])->findOrFail($id);
        return ApiResponse::commonResponse(new MenuResource($menu));
    }

    /**
     * @throws ValidationException
     */
    public function updateStock(Request $request, string $id): JsonResponse
    {
        $menu = Menu::findOrFail($id);
        $request->validate([
            'qty' => 'required|integer|min:0',
            'action' => 'required|string|in:add,subtract',
        ]);
        if ($request->get('action') === 'subtract') {
            $menu->stock -= $request->get('qty');
            if ($menu->stock < 0) {
                throw ValidationException::withMessages([
                    'stock' => ['stock tidak boleh kurang dari 0'],
                ]);
            }
        } else {
            $menu->stock += $request->get('qty');
        }
        $menu->save();
        return ApiResponse::commonResponse(new MenuResource($menu), ResponseMessage::UPDATED);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMenuRequest $request): JsonResponse
    {
        $data = $request->validated();
        $menu = Menu::findOrFail($request->get('id'));
        if ($request->file('image')) {
            $data['image_path'] = $request->file('image')->store('menus', 'public');
        }
        $menu->update($data);

        return ApiResponse::commonResponse(new MenuResource($menu), ResponseMessage::UPDATED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();
        return ApiResponse::commonResponse(null, ResponseMessage::DELETED);
    }
}
