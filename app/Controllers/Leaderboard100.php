<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\StravaActivityModel;
use App\Config\AppConstants;

class Leaderboard100 extends BaseController
{
    public function index()
    {
        // Validate date range is exactly 20 days
        $startDate = AppConstants::CHALLENGE_START_DATE;
        $endDate = AppConstants::CHALLENGE_END_DATE;

        $dateDiff = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24);
        if ($dateDiff != 19) { // 19 because we count the start date as well
            throw new \Exception('Challenge must be exactly 20 days long.');
        }

        $activityModel = new StravaActivityModel();
        $userModel = new UserModel();
        $users = $userModel->findAll();
        $activityTypes = ['Walk', 'Run'];

        $leaderboardData = [];
        foreach ($users as $user) {
            $activities = $activityModel->getActivitiesForLeaderboard($user['strava_athlete_id'], $startDate, $endDate, $activityTypes);

            // Point Calculation Logic
            $points = 0;
            $qualifyingActivities = [];
            $totalDistance = 0;
            $daysWith5km = 0;
            $daysWith12km = 0;

            foreach ($activities as $activity) {
                $totalDistance += $activity['distance'] / 1000; // Convert to km

                if ($activity['distance'] >= 5000) {
                    $points += 10;
                    $daysWith5km++;
                    $qualifyingActivities[] = $activity;
                }

                if ($activity['distance'] >= 12000 && $daysWith12km < 2) {
                    $points += 25;
                    $daysWith12km++;
                    $qualifyingActivities[] = $activity; 
                }
            }

            if ($totalDistance >= 60) {
                $points += 50;
            }

            // Points capped at maximum allowed per day
            $points = min($points, 10 * count($activities)); 

            // Only include users with at least 12 qualifying days
            if ($daysWith5km >= 12) {
                $leaderboardData[] = [
                    // ... rest of user data (same as before) ...
                ];
            }
        }

        usort($leaderboardData, function($a, $b) {
            return $b['points'] - $a['points'];
        });

        return view('leaderboard100_view', [
            'leaderboardData' => $leaderboardData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'filterLabel' => 'All Activities' // You can adjust this for different filter types
        ]);
    }
}
