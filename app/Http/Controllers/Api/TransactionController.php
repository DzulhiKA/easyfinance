<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Get all transactions for authenticated user
     */
    public function index()
    {
        $transactions = auth()->user()
            ->transactions()
            ->with('category')
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($transactions);
    }

    /**
     * Store new transaction
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'type'        => 'required|in:income,expense',
            'amount'      => 'required|numeric|min:0',
            'date'        => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);

        // Pastikan category milik user yang login
        $category = Category::where('id', $validated['category_id'])
            ->where('user_id', auth()->id())
            ->first();

        if (! $category) {
            abort(403, 'Category does not belong to you.');
        }

        $transaction = Transaction::create([
            'user_id'     => auth()->id(),
            'category_id' => $validated['category_id'],
            'type'        => $validated['type'],
            'amount'      => $validated['amount'],
            'date'        => $validated['date'],
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json($transaction, 201);
    }

    /**
     * Delete transaction
     */
    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $transaction->delete();

        return response()->json([
            'message' => 'Transaction deleted successfully'
        ]);
    }
}
