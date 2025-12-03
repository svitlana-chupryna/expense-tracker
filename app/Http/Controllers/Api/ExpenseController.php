<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Expense::query()->orderBy('date', 'desc');

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        return response()->json($query->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $expense = Expense::create($request->all());

        return response()->json($expense, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $expense = Expense::findOrFail($id);
        return response()->json($expense);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $expense = Expense::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $expense->update($request->all());

        return response()->json($expense);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return response()->json(null, 204);
    }

    /**
     * Get dashboard statistics
     */
    public function dashboard(Request $request)
    {
        // Build base query with filters
        $baseQuery = function() use ($request) {
            $query = Expense::query();
            
            // Filter by date range
            if ($request->has('start_date') && $request->start_date) {
                $query->where('date', '>=', $request->start_date);
            }
            if ($request->has('end_date') && $request->end_date) {
                $query->where('date', '<=', $request->end_date);
            }
            
            return $query;
        };

        // Get expenses by category for chart
        $byCategory = $baseQuery()
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderBy('category')
            ->get()
            ->map(function($item) {
                return [
                    'category' => $item->category,
                    'total' => (float) $item->total,
                ];
            });

        // Get total expenses
        $total = $baseQuery()->sum('amount');

        // Get expenses by month for trend (database-agnostic approach)
        $driver = config('database.default');
        $dateFormat = $driver === 'sqlite' 
            ? "strftime('%Y-%m', date)" 
            : "DATE_FORMAT(date, '%Y-%m')";
        
        $byMonth = $baseQuery()
            ->selectRaw("{$dateFormat} as month, SUM(amount) as total")
            ->groupBy(\DB::raw($dateFormat))
            ->orderBy(\DB::raw($dateFormat))
            ->get()
            ->map(function($item) {
                return [
                    'month' => $item->month,
                    'total' => (float) $item->total,
                ];
            });

        return response()->json([
            'byCategory' => $byCategory,
            'total' => (float) $total,
            'byMonth' => $byMonth,
        ]);
    }
}
