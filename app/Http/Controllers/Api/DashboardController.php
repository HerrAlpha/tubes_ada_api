<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Invoice;
use App\Models\RefSetting;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    public function index()
    {
        $ref_profit = RefSetting::where('key', 'profit_admin')->first();
        $user       = User::where('role', '!=', 'ADMIN')->get();
        $invoice    = Invoice::get();

        $data   = [
            'total_investor'        => $user->where('role', 'INVESTOR')->count(),
            'total_resto'           => $user->where('role', 'RESTO')->count(),
            'total_transaction'     => $invoice->count(),
            'revenue'               => $invoice->sum(fn ($v) => $v->total * $v->qty),
            'pending_transaction'   => $invoice->whereIn('status', [1, 2, 3, 6])->sum(fn ($v) => $v->total * $v->qty),
            'canceled_transaction'  => $invoice->where('status', 5)->sum(fn ($v) => $v->total * $v->qty),
            'completed_transaction' => $invoice->where('status', 4)->sum(fn ($v) => $v->total * $v->qty),
            'pending_profit'        => $invoice->whereIn('status', [1, 2, 3, 6])->sum(fn ($v) => ($v->product->price - $v->product->production_price) * ((int) $ref_profit->value / 100) * $v->qty),
            'loss_profit'           => $invoice->where('status', 5)->sum(fn ($v) => ($v->product->price - $v->product->production_price) * ((int) $ref_profit->value / 100) * $v->qty),
            'fixed_profit'          => $invoice->where('status', 4)->sum(fn ($v) => ($v->product->price - $v->product->production_price) * ((int) $ref_profit->value / 100) * $v->qty)
        ];

        return $this->sendResponse('Berhasil menampilkan data', $data);
    }
}
