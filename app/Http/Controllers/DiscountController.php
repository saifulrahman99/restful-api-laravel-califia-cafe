<?php

namespace App\Http\Controllers;

use App\Enums\ResponseMessage;
use App\Helpers\ApiResponse;
use App\Http\Requests\DiscountRequest;
use App\Http\Requests\UpdateDiscountRequest;
use App\Http\Resources\DiscountResource;
use App\Models\Discount;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = $request->get('q');
        $direction = $request->query('direction', 'asc');
        $discounts = Discount::where('name', 'like', "%$q%")->orderBy("name", $direction)->get();
        return ApiResponse::commonResponse(DiscountResource::collection($discounts));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DiscountRequest $request)
    {
        $discount = Discount::create($request->validated());
        return ApiResponse::commonResponse(new DiscountResource($discount), ResponseMessage::CREATED, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $discount = Discount::findOrFail($id);
        return ApiResponse::commonResponse(new DiscountResource($discount));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function updateStatus($id)
    {
        $discount = Discount::findOrFail($id);

        if (now()->greaterThan($discount->end_date)) {
            return ApiResponse::commonResponse(null, "Tanggal diskon sudah lewat", 422);
        }
        if (!$discount->is_active) {
            Menu::where('discount_id', $discount->id)->update(['discount_id' => null]);
        }

        $discount->is_active = !$discount->is_active;
        $discount->save();
        return ApiResponse::commonResponse(new DiscountResource($discount), ResponseMessage::UPDATED);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDiscountRequest $request)
    {
        $discount = Discount::findOrFail($request->get('id'));
        $discount->update($request->validated());
        return ApiResponse::commonResponse(new DiscountResource($discount), ResponseMessage::UPDATED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $discount = Discount::findOrFail($id)->delete();
        return ApiResponse::commonResponse(null, ResponseMessage::DELETED);
    }
}
