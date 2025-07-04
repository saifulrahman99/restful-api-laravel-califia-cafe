<?php

namespace App\Http\Controllers;

use App\Enums\ResponseMessage;
use App\Events\NewOrderEvent;
use App\Events\PaymentStatusUpdated;
use App\Helpers\ApiResponse;
use App\Http\Requests\BillDetailRequest;
use App\Http\Requests\BillDetailToppingRequest;
use App\Http\Requests\BillRequest;
use App\Http\Requests\BillWhereIDs;
use App\Http\Requests\UpdateBillRequest;
use App\Http\Resources\BillResource;
use App\Models\Bill;
use App\Models\Menu;
use App\Models\Topping;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $q = $request->query('q', '');
        $startDate = $request->query('startDate');
        $endDate = $request->query('endDate');
        $direction = $request->query('direction', 'desc');
        $sortBy = $request->query('sortBy', 'trans_date');
        $perPage = $request->query('perPage', 10);
        $page = $request->query('page', 1);
        $status = $request->query('status');
        $notInclude = $request->get('notInclude');
        $withTrashed = $request->get('withTrashed', false);
        $paging = $request->query('paging', true);

        $allowedSortColumns = ['trans_date', 'invoice_no', 'customer_name', 'final_price'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'trans_date'; // Default jika kolom tidak valid
        }

        $bill = Bill::with(['billDetails.billDetailToppings'])
            ->where(function ($query) use ($q, $withTrashed) {
                $query->where('invoice_no', 'like', "%$q%")
                    ->orWhere('customer_name', 'like', "%$q%")
                    ->orWhere('phone_number', 'like', "%$q%");
                if ($withTrashed) {
                    $query->withTrashed();
                }
            });

        // Filter by date range (jika tersedia)
        if (!empty($startDate) && !empty($endDate)) {
            try {
                $bill->whereBetween('trans_date', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Format tanggal tidak valid'], 400);
            }
        }

        if (!empty($notInclude) && empty($status)) {
            $bill->whereNot('status', $notInclude);
        }

        if (!empty($status)) {
            $bill->where('status', $status);
        }

        if ($paging) {
            // Sorting dan pagination
            $bill = $bill->orderBy($sortBy, $direction)
                ->paginate($perPage, ['*'], 'page', $page);
        } else {
            $bill = $bill->orderBy($sortBy, $direction)->get();
        }
        return ApiResponse::commonResponse(BillResource::collection($bill)->response()->getData(true));
    }

    public function recentOrders(Request $request): JsonResponse
    {
        $sortBy = 'trans_date';
        $direction = 'desc';

        $bill = Bill::with(['billDetails.billDetailToppings'])
            ->whereIn('status', ['pending', 'confirm'])
            ->orderBy($sortBy, $direction)
            ->get();

        return ApiResponse::commonResponse(BillResource::collection($bill));
    }

    public function getBillWhereInIDs(BillWhereIDs $request): JsonResponse
    {
        $ids = $request->get("ids");
        $billsQuery = Bill::with(['billDetails.billDetailToppings'])->whereIn("id", $ids)->orderBy("trans_date", "desc");
        return ApiResponse::commonResponse(BillResource::collection($billsQuery->get())->response()->getData(true));
    }

//    public function exportBill(Request $request): JsonResponse
//    {
//
//    }

//    public function printInvoice(Request $request): JsonResponse{
//
//    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BillRequest $request): JsonResponse
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $bill = Bill::create([
                'customer_name' => $data['customer_name'],
                'phone_number' => $this->formatPhoneNumber($data['phone_number']),
                'trans_date' => now(),
                'invoice_no' => $this->generateInvoiceUUID(),
                'table' => $data['table'] ?? null,
                'order_type' => is_null(Arr::get($data, 'table')) ? 'TA' : 'DI',
                'status' => 'pending',
                'final_price' => 0,
            ]);
            $finalPrice = 0; // Untuk menghitung total harga Bill
            // Simpan Bill Detail
            foreach ($data['bill_details'] as $detail) {
                $validator = Validator::make($detail, (new BillDetailRequest())->rules());
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                $menu = Menu::with('discount')->findOrFail($detail['menu_id']);
                if ($menu->stock < $detail['qty']) {
                    throw ValidationException::withMessages([
                        'stock' => ['Stock tidak cukup'],
                    ]);
                }
                $menu->decrement('stock', $detail['qty']); // kurangi stok

                // Hitung harga setelah diskon
                $totalPrice = ($menu->price - optional($menu->discount)->amount ?? 0) * $detail['qty'];

                $billDetail = $bill->billDetails()->create([
                    'menu_id' => $detail['menu_id'],
                    'qty' => $detail['qty'],
                    'price' => $menu->price,
                    'discount_price' => optional($menu->discount)->amount ?? 0,
                    'note' => $detail['note'] ?? null,
                ]);

                $detailTotal = $totalPrice; // Simpan subtotal untuk bill_detail_toppings

                // Simpan Bill Detail Toppings jika ada
                if (!empty($detail['bill_detail_toppings'])) {
                    foreach ($detail['bill_detail_toppings'] as $detailTopping) {
                        $validator = Validator::make($detailTopping, (new BillDetailToppingRequest())->rules());
                        if ($validator->fails()) {
                            return response()->json(['errors' => $validator->errors()], 422);
                        }
                        $topping = Topping::findOrFail($detailTopping['topping_id']);

                        if ($topping->stock < $detailTopping['qty']) {
                            throw ValidationException::withMessages([
                                'stock' => ['Stock tidak cukup'],
                            ]);
                        }
                        $topping->decrement('stock', $detailTopping['qty']);

                        $toppingTotal = $topping->price * $detailTopping['qty']; // Hitung harga topping

                        $billDetail->billDetailToppings()->create([
                            'topping_id' => $detailTopping['topping_id'],
                            'qty' => $detailTopping['qty'],
                            'price' => $topping->price,
                        ]);

                        $detailTotal += $toppingTotal; // Tambahkan harga topping ke total BillDetail
                    }
                }

                $finalPrice += $detailTotal; // Tambahkan harga detail ke total Bill
            }

            // Update final_price di Bill setelah semua BillDetail tersimpan
            $bill->update(['final_price' => $finalPrice]);

            DB::commit();
            broadcast(new NewOrderEvent($bill))->toOthers();

            return ApiResponse::commonResponse(new BillResource($bill), ResponseMessage::CREATED, 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 422,
                'message' => $e->getMessage()], 422);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $bill = Bill::with(['billDetails.billDetailToppings'])->findOrFail($id);
        return ApiResponse::commonResponse(new BillResource($bill));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,confirm,canceled,paid',
        ]);

        $bill = Bill::with('billDetails.menu', 'billDetails.billDetailToppings')->findOrFail($id);

        try {
            DB::transaction(function () use ($request, $bill) {
                if ($request->get('status') === 'canceled') {
                    foreach ($bill->billDetails as $billDetail) {
                        // Cari Menu asli dari ID
                        $menu = Menu::find($billDetail->menu->id ?? null);
                        if ($menu) {
                            $menu->increment('stock', $billDetail->qty);
                        }

                        // Kembalikan stok topping kalau ada
                        foreach ($billDetail->billDetailToppings as $billDetailTopping) {
                            $topping = Topping::find($billDetailTopping->topping_id ?? null);
                            if ($topping) {
                                $topping->increment('stock', $billDetail->qty);
                            }
                        }
                    }
                }

                $bill->update(['status' => $request->get('status')]);
            });

            broadcast(new PaymentStatusUpdated($bill));

            return ApiResponse::commonResponse([
                'id' => $bill->id,
                'status' => $bill->status,
            ], ResponseMessage::UPDATED);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update bill status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateBillRequest $request): JsonResponse
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $bill = Bill::findOrFail($request->get('id'));
            $bill->update($data);
            DB::commit();
            return ApiResponse::commonResponse(new BillResource($bill), ResponseMessage::UPDATED);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $bill = Bill::findOrFail($id);
        DB::beginTransaction();
        try {
            $bill->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()], 500);
        }
        return ApiResponse::commonResponse(null, ResponseMessage::DELETED);
    }

    private function generateInvoiceUUID(): string
    {
        return 'INV-' . strtoupper(Str::random(8));
    }

    private function formatPhoneNumber($phone): string
    {
        // Hilangkan karakter non-numerik
        $phone = preg_replace('/\D/', '', $phone);

        // Ubah 08 menjadi 62
        return preg_replace('/^0/', '62', $phone);
    }
}
