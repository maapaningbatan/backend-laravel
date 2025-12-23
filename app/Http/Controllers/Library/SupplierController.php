<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    /**
     * Display a listing of active suppliers.
     */
    public function index()
    {
        return response()->json(
            LibSupplier::select(
                'id',
                'supplier_name',
                'supplier_address',
                'contact_person',
                'contact_no',
                'tin_no'
            )
                ->where('is_archived', 0)
                ->orderBy('supplier_name')
                ->get()
        );
    }

    /**
     * Store a newly created supplier in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_address' => 'nullable|string|max:500',
            'contact_person' => 'nullable|string|max:255',
            'contact_no' => 'nullable|string|max:50',
            'tin_no' => 'nullable|string|max:50',
        ]);

        $supplier = LibSupplier::create([
            'supplier_name' => $request->supplier_name,
            'supplier_address' => $request->supplier_address,
            'contact_person' => $request->contact_person,
            'contact_no' => $request->contact_no,
            'tin_no' => $request->tin_no,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return response()->json($supplier, 201);
    }

    /**
     * Display the specified supplier.
     */
    public function show($id)
    {
        $supplier = LibSupplier::findOrFail($id);

        return response()->json($supplier);
    }

    /**
     * Update the specified supplier in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_address' => 'nullable|string|max:500',
            'contact_person' => 'nullable|string|max:255',
            'contact_no' => 'nullable|string|max:50',
            'tin_no' => 'nullable|string|max:50',
        ]);

        $supplier = LibSupplier::findOrFail($id);

        $supplier->update([
            'supplier_name' => $validated['supplier_name'],
            'supplier_address' => $validated['supplier_address'] ?? null,
            'contact_person' => $validated['contact_person'] ?? null,
            'contact_no' => $validated['contact_no'] ?? null,
            'tin_no' => $validated['tin_no'] ?? null,
            'updated_by' => Auth::id(),
        ]);

        return response()->json($supplier);
    }

    /**
     * Soft delete the specified supplier.
     */
    public function destroy($id)
    {
        $supplier = LibSupplier::findOrFail($id);
        $supplier->deleted_by = Auth::id();
        $supplier->save();
        $supplier->delete();

        return response()->json(['message' => 'Supplier deleted successfully']);
    }

    /**
     * Archive the specified supplier.
     */
    public function archive($id)
    {
        $supplier = LibSupplier::find($id);
        if (! $supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }

        $supplier->is_archived = 1;
        $supplier->save();

        return response()->json(['message' => 'Supplier archived successfully']);
    }

    /**
     * Display all archived suppliers.
     */
    public function archived()
    {
        $suppliers = LibSupplier::where('is_archived', 1)->get();

        return response()->json($suppliers);
    }

    /**
     * Restore an archived supplier.
     */
    public function restore($id)
    {
        $supplier = LibSupplier::findOrFail($id);
        $supplier->is_archived = 0;
        $supplier->save();

        return response()->json(['message' => 'Supplier restored successfully']);
    }

    /**
     * Permanently delete a supplier.
     */
    public function forceDelete($id)
    {
        $supplier = LibSupplier::findOrFail($id);
        $supplier->delete();

        return response()->json(['message' => 'Supplier permanently deleted']);
    }
}
