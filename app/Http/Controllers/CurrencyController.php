<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function all()
    {
        $currency = Currency::all();

        return response()->json([
            "message" => "Get all currencies successful",
            'data' => [
                'currency' => $currency->map(function ($currency) {
                    return [
                        "id" => $currency->id,
                        "name" => $currency->name,
                        "symbol" => $currency->symbol,
                        "code" => $currency->code,
                        "created_at" => $currency->created_at,
                        "updated_at" => $currency->updated_at
                    ];
                })
            ]
        ], 200);
    }
}
