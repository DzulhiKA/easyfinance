<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard summary
     * Optional query:
     * - month (1-12)
     * - year (YYYY)
     */
    public function summary(Request $request)
    {
        $userId = auth('api')->id();

        $month = $request->query('month');
        $year  = $request->query('year');

        $baseQuery = Transaction::where('user_id', $userId);

        if ($month) {
            $baseQuery->whereMonth('date', $month);
        }

        if ($year) {
            $baseQuery->whereYear('date', $year);
        }

        $totalIncome = (clone $baseQuery)
            ->where('type', 'income')
            ->sum('amount');

        $totalExpense = (clone $baseQuery)
            ->where('type', 'expense')
            ->sum('amount');

        return response()->json([
            'total_income'  => (float) $totalIncome,
            'total_expense' => (float) $totalExpense,
            'balance'       => (float) ($totalIncome - $totalExpense),
        ]);
    }
}
