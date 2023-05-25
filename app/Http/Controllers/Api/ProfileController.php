<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\RefSetting;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ProfileController extends BaseController
{
    public function index()
    {
        $user = Auth::user();

        switch ($user->role) {
            case 'RESTO':
                $data = [
                    'name'          => $user->name,
                    'email'         => $user->email,
                    'phone'         => $user->phone,
                    'profile_pict'  => $user->profile_pict,
                    'created_at'    => $user->created_at
                ];
                break;

            case 'INVESTOR':
                $profit             = RefSetting::where('key', 'profit_investor')->first();

                $data = [
                    'name'          => $user->name,
                    'email'         => $user->email,
                    'phone'         => $user->phone,
                    'profile_pict'  => $user->profile_pict,
                    'total_profit'  => $user->investorInvoices->sum(fn ($v) => ($v->product->price - $v->product->production_price) * ((int) $profit->value / 100) * $v->qty),
                    'created_at'    => $user->created_at
                ];
                break;

            case 'ENTERPRISE':
                $profit             = RefSetting::where('key', 'profit_enterprise')->first();
                $total_profit       = Invoice::query()
                    ->whereHas('product.enterprise', fn ($q) => $q->where('user_id', $user->id))
                    ->whereIn('status', [2, 3, 4])
                    ->get()
                    ->sum(fn ($v) => ($v->product->price - $v->product->production_price) * ((int) $profit->value / 100) * $v->qty);

                $data = [
                    'name'          => $user->name,
                    'email'         => $user->email,
                    'phone'         => $user->phone,
                    'profile_pict'  => $user->profile_pict,
                    'total_profit'  => $total_profit,
                    'created_at'    => $user->created_at
                ];
                break;

            default:
                return $this->sendError('Forbidden!', Response::HTTP_FORBIDDEN);
                break;
        }

        return $this->sendResponse('Berhasil menampilkan data.', $data);
    }
}
