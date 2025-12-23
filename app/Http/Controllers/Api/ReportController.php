<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;


class ReportController extends Controller
{
    /**
     * Report bulanan (detail transaksi)
     * /api/reports/monthly?month=1&year=2025
     */
    public function monthly(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer|min:2000',
        ]);

        $userId = auth('api')->id();

        $transactions = DB::table('transactions')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $userId)
            ->whereMonth('transactions.date', $request->month)
            ->whereYear('transactions.date', $request->year)
            ->orderBy('transactions.date')
            ->select(
                'transactions.date',
                'transactions.type',
                'transactions.amount',
                'transactions.description',
                'categories.name as category'
            )
            ->get();

        return response()->json([
            'month' => (int) $request->month,
            'year'  => (int) $request->year,
            'data'  => $transactions,
        ]);
    }

    /**
     * Report tahunan (summary per bulan)
     * /api/reports/yearly?year=2025
     */
    public function yearly(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000',
        ]);

        $userId = auth('api')->id();

        $rows = DB::table('transactions')
            ->selectRaw('
                MONTH(date) as month,
                SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as expense
            ')
            ->where('user_id', $userId)
            ->whereYear('date', $request->year)
            ->groupByRaw('MONTH(date)')
            ->orderBy('month')
            ->get();

        $data = collect(range(1, 12))->map(function ($m) use ($rows) {
            $row = $rows->firstWhere('month', $m);

            return [
                'month'   => $m,
                'income' => $row->income ?? 0,
                'expense' => $row->expense ?? 0,
                'balance' => ($row->income ?? 0) - ($row->expense ?? 0),
            ];
        });

        return response()->json([
            'year' => (int) $request->year,
            'data' => $data,
        ]);
    }

    /**
     * Report per kategori
     * /api/reports/category?month=1&year=2025
     */
    public function byCategory(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer|min:2000',
        ]);

        $userId = auth('api')->id();

        $rows = DB::table('transactions')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $userId)
            ->whereMonth('transactions.date', $request->month)
            ->whereYear('transactions.date', $request->year)
            ->groupBy('categories.name', 'transactions.type')
            ->selectRaw('
                categories.name as category,
                transactions.type,
                SUM(transactions.amount) as total
            ')
            ->orderBy('total', 'desc')
            ->get();

        return response()->json([
            'month' => (int) $request->month,
            'year'  => (int) $request->year,
            'data'  => $rows,
        ]);
    }
    public function monthlyPdf(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer|min:2000',
        ]);

        $userId = auth('api')->id();

        $transactions = DB::table('transactions')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $userId)
            ->whereMonth('transactions.date', $request->month)
            ->whereYear('transactions.date', $request->year)
            ->orderBy('transactions.date')
            ->select(
                'transactions.date',
                'transactions.type',
                'transactions.amount',
                'transactions.description',
                'categories.name as category'
            )
            ->get();

        $pdf = Pdf::loadView('reports.monthly', [
            'transactions' => $transactions,
            'month' => $request->month,
            'year'  => $request->year,
        ]);

        return $pdf->download(
            'laporan-bulanan-' . $request->month . '-' . $request->year . '.pdf'
        );
    }
    public function yearlyPdf(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000',
        ]);

        $userId = auth('api')->id();

        $rows = DB::table('transactions')
            ->selectRaw('
            MONTH(date) as month,
            SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as income,
            SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as expense
        ')
            ->where('user_id', $userId)
            ->whereYear('date', $request->year)
            ->groupByRaw('MONTH(date)')
            ->orderBy('month')
            ->get();

        $data = collect(range(1, 12))->map(function ($m) use ($rows) {
            $row = $rows->firstWhere('month', $m);

            $income = $row->income ?? 0;
            $expense = $row->expense ?? 0;

            return [
                'month'   => $m,
                'income' => $income,
                'expense' => $expense,
                'balance' => $income - $expense,
            ];
        });

        $pdf = Pdf::loadView('reports.yearly', [
            'year' => $request->year,
            'data' => $data,
        ]);

        return $pdf->download(
            'laporan-tahunan-' . $request->year . '.pdf'
        );
    }
}
