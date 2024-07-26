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
        if ($dateDiff != 19) { 
            throw new \Exception('Challenge must be exactly 20 days long.');
        }

        $activityModel = new StravaActivityModel();
        $userModel = new UserModel();
        $users = $userModel->select(['name', 'strava_athlete_id','profile_medium'])->findAll(); // Explicitly select 'name'
        $activityTypes = ['Walk', 'Run'];

        $leaderboardData = [];
        foreach ($users as $user) {
            $activities = $activityModel->getActivitiesForLeaderboard($user['strava_athlete_id'], $startDate, $endDate, $activityTypes);

            // Point Calculation Logic
            $pointsCalculation = $this->calculatePoints($activities);
            $points = $pointsCalculation['points'];
            $pointsBreakdown = $pointsCalculation['breakdown'];

            // Only include users with at least 12 qualifying days
            $totalDistance = array_reduce($activities, function($sum, $activity) {
                return $sum + $activity['distance'] / 1000;
            }, 0);

            if($totalDistance >= 60){
                $leaderboardData[] = [
                    'strava_athlete_id' => $user['strava_athlete_id'],
                    'name' => $user['name'],
                    'profile_medium' => $user['profile_medium'],
                    'total_distance' => $totalDistance,
                    'total_activities' => count($activities),
                    'points' => $points,
                    'points_breakdown' => $pointsBreakdown,
                    'qualifying_activities' => $this->getQualifyingActivities($activities),
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
    
    private function calculatePoints($activities)
    {
        $points = 0;
        $pointsBreakdown = [];
        $daysWith5km = 0;
        $daysWith12km = 0;

        foreach ($activities as $activity) {
            $distance = $activity['distance'] / 1000; // Convert meters to kilometers

            if ($distance >= 5 && $daysWith5km < 20) {
                $points += 10;
                $daysWith5km++;
                $pointsBreakdown[] = "10 points for 5+ km activity on " . date('Y-m-d', strtotime($activity['start_date_local']));
            }

            if ($distance >= 12 && $daysWith12km < 2) {
                $points += 25;
                $daysWith12km++;
                $pointsBreakdown[] = "25 points for 12+ km activity on " . date('Y-m-d', strtotime($activity['start_date_local']));
            }
        }

        return ['points' => $points, 'breakdown' => $pointsBreakdown];
    }

    private function getQualifyingActivities($activities)
    {
        $qualifyingActivities = [];
        $daysWith5km = 0;
        $daysWith12km = 0;

        foreach ($activities as $activity) {
            if ($activity['distance'] >= 5000 && $daysWith5km < 20) {
                $qualifyingActivities[] = $activity;
                $daysWith5km++;
            }

            if ($activity['distance'] >= 12000 && $daysWith12km < 2) {
                $qualifyingActivities[] = $activity;
                $daysWith12km++;
            }
        }

        return $qualifyingActivities;
    }
}
