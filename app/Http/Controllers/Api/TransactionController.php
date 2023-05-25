<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\RefSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $user               = Auth::user();
        $keyword            = $request->keyword;
        $sort               = strtolower($request->input('sort', 'desc'));
        $perPage            = $request->input('per_page', 10);

        switch ($user->role) {
            case 'RESTO':
                $data       = $user->restoInvoices()
                    ->select('id', 'invoice_number', 'qty', 'total', 'status', 'created_at')
                    ->addSelect([
                        'name'              => Product::select('name')->whereColumn('invoices.product_id', 'products.id'),
                        'product_pict'      => Product::select('product_pict')->whereColumn('invoices.product_id', 'products.id'),
                        'price'             => Product::select('price')->whereColumn('invoices.product_id', 'products.id'),
                    ])
                    ->when($keyword, fn ($q) => $q->where(
                        fn ($q) => $q
                            ->whereHas('product', fn ($q) => $q->where('name', 'like', "%$keyword%"))
                            ->orWhereHas('product', fn ($q) => $q->where('description', 'like', "%$keyword%"))
                            ->orWhere('invoice_number', 'like', "%$keyword%")
                    ))
                    ->when($sort == 'asc', fn ($q) => $q->oldest(), fn ($q) => $q->latest())
                    ->paginate($perPage);
                break;

            case 'INVESTOR':
                $profit     = RefSetting::where('key', 'profit_investor')->first();
                $data       = $user->investorInvoices()
                    ->select('id', 'invoice_number', 'qty', 'total', 'status', 'created_at')
                    ->addSelect([
                        'name'              => Product::select('name')->whereColumn('invoices.product_id', 'products.id'),
                        'product_pict'      => Product::select('product_pict')->whereColumn('invoices.product_id', 'products.id'),
                        'price'             => Product::select('price')->whereColumn('invoices.product_id', 'products.id'),
                        'production_price'  => Product::select('production_price')->whereColumn('invoices.product_id', 'products.id'),
                        'description'       => Product::select('description')->whereColumn('invoices.product_id', 'products.id'),
                    ])
                    ->when($keyword, fn ($q) => $q->where(
                        fn ($q) => $q
                            ->whereHas('product', fn ($q) => $q->where('name', 'like', "%$keyword%"))
                            ->orWhereHas('product', fn ($q) => $q->where('description', 'like', "%$keyword%"))
                            ->orWhere('invoice_number', 'like', "%$keyword%")
                    ))
                    ->when($sort == 'asc', fn ($q) => $q->oldest(), fn ($q) => $q->latest())
                    ->get()
                    ->map(fn ($v) => collect($v)->replace([
                        'profit'    => ($v->price - $v->production_price) * ((int) $profit->value / 100) * $v->qty,
                        'total'     => $v->production_price * $v->qty
                    ])->except(['price', 'description']))
                    ->paginate($perPage);
                break;

            case 'ENTERPRISE':
                $profit     = RefSetting::where('key', 'profit_enterprise')->first();
                $data       = Invoice::query()
                    ->select('id', 'invoice_number', 'qty', 'total', 'status', 'created_at')
                    ->addSelect([
                        'name'              => Product::select('name')->whereColumn('invoices.product_id', 'products.id'),
                        'product_pict'      => Product::select('product_pict')->whereColumn('invoices.product_id', 'products.id'),
                        'price'             => Product::select('price')->whereColumn('invoices.product_id', 'products.id'),
                        'production_price'  => Product::select('production_price')->whereColumn('invoices.product_id', 'products.id'),
                        'description'       => Product::select('description')->whereColumn('invoices.product_id', 'products.id'),
                    ])
                    ->when($keyword, fn ($q) => $q->where(
                        fn ($q) => $q
                            ->whereHas('product', fn ($q) => $q->where('name', 'like', "%$keyword%"))
                            ->orWhereHas('product', fn ($q) => $q->where('description', 'like', "%$keyword%"))
                            ->orWhere('invoice_number', 'like', "%$keyword%")
                    ))
                    ->when($sort == 'asc', fn ($q) => $q->oldest(), fn ($q) => $q->latest())
                    ->whereHas('product.enterprise', fn ($q) => $q->where('user_id', $user->id))
                    ->where('status', '!=', 6)
                    ->get()
                    ->map(fn ($v) => collect($v)->replace([
                        'profit'    => ($v->price - $v->production_price) * ((int) $profit->value / 100) * $v->qty,
                        'total'     => $v->production_price * $v->qty
                    ])->except(['price', 'description']))
                    ->paginate($perPage);
                break;

            case 'ADMIN':
                $profit     = RefSetting::where('key', 'profit_admin')->first();

                $data       = Invoice::query()
                    ->select('id', 'invoice_number', 'qty', 'total', 'status', 'created_at')
                    ->addSelect([
                        'resto_name'        => User::select('name')->whereColumn('invoices.resto_id', 'users.id'),
                        'investor_name'     => User::select('name')->whereColumn('invoices.investor_id', 'users.id'),
                        'name'              => Product::select('name')->whereColumn('invoices.product_id', 'products.id'),
                        'product_pict'      => Product::select('product_pict')->whereColumn('invoices.product_id', 'products.id'),
                        'price'             => Product::select('price')->whereColumn('invoices.product_id', 'products.id'),
                        'production_price'  => Product::select('production_price')->whereColumn('invoices.product_id', 'products.id'),
                    ])
                    ->when($keyword, fn ($q) => $q->where(
                        fn ($q) => $q
                            ->whereHas('product', fn ($q) => $q->where('name', 'like', "%$keyword%"))
                            ->orWhereHas('product', fn ($q) => $q->where('description', 'like', "%$keyword%"))
                            ->orWhere('invoice_number', 'like', "%$keyword%")
                    ))
                    ->when($sort == 'asc', fn ($q) => $q->oldest(), fn ($q) => $q->latest())
                    ->get()
                    ->map(fn ($v) => collect($v)->replace([
                        'profit'            => ($v->price - $v->production_price) * ((int) $profit->value / 100) * $v->qty,
                        'production_total'  => $v->production_price * $v->qty
                    ])->except(['description']))
                    ->paginate($perPage);
                break;

            default:
                return $this->sendError('Forbidden!', Response::HTTP_FORBIDDEN);
                break;
        }

        return $this->sendResponse('Berhasil menampilkan data.', $this->mapPaginate($data));
    }

    public function show(Invoice $invoice): JsonResponse
    {
        $user               = Auth::user();

        switch ($user->role) {
            case 'RESTO':
                $data   = [
                    'id'                => $invoice->id,
                    'invoice_number'    => $invoice->invoice_number,
                    'name'              => $invoice->product->name,
                    'product_pic'       => $invoice->product->product_pict,
                    'price'             => $invoice->product->price,
                    'qty'               => $invoice->qty,
                    'total'             => $invoice->total,
                    'status'            => $invoice->status,
                    'created_at'        => $invoice->created_at
                ];
                break;

            case 'INVESTOR':
                $profit             = RefSetting::where('key', 'profit_investor')->first();
                $profit_investor    = ($invoice->product->price - $invoice->product->production_price) * ((int) $profit->value / 100) * $invoice->qty;

                $data = [
                    'id'                => $invoice->id,
                    'invoice_number'    => $invoice->invoice_number,
                    'name'              => $invoice->product->name,
                    'product_pic'       => $invoice->product->product_pict,
                    'price'             => $invoice->product->price,
                    'qty'               => $invoice->qty,
                    'total'             => $invoice->total,
                    'profit'            => $profit_investor,
                    'status'            => $invoice->status,
                    'created_at'        => $invoice->created_at
                ];
                break;

            case 'ENTERPRISE':
                $profit             = RefSetting::where('key', 'profit_enterprise')->first();
                $profit_enterprise  = ($invoice->product->price - $invoice->product->production_price) * ((int) $profit->value / 100) * $invoice->qty;

                $data = [
                    'id'                => $invoice->id,
                    'invoice_number'    => $invoice->invoice_number,
                    'name'              => $invoice->product->name,
                    'product_pic'       => $invoice->product->product_pict,
                    'price'             => $invoice->product->price,
                    'qty'               => $invoice->qty,
                    'total'             => $invoice->total,
                    'profit'            => $profit_enterprise,
                    'status'            => $invoice->status,
                    'created_at'        => $invoice->created_at
                ];
                break;

            case 'ADMIN':
                $profit                 = $invoice->product->price - $invoice->product->production_price;

                $ref_profit_admin       = RefSetting::where('key', 'profit_admin')->first();
                $profit_admin           = $profit * ((int) $ref_profit_admin->value / 100) * $invoice->qty;

                $ref_profit_enterprise  = RefSetting::where('key', 'profit_enterprise')->first();
                $profit_enterprise      = $profit * ((int) $ref_profit_enterprise->value / 100) * $invoice->qty;

                $ref_profit_investor    = RefSetting::where('key', 'profit_investor')->first();
                $profit_investor        = $profit * ((int) $ref_profit_investor->value / 100) * $invoice->qty;

                $data = [
                    'id'                => $invoice->id,
                    'invoice_number'    => $invoice->invoice_number,
                    'name'              => $invoice->product->name,
                    'product_pic'       => $invoice->product->product_pict,
                    'price'             => $invoice->product->price,
                    'qty'               => $invoice->qty,
                    'total'             => $invoice->total,
                    'profit_admin'      => $profit_admin,
                    'profit_enterprise' => $profit_enterprise,
                    'profit_investor'   => $profit_investor,
                    'status'            => $invoice->status,
                    'created_at'        => $invoice->created_at
                ];

                break;

            default:
                return $this->sendError('Forbidden!', Response::HTTP_FORBIDDEN);
                break;
        }

        return $this->sendResponse('Berhasil menampilkan data.', $data);
    }

    public function approve(Invoice $invoice): JsonResponse
    {
        $user = Auth::user();

        if ($invoice->status != 6 && $user->role == 'ENTERPRISE') {
            return $this->sendError('Transaksi hanya dapat disetujui ketika status menunggu konfirmasi UMKM.');
        }

        DB::beginTransaction();
        try {
            $invoice->update(['status' => 1]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendErrorException($e->getMessage());
        }

        return $this->sendResponse('Berhasil menyetujui transaksi.');
    }

    public function cancel(Invoice $invoice): JsonResponse
    {
        $user = Auth::user();

        if ($invoice->status != 6 && $user->role == 'ENTERPRISE') {
            return $this->sendError('Transaksi hanya dapat dibatalkan ketika status menunggu konfirmasi UMKM.');
        }

        if ($invoice->status != 1 && $user->role == 'RESTO') {
            return $this->sendError('Transaksi hanya dapat dibatalkan ketika status menunggu konfirmasi.');
        }

        if ($invoice->product->enterprise->user_id != $user->id && $user->role == 'ENTERPRISE') {
            return $this->sendError('Forbidden!', Response::HTTP_FORBIDDEN);
        }

        if ($invoice->resto_id != $user->id && $user->role == 'RESTO') {
            return $this->sendError('Forbidden!', Response::HTTP_FORBIDDEN);
        }

        DB::beginTransaction();
        try {
            $invoice->update(['status' => 5]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendErrorException($e->getMessage());
        }

        return $this->sendResponse('Berhasil membatalkan transaksi.');
    }

    public function update_status(Invoice $invoice, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status'    => 'required|numeric|exists:ref_status_transactions,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            $data = [
                'status' => $request->status,
            ];

            if ($request->status == 1) {
                $data = array_merge($data, ['investor_id' => null]);
            }

            $invoice->update(['status' => $request->status]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendErrorException($e->getMessage());
        }

        return $this->sendResponse('Berhasil mengubah status transaksi.');
    }
}
