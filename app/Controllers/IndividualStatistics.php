<?php

namespace App\Controllers;

use App\Models\StravaActivityModel;

class IndividualStatistics extends BaseController
{
    public function index()
    {
        $activityModel = new StravaActivityModel();
        $data['analytics'] = $activityModel->getStatsData();

        return view('individual_leaderboard_view', $data);
    }
}
