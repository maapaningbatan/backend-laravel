<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibCluster;

class ClusterController extends Controller
{
    public function index()
    {
        return LibCluster::orderBy('cluster_desc')
            ->select('id', 'cluster_code', 'cluster_desc', 'region_id')
            ->get();
    }
}
