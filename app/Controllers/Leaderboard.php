<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\StravaActivityModel;

class Leaderboard extends BaseController
{
    public function index()
    {
        $activityModel = new StravaActivityModel();

        // Fetch leaderboard data within the date range (April 1st - August 1st)
        $startDate = '2024-04-01';
        $endDate = '2024-08-01';
        
        // Initialize the leaderboard data array
        $leaderboardData = [];
        
        // Retrieve user information, including Strava athlete IDs
        $userModel = new UserModel();
        $users = $userModel->findAll();
        
        foreach($users as $user) {
            // Calculate total distance for each user
            $totalDistance = $activityModel->select('SUM(distance) AS total_distance')
                ->where('strava_athlete_id', $user['strava_athlete_id'])
                ->where('start_date >=', $startDate)
                ->where('start_date <', $endDate)
                ->first()['total_distance'];
        
            // Calculate total number of activities for each user
            $totalActivities = $activityModel->where('strava_athlete_id', $user['strava_athlete_id'])
                ->where('start_date >=', $startDate)
                ->where('start_date <', $endDate)
                ->countAllResults();
        
            // Add the user to the leaderboard data
            $leaderboardData[] = [
                'strava_athlete_id' => $user['strava_athlete_id'],
                'name' => $user['name'],
                'profile_medium' => $user['profile_medium'],
                'total_distance' => $totalDistance,
                'total_activities' => $totalActivities
            ];
        }

        // Sort leaderboard by total distance (descending)
        usort($leaderboardData, function($a, $b) {
            return $b['total_distance'] - $a['total_distance'];
        });

        $data = [
            'leaderboardData' => $leaderboardData,
        ];

        return view('leaderboard_view', $data);
    }
}
