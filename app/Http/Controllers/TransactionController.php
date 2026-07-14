<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'wallet_id' => 'exists:wallets,id',
                'category_id' => 'exists:categories,id',
                'amount' => 'required|integer|min:1',
                'date' => ['required', Rule::date()->format('Y-m-d')],
                'note' => 'nullable'
            ]);

            $t = Transaction::create([
                'wallet_id' => $request->wallet_id,
                'category_id' => $request->category_id,
                'amount' => $request->amount,
                'date' => $request->date,
                'note' => $request->note
            ]);

            return response()->json([
                "status" => "success",
                "message" => "Transaction added successful",
                'data' =>  [
                    "category_id" => $t->category_id,
                    "wallet_id" => $t->wallet_id,
                    "amount" => $t->amount,
                    "note" => $t->note,
                    "date" => $t->date,
                    "updated_at" => $t->updated_at,
                    "created_at" => $t->created_at,
                    "id" => $t->id
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

    public function delete(Request $request, int $id)
    {
        $t = Transaction::where('id', $id)->first();

        if (!$t) {
            return response()->json([
                "status" => "error",
                "message" => "Not found"
            ], 404);
        }

        if ($request->user()->id !== $t->wallet->user_id) {
            return response()->json([
                "status" => "error",
                "message" => "Forbidden access"
            ], 403);
        }

        $t->delete();

        return response()->json([
            "status" => "success",
            "message" => "Transaction deleted successful"
        ], 200);
    }

    public function all(Request $request) {
        $validated = $request->validate([
            'page' => 'nullable|integer',
            'per_page' => 'nullable|integer',
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer'
        ]);

        $t = Transaction::query()->whereRelation('wallet', 'user_id', $request->user()->id)->when(isset($validated['month']), )
    }
}
