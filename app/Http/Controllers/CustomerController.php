<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class CustomerController extends Controller
{
    public function index(): Response
    {
        $customers = Customer::withCount('washes')
            ->latest()
            ->paginate(20);

        return Inertia::render('Customers/Index', [
            'customers' => $customers,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255|unique:customers',
            'email' => 'nullable|email|max:255',
            'plate_number' => 'required|string|max:255',
            'vehicle_type' => 'required|string|max:255',
            'make' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'membership' => 'string|max:255',
            'status' => 'string|max:255',
        ]);

        Customer::create($validated);

        return back()->with('success', 'Customer created successfully.');
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255|unique:customers,phone,'.$customer->id,
            'email' => 'nullable|email|max:255',
            'plate_number' => 'required|string|max:255',
            'vehicle_type' => 'required|string|max:255',
            'make' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'membership' => 'string|max:255',
            'status' => 'string|max:255',
        ]);

        $customer->update($validated);

        return back()->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return back()->with('success', 'Customer deleted successfully.');
    }

    public function show(Customer $customer): Response
    {
        $customer->load(['washes.package', 'washes.bay']);

        return Inertia::render('Customers/Show', [
            'customer' => $customer,
        ]);
    }
}
