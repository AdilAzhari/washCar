<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class InventoryController extends Controller
{
    public function index(): Response
    {
        $items = InventoryItem::with('branch')->get();
        $branches = Branch::all();

        return Inertia::render('Inventory/Index', [
            'items' => $items,
            'branches' => $branches,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'min_quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:255',
            'unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        InventoryItem::create($validated);

        return back()->with('success', 'Inventory item created successfully.');
    }

    public function update(Request $request, InventoryItem $inventory): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'min_quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:255',
            'unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $inventory->update($validated);

        return back()->with('success', 'Inventory item updated successfully.');
    }

    public function destroy(InventoryItem $inventory): RedirectResponse
    {
        $inventory->delete();

        return back()->with('success', 'Inventory item deleted successfully.');
    }
}
