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
        $users = $userModel->select(['name', 'strava_athlete_id','profile_medium'])->findAll();
        $activityTypes = ['Walk', 'Run'];

        $leaderboardData = [];
        foreach ($users as $user) {
            $activities = $activityModel->getActivitiesForLeaderboard($user['strava_athlete_id'], $startDate, $endDate, $activityTypes);
            $pointsCalculation = $this->calculatePoints($activities);
            $points = $pointsCalculation['points'];
            $pointsBreakdown = $pointsCalculation['breakdown'];
            $totalDistance = $this->calculateTotalDistance($activities); // Calculate total distance
            
            if ($points > 0) {
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

            if ($distance >= 5) {
                $daysWith5km++;
            }
            if ($distance >= 12 && $daysWith12km < 2) {
                $daysWith12km++;
            }
        }

        // Award points only if minimum criteria are met
        if ($daysWith5km >= 12) {
            $points += 10 * min($daysWith5km, 20); // Max 10 points per day for up to 20 days
            $pointsBreakdown['5km+'] = 10 * min($daysWith5km, 20);
            if ($daysWith12km > 0) {
                $points += 25 * $daysWith12km; // 25 points per day for up to 2 days
                $pointsBreakdown['12km+'] = 25 * $daysWith12km;
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
    
     private function calculateTotalDistance($activities){
        return array_reduce($activities, function($sum, $activity) {
            return $sum + $activity['distance'] / 1000;
        }, 0);
    }
}
