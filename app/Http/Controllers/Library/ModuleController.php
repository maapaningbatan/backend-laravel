<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibModule;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    // GET /api/modules
    public function index()
    {
        $modules = LibModule::whereNull('deleted_at')
            ->orderBy('module_name', 'asc')
            ->get(['id', 'module_name']); // only fetch what you need

        // Transform for Vue
        $modules = $modules->map(fn($m) => [
            'id' => $m->id,
            'name' => $m->module_name
        ]);

        return response()->json(LibModule::all());
    }
}
