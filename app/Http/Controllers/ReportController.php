<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function expense(Request $request, int $id)
    {
        $wallet = Wallet::where('id', $id)->first();

        $validated = $request->validate([
            'month' => 'sometimes|integer|between:1,12',
            'year' => 'sometimes|integer',
        ]);

        $transaction = $wallet->transactions()->whereRelation('category', 'type', 'EXPENSE')->when(isset($validated['month']), function ($query) use ($validated) {
            $query->whereMonth('date', $validated['month']);
        })->when(isset($validated['year']), function ($query) use ($validated) {
            $query->whereYear('date', $validated['year']);
        })->get();

        return response()->json([
            "status" => "success",
            "message" => "Get summary by expense category successful",
            'data' => [
                'summary' => $transaction->map(function ($transaction) {
                    return [
                        "category" => [
                            "id" => $transaction->category->id,
                            "name" => $transaction->category->name,
                            "icon" => $transaction->category->icon,
                            "color" => $transaction->category->color,
                            "type" => $transaction->category->type,
                            "created_at" => $transaction->category->created_at,
                            "updated_at" => $transaction->category->updated_at
                        ],
                        "amount" => $transaction->amount
                    ];
                })
            ]
        ], 200);
    }

    public function income(Request $request, int $id)
    {
        $wallet = Wallet::where('id', $id)->first();

        $validated = $request->validate([
            'month' => 'sometimes|integer|between:1,12',
            'year' => 'sometimes|integer',
        ]);

        $transaction = $wallet->transactions()->whereRelation('category', 'type', 'INCOME')->when(isset($validated['month']), function ($query) use ($validated) {
            $query->whereMonth('date', $validated['month']);
        })->when(isset($validated['year']), function ($query) use ($validated) {
            $query->whereYear('date', $validated['year']);
        })->get();

        return response()->json([
            "status" => "success",
            "message" => "Get summary by income category successful",
            'data' => [
                'summary' => $transaction->map(function ($transaction) {
                    return [
                        "category" => [
                            "id" => $transaction->category->id,
                            "name" => $transaction->category->name,
                            "icon" => $transaction->category->icon,
                            "color" => $transaction->category->color,
                            "type" => $transaction->category->type,
                            "created_at" => $transaction->category->created_at,
                            "updated_at" => $transaction->category->updated_at
                        ],
                        "amount" => $transaction->amount
                    ];
                })
            ]
        ], 200);
    }
}
