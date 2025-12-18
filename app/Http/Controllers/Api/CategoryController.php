<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get all categories owned by authenticated user
     */
    public function index()
    {
        return response()->json(
            auth('api')->user()->categories
        );
    }

    /**
     * Store new category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
        ]);

        $category = Category::create([
            'user_id' => auth('api')->id(),
            'name'    => $validated['name'],
            'type'    => $validated['type'],
        ]);

        return response()->json($category, 201);
    }

    /**
     * Update existing category
     */
    public function update(Request $request, Category $category)
    {
        $this->authorizeOwner($category);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:income,expense',
        ]);

        $category->update($validated);

        return response()->json($category);
    }

    /**
     * Delete category
     */
    public function destroy(Category $category)
    {
        $this->authorizeOwner($category);

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }

    /**
     * Ensure category belongs to authenticated user
     */
    private function authorizeOwner(Category $category): void
    {
        if ($category->user_id !== auth('api')->id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
