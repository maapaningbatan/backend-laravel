<?php

namespace App\Http\Controllers\LibraryController;

use App\Http\Controllers\Controller;
use App\Models\Library\LibCluster;

class ClusterController extends Controller
{
    public function index()
    {
        // SoftDeletes will automatically exclude deleted rows
        return LibCluster::orderBy('Cluster')
            ->select('Cluster_Id', 'Cluster', 'Cluster_Desc', 'Region')
            ->get();
    }
}
