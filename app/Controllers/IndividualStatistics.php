<?php
namespace App\Controllers;

use App\Models\StravaActivityModel;

class IndividualStatistics extends BaseController
{
    public function index()
    {
        $activityModel = new StravaActivityModel();
        $data = $activityModel->getStatsData(); 

        return view('individual_leaderboard_view', ['tableData' => json_encode($data)]); // Load the new view
    }
}