<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        $query = Transaction::with('category')->latest('date');

        if (!empty($validated['from'])) {
            $query->whereDate('date', '>=', $validated['from']);
        }
        if (!empty($validated['to'])) {
            $query->whereDate('date', '<=', $validated['to']);
        }
        if (!empty($validated['category_id'])) {
            $query->where('category_id', $validated['category_id']);
        }

        return response()->json($query->paginate(25));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'date' => ['required', 'date'],
        ]);

        $transaction = Transaction::create($data)->load('category');

        return response()->json($transaction, 201);
    }

    public function update(Request $request, Transaction $transaction): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'amount' => ['sometimes', 'required', 'numeric'],
            'category_id' => ['sometimes', 'required', 'integer', 'exists:categories,id'],
            'date' => ['sometimes', 'required', 'date'],
        ]);

        $transaction->update($data);

        return response()->json($transaction->fresh('category'));
    }

    public function destroy(Transaction $transaction): JsonResponse
    {
        $transaction->delete();
        return response()->json(['status' => 'deleted']);
    }
}


