<?php

namespace App\Http\Controllers;

class BaseController extends Controller
{
    public function sendResponse($message, $data = [], $code = \Illuminate\Http\Response::HTTP_OK)
    {
        $res = [
            'success'   => true,
            'message'   => $message,
        ];

        if (!empty($data)) {
            $res['data'] = $data;
        }

        return response()->json($res, $code);
    }

    public function sendError($message, $code = \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY)
    {
        $res = [
            'success'   => false,
            'message'   => $message
        ];

        return response()->json($res, $code);
    }

    public function sendErrorException($message, $code = \Illuminate\Http\Response::HTTP_BAD_REQUEST)
    {
        $res = [
            'success'   => false,
            'message'   => config('app.env') == 'production' ? 'Terjadi kesalahan server' : $message
        ];

        return response()->json($res, $code);
    }

    public function mapPaginate($data)
    {
        $data = $data->toArray();

        return [
            "current_page"  => $data["current_page"],
            "from"          => $data["from"],
            "last_page"     => $data["last_page"],
            "per_page"      => (int) $data["per_page"],
            "to"            => $data["to"],
            "total"         => $data["total"],
            "data"          => $data["data"]
        ];
    }
}
