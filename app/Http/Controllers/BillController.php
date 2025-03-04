<?php

namespace App\Http\Controllers;

use App\Enums\ResponseMessage;
use App\Helpers\ApiResponse;
use App\Http\Requests\BillDetailRequest;
use App\Http\Requests\BillDetailToppingRequest;
use App\Http\Requests\BillRequest;
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
        $start_date = $request->query('start_date');
        $end_date = $request->query('end_date');
        $direction = $request->query('direction', 'desc');
        $sortBy = $request->query('sortBy', 'trans_date');
        $perPage = $request->query('perPage', 10);
        $page = $request->query('page', 1);

        $allowedSortColumns = ['trans_date', 'invoice_no', 'customer_name', 'final_price'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'trans_date'; // Default jika kolom tidak valid
        }

        $bill = Bill::with(['billDetails.billDetailToppings'])
            ->where(function ($query) use ($q) {
                $query->where('invoice_no', 'like', "%$q%")
                    ->orWhere('customer_name', 'like', "%$q%")
                    ->orWhere('phone_number', 'like', "%$q%")
                    ->withTrashed();
            });

        // Filter by date range (jika tersedia)
        if (!empty($start_date) && !empty($end_date)) {
            try {
                $bill->whereBetween('trans_date', [
                    Carbon::parse($start_date)->startOfDay(),
                    Carbon::parse($end_date)->endOfDay()
                ]);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Format tanggal tidak valid'], 400);
            }
        }

        // Sorting dan pagination
        $bill = $bill->orderBy($sortBy, $direction)
            ->paginate($perPage, ['*'], 'page', $page);

        return ApiResponse::commonResponse(BillResource::collection($bill)->response()->getData(true));
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
                        'stock' => ['Stock tidak boleh kurang dari 0'],
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
            return ApiResponse::commonResponse(new BillResource($bill), ResponseMessage::CREATED, 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
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

        $bill = Bill::findOrFail($id);
        $bill->update(['status' => $request->get('status')]);
        return ApiResponse::commonResponse([
            'id' => $bill->id,
            'status' => $bill->status,
        ], ResponseMessage::UPDATED);
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
            return response()->json(['message' => $e->getMessage()], 500);
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
