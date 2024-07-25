<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\StravaActivityModel;

class Leaderboard extends BaseController
{
    public function index()
    {
        $activityModel = new StravaActivityModel();
        $userModel = new UserModel();
        $users = $userModel->findAll();

        // Fetch leaderboard data within the date range (April 1st - August 1st)
        $startDate = '2024-07-11';
        $endDate = '2024-07-30';

        // Get the activity type filter from the query string
        $leaderboardData = [];

        foreach ($users as $user) {
            // Fetch and filter activities
            $activities = $activityModel
                ->where('strava_athlete_id', $user['strava_athlete_id'])
                ->where('start_date >=', $startDate)
                ->where('start_date <', $endDate);

            $activities->whereIn('type', ['Walk', 'Run']);

            $activities = $activities->findAll();

            // Calculate total distance and points
            $totalDistance = 0;
            $points = 0;
            $has5kmActivity = false;
            $qualifyingActivities = []; // Array to store qualifying activities

            foreach ($activities as $activity) {
                $distanceKm = $activity['distance'] / 1000;
                $totalDistance += $distanceKm;

                // Points calculation
                if ($distanceKm >= 2) {
                    $points++;
                }
                if ($distanceKm >= 5 && !$has5kmActivity) {
                    $points += 10;
                    $has5kmActivity = true;
                }

                // Store qualifying activities
                if ($distanceKm >= 2 || ($distanceKm >= 5 && !$has5kmActivity)) {
                    $activity['distance'] = $distanceKm; // Store distance in km
                    $qualifyingActivities[] = $activity;
                }
            }

            if ($totalDistance >= 50) {
                $points += 20;
            }

            // Add user to leaderboard only if they have activities of the selected type
            if (count($activities) > 0) {
                $leaderboardData[] = [
                    'strava_athlete_id' => $user['strava_athlete_id'],
                    'name' => $user['name'],
                    'profile_medium' => $user['profile_medium'],
                    'total_distance' => $totalDistance,
                    'total_activities' => count($activities),
                    'points' => $points,
                    'qualifying_activities' => $qualifyingActivities
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
