<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\StravaActivityModel;
use App\Config\AppConstants;

class Leaderboard extends BaseController
{
    public function index()
    {
        $activityModel = new StravaActivityModel();
        $userModel = new UserModel();
        $users = $userModel->findAll();

        // Fetch leaderboard data within the date range (April 1st - August 1st)
        $startDate = AppConstants::CHALLENGE_START_DATE;
        $endDate = AppConstants::CHALLENGE_END_DATE;

        // Get the activity type filter from the query string
        $leaderboardData = [];

        foreach ($users as $user) {
            $activities = $activityModel
                ->where('strava_athlete_id', $user['strava_athlete_id'])
                ->where('start_date >=', $startDate)
                ->where('start_date <', $endDate);
                $activities->whereIn('type', ['Walk', 'Run']);
         
            $activities = $activities->findAll();

            $totalDistance = 0;
            $points = 0;
            $has5kmActivity = false;
            $qualifyingActivities = [];

            // Group activities by date
            $activitiesByDate = [];
            foreach ($activities as $activity) {
                $date = date('Y-m-d', strtotime($activity['start_date_local']));
                $activitiesByDate[$date][] = $activity;
            }

            // Find the longest activity for each day and award points
            foreach ($activitiesByDate as $dateActivities) {
                usort($dateActivities, function($a, $b) {
                    return $b['distance'] - $a['distance']; // Sort by distance descending
                });

                $longestActivity = $dateActivities[0];
                $totalDistance += $longestActivity['distance'] / 1000; // Calculate in kilometers

                if ($longestActivity['distance'] >= 2000) {
                    $points++;
                }
                if ($longestActivity['distance'] >= 5000 && !$has5kmActivity) {
                    $points += 10;
                    $has5kmActivity = true;
                }

                $qualifyingActivities[] = $longestActivity;
            }

            if ($totalDistance >= 50) {
                $points += 20;
            }

            // Add user to leaderboard only if they have qualifying activities
            if (!empty($qualifyingActivities)) {
                $leaderboardData[] = [
                    'strava_athlete_id' => $user['strava_athlete_id'],
                    'name' => $user['name'],
                    'profile_medium' => $user['profile_medium'],
                    'total_distance' => $totalDistance,
                    'total_activities' => count($activities),
                    'points' => $points,
                    'qualifying_activities' => $qualifyingActivities, 
                ];
            }
        }

        // Sort leaderboard by points (descending)
        usort($leaderboardData, function ($a, $b) {
            return $b['points'] - $a['points'];
        });

        $data = [
            'leaderboardData' => $leaderboardData,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        return view('leaderboard_view', $data);
    }
}
