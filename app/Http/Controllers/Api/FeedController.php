<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\RefSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedController extends BaseController
{
    public function index_enterprise(Request $request): JsonResponse
    {
        $keyword            = $request->keyword;
        $sort               = strtolower($request->input('sort', 'desc'));
        $perPage            = $request->input('per_page', 10);

        $profit_enterprise    = RefSetting::where('key', 'profit_enterprise')->first();

        $data = Invoice::query()
            ->select('id', 'qty', 'created_at')
            ->addSelect([
                'name'              => Product::select('name')->whereColumn('invoices.product_id', 'products.id'),
                'product_pict'      => Product::select('product_pict')->whereColumn('invoices.product_id', 'products.id'),
                'price'             => Product::select('price')->whereColumn('invoices.product_id', 'products.id'),
                'production_price'  => Product::select('production_price')->whereColumn('invoices.product_id', 'products.id'),
                'description'       => Product::select('description')->whereColumn('invoices.product_id', 'products.id'),
            ])
            ->when($keyword, fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', "%$keyword%")->orWhere('description', 'like', "%$keyword%")))
            ->when($sort == 'asc', fn ($q) => $q->oldest(), fn ($q) => $q->latest())
            ->where([
                'status'        => 6, // Menunggu konfirmasi UMKM
                'investor_id'    => null,
            ])
            ->whereHas('product', fn ($q) => $q->where('enterprise_id', Auth::user()->enterprise->id))
            ->get()
            ->map(fn ($v) => collect($v)->replace([
                'profit'    => ($v->price - $v->production_price) * ((int) $profit_enterprise->value / 100) * $v->qty,
                'total'     => $v->production_price * $v->qty
            ])->except(['price', 'description']))
            ->paginate($perPage);

        return $this->sendResponse('Berhasil menampilkan data.', $this->mapPaginate($data));
    }

    public function index_product(Request $request): JsonResponse
    {
        $user               = Auth::user();
        $keyword            = $request->keyword;
        $sort               = strtolower($request->input('sort', 'desc'));
        $perPage            = $request->input('per_page', 10);

        $data = Product::query()
            ->select('id', 'name', 'product_pict', 'price', 'created_at')
            ->when($keyword, fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', "%$keyword%")->orWhere('description', 'like', "%$keyword%")))
            ->when($sort == 'asc', fn ($q) => $q->oldest(), fn ($q) => $q->latest())
            ->when($user->role == 'ENTERPRISE', fn ($q) => $q->whereHas('enterprise', fn ($q) => $q->where('user_id', $user->id)))
            ->paginate($perPage);

        return $this->sendResponse('Berhasil menampilkan data.', $this->mapPaginate($data));
    }

    public function index_investment(Request $request): JsonResponse
    {
        $keyword            = $request->keyword;
        $sort               = strtolower($request->input('sort', 'desc'));
        $perPage            = $request->input('per_page', 10);

        $profit_investor    = RefSetting::where('key', 'profit_investor')->first();

        $data = Invoice::query()
            ->select('id', 'qty', 'created_at')
            ->addSelect([
                'name'              => Product::select('name')->whereColumn('invoices.product_id', 'products.id'),
                'product_pict'      => Product::select('product_pict')->whereColumn('invoices.product_id', 'products.id'),
                'price'             => Product::select('price')->whereColumn('invoices.product_id', 'products.id'),
                'production_price'  => Product::select('production_price')->whereColumn('invoices.product_id', 'products.id'),
                'description'       => Product::select('description')->whereColumn('invoices.product_id', 'products.id'),
            ])
            ->when($keyword, fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', "%$keyword%")->orWhere('description', 'like', "%$keyword%")))
            ->when($sort == 'asc', fn ($q) => $q->oldest(), fn ($q) => $q->latest())
            ->where([
                'status'        => 1, // Menunggu konfirmasi
                'investor_id'    => null
            ])
            ->get()
            ->map(fn ($v) => collect($v)->replace([
                'profit'    => ($v->price - $v->production_price) * ((int) $profit_investor->value / 100) * $v->qty,
                'total'     => $v->production_price * $v->qty
            ])->except(['price', 'description']))
            ->paginate($perPage);

        return $this->sendResponse('Berhasil menampilkan data.', $this->mapPaginate($data));
    }

    public function show_product(Product $product): JsonResponse
    {
        $data = $product->only('id', 'name', 'description', 'product_pict', 'price', 'created_at');

        return $this->sendResponse('Berhasil menampilkan data.', $data);
    }

    public function show_invoice(Invoice $invoice): JsonResponse
    {
        $profit_investor    = RefSetting::where('key', 'profit_investor')->first();

        $data = [
            'id'            => $invoice->id,
            'name'          => $invoice->product->name,
            'description'   => $invoice->product->description,
            'product_pict'  => $invoice->product->product_pict,
            'price'         => $invoice->product->production_price,
            'qty'           => $invoice->qty,
            'total'         => $invoice->product->production_price * $invoice->qty,
            'profit'        => ($invoice->product->price - $invoice->product->production_price) * ((int) $profit_investor->value / 100) * $invoice->qty,
            'created_at'    => $invoice->created_at
        ];

        return $this->sendResponse('Berhasil menampilkan data.', $data);
    }
}
