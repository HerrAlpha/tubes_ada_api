<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\RefSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CheckoutController extends BaseController
{
    public function index_product(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id'    => 'required|numeric|exists:products,id',
            'qty'           => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $product        = Product::select('id', 'name', 'product_pict', 'price', 'created_at')->find($request->product_id);
        $qty            = (int) $request->qty;
        $amount         = $qty * $product->price;

        $data           = [
            'product'               => $product,
            'qty'                   => $qty,
            'amount'                => $amount
        ];

        return $this->sendResponse('Berhasil menampilkan data.', $data);
    }

    public function index_investment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'investment_id'    => 'required|numeric|exists:invoices,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $invoice        = Invoice::whereNull('investor_id')->find($request->investment_id);

        if (!$invoice) {
            return $this->sendError('Not Found!', Response::HTTP_NOT_FOUND);
        }

        $product        = $invoice->product->only('id', 'name', 'product_pict', 'price', 'created_at');
        $qty            = $invoice->qty;
        $amount         = $qty * $invoice->product->production_price;

        $data           = [
            'product'               => $product,
            'qty'                   => $qty,
            'amount'                => $amount
        ];

        return $this->sendResponse('Berhasil menampilkan data.', $data);
    }

    public function checkout_product(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id'    => 'required|numeric|exists:products,id',
            'qty'           => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $product        = Product::find($request->product_id);

        DB::beginTransaction();
        try {
            $invoice_number = 'INV/' . now()->format('dmY') . '/' . Str::upper(Str::random(12));
            $qty            = (int) $request->qty;

            Auth::user()->restoInvoices()->create([
                'product_id'        => $request->product_id,
                'invoice_number'    => $invoice_number,
                'qty'               => $request->qty,
                'total'             => $qty * $product->price,
                'status'            => 6 // Status menunggu konfirmasi UMKM
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendErrorException($e->getMessage());
        }

        return $this->sendResponse('Berhasil melakukan checkout.');
    }

    public function checkout_investment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'investment_id'    => 'required|numeric|exists:invoices,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $invoice        = Invoice::whereNull('investor_id')->find($request->investment_id);

        if (!$invoice) {
            return $this->sendError('Not Found!', Response::HTTP_NOT_FOUND);
        }

        DB::beginTransaction();
        try {
            $invoice->update(['investor_id' => Auth::id(), 'status' => 2]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendErrorException($e->getMessage());
        }

        return $this->sendResponse('Berhasil melakukan checkout.');
    }
}
