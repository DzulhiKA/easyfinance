<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class TransactionController extends Controller
{
    /**
     * Get all transactions for authenticated user
     */
    public function index()
    {
        $userId = auth('api')->id();

        $transactions = Transaction::with('category')
            ->where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($transactions);
    }


    /**
     * Store new transaction
     */
    public function store(Request $request)
    {
        $user = auth('api')->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'type'        => 'required|in:income,expense',
            'amount'      => 'required|numeric|min:0',
            'date'        => 'required|date',
            'description' => 'nullable|string|max:500',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Pastikan category milik user
        $category = Category::where('id', $validated['category_id'])
            ->where('user_id', $user->id)
            ->first();

        if (! $category) {
            return response()->json([
                'message' => 'Category does not belong to you.'
            ], 403);
        }

        // Upload image
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('transactions', 'public');
        }

        $transaction = Transaction::create([
            'user_id'     => $user->id,
            'category_id' => $validated['category_id'],
            'type'        => $validated['type'],
            'amount'      => $validated['amount'],
            'date'        => $validated['date'],
            'description' => $validated['description'] ?? null,
            'image'       => $imagePath,
        ]);

        $transaction->image_url = $transaction->image
            ? asset('storage/' . $transaction->image)
            : null;

        return response()->json($transaction, 201);
    }

    /**
     * Delete transaction
     */
    public function destroy(Transaction $transaction)
    {
        $user = auth('api')->user();

        if (! $user || $transaction->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($transaction->image) {
            Storage::disk('public')->delete($transaction->image);
        }

        $transaction->delete();

        return response()->json([
            'message' => 'Transaction deleted successfully'
        ]);
    }

    /**
     * Update transaction
     */    public function update(Request $request, $id)
    {
        $user = auth('api')->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $transaction = Transaction::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
        if (! $transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'type'        => 'required|in:income,expense',
            'amount'      => 'required|numeric|min:0',
            'date'        => 'required|date',
            'description' => 'nullable|string|max:500',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Pastikan category milik user
        $category = Category::where('id', $validated['category_id'])
            ->where('user_id', $user->id)
            ->first();

        if (! $category) {
            return response()->json([
                'message' => 'Category does not belong to you.'
            ], 403);
        }

        // Handle image update
        if ($request->hasFile('image')) {
            if ($transaction->image) {
                Storage::disk('public')->delete($transaction->image);
            }
            $imagePath = $request->file('image')->store('transactions', 'public');
        } else {
            $imagePath = $transaction->image;
        }

        // Update transaction
        $transaction->update([
            'category_id' => 'required|exists:categories,id',
            'type'        => 'required|in:income,expense',
            'amount'      => 'required|numeric|min:0',
            'date'        => 'required|date',
            'description' => 'nullable|string|max:500',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        // Return updated transaction with image URL
        if ($transaction->image) {
            $transaction->image_url = asset('storage/' . $transaction->image);
        }

        return response()->json($transaction);
    }
}