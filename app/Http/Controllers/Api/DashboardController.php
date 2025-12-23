<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    public function chart(Request $request)
{
    $userId = auth('api')->id();
    $year = $request->query('year', date('Y'));

    // Ambil total income & expense per bulan
    $transactions = DB::table('transactions')
        ->selectRaw('
            MONTH(date) as month,
            SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as income,
            SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as expense
        ')
        ->where('user_id', $userId)
        ->whereYear('date', $year)
        ->groupByRaw('MONTH(date)')
        ->orderBy('month')
        ->get();

    // Default data 12 bulan
    $chart = collect(range(1, 12))->map(function ($month) use ($transactions) {
        $data = $transactions->firstWhere('month', $month);

        return [
            'month'   => $month,
            'income' => $data->income ?? 0,
            'expense'=> $data->expense ?? 0,
        ];
    });

    return response()->json([
        'year' => (int) $year,
        'data' => $chart,
    ]);
}

}
