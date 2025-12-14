<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class PackageController extends Controller
{
    public function index(): Response
    {
        $packages = Package::withCount('washes')->get();

        return Inertia::render('Packages/Index', [
            'packages' => $packages,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'color' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        Package::create($validated);

        return back()->with('success', 'Package created successfully.');
    }

    public function update(Request $request, Package $package): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'color' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $package->update($validated);

        return back()->with('success', 'Package updated successfully.');
    }

    public function destroy(Package $package): RedirectResponse
    {
        $package->delete();

        return back()->with('success', 'Package deleted successfully.');
    }
}
