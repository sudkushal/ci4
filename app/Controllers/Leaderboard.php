<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\LeaderboardModel;
use App\Config\AppConstants;

class Leaderboard extends BaseController
{
    public function index()
    {
        $leaderboardModel = new LeaderboardModel();

        $data['leaderboard_data'] = $leaderboardModel->get_leaderboard_data();

        return view('leaderboard_view', $data);
    }
}
