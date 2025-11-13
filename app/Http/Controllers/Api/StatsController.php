<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function totalsByCategory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $query = Transaction::query()
            ->select('categories.name as category', DB::raw('SUM(amount) as total'))
            ->join('categories', 'categories.id', '=', 'transactions.category_id')
            ->groupBy('categories.name')
            ->orderBy('categories.name');

        if (!empty($validated['from'])) {
            $query->whereDate('date', '>=', $validated['from']);
        }
        if (!empty($validated['to'])) {
            $query->whereDate('date', '<=', $validated['to']);
        }

        return response()->json($query->get());
    }
}


