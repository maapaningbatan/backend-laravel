<?php

namespace App\Http\Controllers\LibraryController;

use App\Http\Controllers\Controller;
use App\Models\Library\LibOffice;

class OfficeController extends Controller
{
    public function index()
    {
        return LibOffice::orderBy('Office')
            ->get([
                'Office_Id',
                'Office',
                'Office_Desc',
                'OBS_Head',
                'Region',
                'Cluster',
                'Bldg_Id'
            ]);
    }

    public function getByCluster($clusterId)
    {
        return LibOffice::where('Cluster', $clusterId)
            ->orderBy('Office')
            ->get([
                'Office_Id',
                'Office',
                'Office_Desc',
                'OBS_Head',
                'Region',
                'Cluster',
                'Bldg_Id'
            ]);
    }
    
    public function getByRegion($regionId)
{
    return LibOffice::where('Region', $regionId)
        ->orderBy('Office')
        ->get([
            'Office_Id',
            'Office',
            'Office_Desc',
            'OBS_Head',
            'Region',
            'Cluster',
            'Bldg_Id'
        ]);
}

}
