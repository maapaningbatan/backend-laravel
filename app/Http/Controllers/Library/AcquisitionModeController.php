<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibAcquisitionMode; // ✅ Correct model name


class AcquisitionModeController extends Controller
{
    public function index()
    {
        $modes = LibAcquisitionMode::select('id', 'acquisition_desc')
            ->where('is_deleted', 0)
            ->orderBy('acquisition_desc')
            ->get();

        return response()->json(['data' => $modes]);
    }
}
