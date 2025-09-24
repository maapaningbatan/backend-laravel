<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Library\LibOffice;

class OfficeController extends Controller
{
    public function index()
    {
        return LibOffice::orderBy('office_desc')
            ->get([
                'id',
                'office_code',
                'office_desc',
                'obs_head',
                'region_id',
                'cluster_id',
                'bldg_id',
            ]);
    }

    public function getByCluster($clusterId)
    {
        return LibOffice::where('cluster_id', $clusterId)
            ->orderBy('office_desc')
            ->get([
                'id',
                'office_code',
                'office_desc',
                'obs_head',
                'region_id',
                'cluster_id',
                'bldg_id',
            ]);
    }

    public function getByRegion($regionId)
    {
        return LibOffice::where('region_id', $regionId)
            ->orderBy('office_desc')
            ->get([
                'id',
                'office_code',
                'office_desc',
                'obs_head',
                'region_id',
                'cluster_id',
                'bldg_id',
            ]);
    }
}
