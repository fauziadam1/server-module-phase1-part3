<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WalletController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'currency_code' => 'required|exists:currencies,code'
            ]);

            $currency = Currency::where('code', $request->currency_code)->first();

            $wallet = Wallet::create([
                'name' => $request->name,
                'user_id' => $request->user()->id,
                'currency_id' => $currency->id,
            ]);

            return response()->json([
                "status" => "success",
                "message" => "Wallet added successful",
                "data" => [
                    "name" => $wallet->name,
                    "user_id" => $wallet->user_id,
                    "updated_at" => $wallet->updated_at,
                    "created_at" => $wallet->created_at,
                    "id" => $wallet->id,
                    "currency_code" => $currency->code
                ]
            ], 201);
        } catch (ValidationException $error) {
            return response()->json([
                "status" => "error",
                "message" => "Invalid field",
                'errors' => $error->errors()
            ], 422);
        }
    }

    public function update(Request $request, int $id)
    {
        $wallet = Wallet::where('id', $id)->first();

        if (!$wallet) {
            return response()->json([
                "status" => "error",
                "message" => "Not found"
            ], 404);
        }

        if ($wallet->user_id !== $request->user()->id) {
            return response()->json([
                "status" => "error",
                "message" => "Forbidden access"
            ], 403);
        }

        $data = $request->validate([
            'name' => 'required',
        ]);

        $wallet->update($data);

        return response()->json([
            "status" => "success",
            "message" => "Wallet updated successful",
            "data" => [
                "id" => $wallet->id,
                "user_id" => $wallet->user_id,
                "name" => $wallet->name,
                "created_at" => $wallet->created_at,
                "updated_at" => $wallet->updated_at,
                "deleted_at" => $wallet->deleted_at,
                "currency_code" => $wallet->currency->code
            ]
        ], 200);
    }

    public function delete(Request $request, int $id)
    {
        $wallet = Wallet::where('id', $id)->first();

        if (!$wallet) {
            return response()->json([
                "status" => "error",
                "message" => "Not found"
            ], 404);
        }

        if ($wallet->user_id !== $request->user()->id) {
            return response()->json([
                "status" => "error",
                "message" => "Forbidden access"
            ], 403);
        }

        $wallet->update(['deleted_at' => date('Y-m-d')]);

        return response()->json([
            "status" => "success",
            "message" => "Wallet deleted successful",
        ]);
    }

    public function all(Request $request)
    {
        $wallet = Wallet::where('user_id', $request->user()->id)->where('deleted_at', null)->get();

        return response()->json([
            "status" => "success",
            "message" => "Get all wallets successful",
            'data' => [
                'wallets' => $wallet->map(function ($wallet) {
                    $transactions = $wallet->transactions()->with('category')->get();

                    $balance = 0;

                    $transactions->map(function ($t) use (&$balance) {
                        if ($t->category->type === "INCOME") {
                            $balance += $t->amount;
                        } else {
                            $balance -= $t->amount;
                        }
                    });


                    return [
                        "id" => $wallet->id,
                        "user_id" => $wallet->user_id,
                        "name" => $wallet->name,
                        "created_at" => $wallet->created_at,
                        "updated_at" => $wallet->updated_at,
                        "deleted_at" => $wallet->deleted_at,
                        "currency_code" => $wallet->currency->code,
                        "balance" => $balance
                    ];
                })
            ]
        ]);
    }

    public function index(Request $request, int $id)
    {
        $wallet = Wallet::where('id', $id)->where('deleted_at', null)->first();

        if (!$wallet) {
            return response()->json([
                "status" => "error",
                "message" => "Not found"
            ], 404);
        }

        if ($wallet->user_id !== $request->user()->id) {
            return response()->json([
                "status" => "error",
                "message" => "Forbidden access"
            ], 403);
        }

        $transactions = $wallet->transactions()->with('category')->get();

        $balance = 0;

        $transactions->map(function ($t) use (&$balance) {
            if ($t->category->type === "INCOME") {
                $balance += $t->amount;
            } else {
                $balance -= $t->amount;
            }
        });

        return response()->json([
            "status" => "success",
            "message" => "Get detail wallet successful",
            "data" => [
                "id" => $wallet->id,
                "user_id" => $wallet->user_id,
                "name" => $wallet->name,
                "created_at" => $wallet->created_at,
                "updated_at" => $wallet->updated_at,
                "deleted_at" => $wallet->deleted_at,
                "currency_code" => $wallet->currency->code,
                "balance" => $balance
            ]
        ]);
    }
}
