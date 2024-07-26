<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\StravaActivityModel;
use App\Config\AppConstants;
use CodeIgniter\Database\Exceptions\DatabaseException;

class LeaderboardHdr extends BaseController
{
    /**
     * Main action to display the leaderboard.
     * Fetches user and activity data, calculates points, and renders the leaderboard view.
     */
    public function index()
    {
        // 1. Challenge Configuration (Easily Customizable)
        $challengeConfig = [
            'minDistance' => [      // Minimum daily distance criteria
                'km' => 2,            // Minimum distance in km
                'pointsPerDay' => 1, // Points per day for reaching this distance
                'minDays' => 1,      // Minimum days to qualify for overall points
                'maxDays' => 20,       // 0 means no maximum limit on days
            ],
            'activeDay' => [       // Active day criteria
                'km' => 0,            // Distance in km to qualify for active day
                'pointsPerDay' => 0, // Points per active day
                'minDays' => 0,       // Minimum active days
                'maxDays' => 0,      // Maximum active days
            ],
            'bonusDay' => [        // Bonus day criteria
                'km' => 5,           // Distance in km to qualify for bonus day
                'points' => 10,     // Points awarded for a bonus day
            ],
            'overallMinDistance' => [  // Overall minimum distance criteria
                'km' => 50,           // Total distance in km
                'points' => 10,     // Points awarded for overall distance
            ],
        ];

        // 2. Get Challenge Start and End Dates
        $startDate = AppConstants::CHALLENGE_START_DATE;
        $endDate = AppConstants::CHALLENGE_END_DATE;
        
        // 3. Calculate Challenge Duration (in days)
        $dateDiff = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24) + 1; 

        // 4. Input Validation (Ensure challenge is at least one day long)
        if ($dateDiff < 1) {
            throw new \Exception('Challenge duration must be at least 1 day.');
        }
        
        // 5. Get Users and Activities from the Database
        $activityModel = new StravaActivityModel();
        $userModel = new UserModel();

        try {
            $users = $userModel->select(['name', 'strava_athlete_id','profile_medium'])->findAll();
        } catch (DatabaseException $e) {
            // Handle database error (e.g., log, show error message)
            return redirect()->back()->with('error', 'Error Fetching Users.' . $e->getMessage());
        }

        $activityTypes = ['Walk', 'Run'];

        // 6. Process Leaderboard Data
        $leaderboardData = [];
        foreach ($users as $user) {
            $activities = $activityModel->getActivitiesForLeaderboard(
                $user['strava_athlete_id'], 
                $startDate, 
                $endDate, 
                $activityTypes
            );

            $pointsCalculation = $this->calculatePoints($activities, $challengeConfig, $dateDiff);

            $leaderboardData[] = array_merge($user, $pointsCalculation, [
                'total_distance' => $this->calculateTotalDistance($activities),
                'total_activities' => count($activities)
            ]);
        }

        // 7. Sort Leaderboard by Points (Descending)
        usort($leaderboardData, fn($a, $b) => $b['points'] - $a['points']);

        // 8. Render the Leaderboard View
        return view('leaderboard_hdr_view', [
            'leaderboardData' => $leaderboardData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'challengeConfig' => $challengeConfig,
        ]);
    }


    // Private Methods for Calculations
    
    /**
     * Calculates points and their breakdown for a user's activities based on challenge rules.
     *
     * @param array $activities The user's activities within the challenge period.
     * @param array $challengeConfig The configuration settings for the challenge.
     * @param int $dateDiff The total number of days in the challenge.
     * @return array An array containing the total points and a breakdown of the points.
     */
    private function calculatePoints($activities, $challengeConfig, $dateDiff)
    {
        $points = 0;
        $pointsBreakdown = [];
        $daysWithMinDistance = 0;
        $bonusDayAchieved = false;
        $totalDistance = 0;

        foreach ($activities as $activity) {
            $distance = $activity['distance'] / 1000;
            $totalDistance += $distance;

            if ($distance >= $challengeConfig['minDistance']['km']) {
                $daysWithMinDistance++;
            }

            if (!$bonusDayAchieved && $distance >= $challengeConfig['bonusDay']['km']) {
                $points += $challengeConfig['bonusDay']['points'];
                $pointsBreakdown['Bonus Day'] = $challengeConfig['bonusDay']['points'];
                $bonusDayAchieved = true;
            }
        }

        // Award Points Based on Criteria (Independent)
        if ($daysWithMinDistance >= $challengeConfig['minDistance']['minDays']) {
            // Cap the number of days to the maximum allowed
            $daysCounted = ($challengeConfig['minDistance']['maxDays'] > 0) ? 
                min($daysWithMinDistance, $challengeConfig['minDistance']['maxDays']) : 
                $daysWithMinDistance;
            $points += $challengeConfig['minDistance']['pointsPerDay'] * $daysCounted;
            $pointsBreakdown['Min. Distance Days'] = $challengeConfig['minDistance']['pointsPerDay'] * $daysCounted;
        }

        // Calculate points for overall minimum distance (only if minimum days are met)
        if ($daysWithMinDistance >= $challengeConfig['minDistance']['minDays'] &&
            $totalDistance >= $challengeConfig['overallMinDistance']['km']) {
            $points += $challengeConfig['overallMinDistance']['points'];
            $pointsBreakdown['Overall Distance'] = $challengeConfig['overallMinDistance']['points'];
        }
        return ['points' => $points, 'breakdown' => $pointsBreakdown];
    }    


    /**
     * Calculates the total distance (in km) from an array of activities.
     *
     * @param array $activities An array of activities, each with a 'distance' field (in meters).
     * @return float The total distance in kilometers.
     */
    private function calculateTotalDistance($activities)
    {
        return array_reduce($activities, function ($carry, $activity) {
            return $carry + ($activity['distance'] / 1000); // Convert meters to kilometers
        }, 0);
    }

    private function getQualifyingActivities($activities, $challengeConfig, $dateDiff)
    {
        $qualifyingActivities = [];
        $daysWithMinDistance = 0;
        $bonusDayAchieved = false;

        // Group activities by date
        $activitiesByDate = [];
        foreach ($activities as $activity) {
            $date = date('Y-m-d', strtotime($activity['start_date_local']));
            $activitiesByDate[$date][] = $activity;
        }

        // Find longest activity for each day and check for qualifying activities
        foreach ($activitiesByDate as $date => $dailyActivities) {
            usort($dailyActivities, function ($a, $b) {
                return $b['distance'] - $a['distance'];
            });
            $longestActivity = $dailyActivities[0];

            if ($longestActivity['distance'] >= $challengeConfig['minDistance']['km'] * 1000 && $daysWithMinDistance < $challengeConfig['minDistance']['minDays']) {
                $qualifyingActivities[] = $longestActivity;
                $daysWithMinDistance++;
            }

            if (!$bonusDayAchieved && $longestActivity['distance'] >= $challengeConfig['bonusDay']['km'] * 1000) {
                $qualifyingActivities[] = $longestActivity;
                $bonusDayAchieved = true;
            }
        }

        return $qualifyingActivities;
    }
}
