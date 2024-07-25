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
        
        foreach ($users as $user) {
            $activities = $activityModel
                ->where('strava_athlete_id', $user['strava_athlete_id'])
                ->where('type', 'Walk')
                ->where('start_date >=', $startDate)
                ->where('start_date <', $endDate)
                ->findAll();

            $totalDistance = 0;
            $points = 0;
            $has5kmWalk = false; 

            foreach ($activities as $activity) {
                $totalDistance += $activity['distance'] / 1000; // Calculate in kilometers

                if ($activity['distance'] >= 2000) { // Award points for 2+ km walks
                    $points++;
                }
                if ($activity['distance'] >= 5000 && !$has5kmWalk) { // Award 10 points for 1st 5+ km walk
                    $points += 10;
                    $has5kmWalk = true;
                }
            }

            if ($totalDistance >= 50) {  // Award 20 points for 50+ km total
                $points += 20;
            }

            $leaderboardData[] = [
                'strava_athlete_id' => $user['strava_athlete_id'],
                'name' => $user['name'],
                'profile_medium' => $user['profile_medium'], // Add profile picture (if available)
                'total_distance' => $totalDistance, 
                'total_activities' => count($activities),
                'points' => $points
            ];
        }

        // Sort leaderboard by total distance (descending)
        usort($leaderboardData, function($a, $b) {
            return $b['points'] - $a['points'];
        });

        $data = [
            'leaderboardData' => $leaderboardData,
        ];

        return view('leaderboard_view', $data);
    }
}
