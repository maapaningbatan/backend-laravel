<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibCluster;

class ClusterController extends Controller
{
    public function index()
    {
        return LibCluster::orderBy('Cluster')
            ->select('Cluster_Id', 'Cluster', 'Cluster_Desc', 'Region')
            ->get();
    }
}
