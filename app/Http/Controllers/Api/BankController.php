<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\RefSetting;
use Illuminate\Http\Request;

class BankController extends BaseController
{
    public function index()
    {
        $account_name   = RefSetting::where('key', 'account_bank_name')->first();
        $account_number = RefSetting::where('key', 'account_bank_number')->first();

        return $this->sendResponse('Berhasil menampilkan data.', [
            'bank_account_name'     => $account_name->value,
            'bank_account_number'   => $account_number->value,
        ]);
    }
}
