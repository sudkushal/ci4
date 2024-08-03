<?php

namespace App\Controllers;

use App\Models\LeaderboardModel;
use CodeIgniter\Controller;

class LeaderboardController extends Controller
{
    public function index()
    {
        $model = new LeaderboardModel();

        // Retrieve data from each stage
        $data['stage1_data'] = $model->getStage1Data();
        $data['stage2_data'] = $model->getStage2Data();
        $data['stage3_data'] = $model->getStage3Data();
        $data['stage4_data'] = $model->getStage4Data();
        $data['stage5_data'] = $model->getStage5Data();
        $data['consolidated_data'] = $model->getConsolidatedData();

        // Load the view and pass the data
        echo view('stagewise_view', $data);
    }
}
