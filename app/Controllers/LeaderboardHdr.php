<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\StravaActivityModel;
use App\Config\AppConstants;
use CodeIgniter\Database\Exceptions\DatabaseException;

class LeaderboardHdr extends BaseController
{
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
            $users = $userModel->select(['name', 'strava_athlete_id', 'profile_medium'])->findAll();
        } catch (DatabaseException $e) {
            // Handle database error (e.g., log, show error message)
            return redirect()->back()->with('error', 'Error Fetching Users: ' . $e->getMessage());
        }

        $activityTypes = ['Walk', 'Run'];

        // 6. Process Leaderboard Data
        $leaderboardData = [];
        foreach ($users as $user) {
            $activities = $activityModel->getActivitiesForLeaderboard(
                $user['strava_athlete_id'],
                $startDate,
                $endDate,
                $activityTypes, 
                $challengeConfig['minDistance']['km']
            );
            
            $pointsCalculation = $this->calculatePoints($activities, $challengeConfig, $startDate, $endDate, $dateDiff);

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

    /**
     * Calculates points and their breakdown for a user's activities based on challenge rules.
     *
     * @param array $activities The user's activities within the challenge period.
     * @param array $challengeConfig The configuration settings for the challenge.
     * @param string $startDate The start date of the challenge.
     * @param string $endDate The end date of the challenge.
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
        $consideredActivities = [];

        // Group activities by date
        $activitiesByDate = [];
        foreach ($activities as $activity) {
            $date = date('Y-m-d', strtotime($activity['start_date_local']));
            $activitiesByDate[$date][] = $activity;
        }

        // Find the longest activity for each day and award points
        foreach ($activitiesByDate as $date => $dailyActivities) {
            usort($dailyActivities, fn($a, $b) => $b['distance'] - $a['distance']); // Sort by distance descending
            $longestActivity = $dailyActivities[0];
            $distance = $longestActivity['distance'] / 1000; // Convert to km
            $totalDistance += $distance;

            if ($distance >= $challengeConfig['minDistance']['km']) {
                $daysWithMinDistance++;
                $pointsBreakdown["Min. Distance Day ($date)"] = $challengeConfig['minDistance']['pointsPerDay'];
                $consideredActivities[] = $activity['activity_id'];
            }

            // Bonus points awarded only once for the longest activity meeting the distance requirement
            if (!$bonusDayAchieved && $distance >= $challengeConfig['bonusDay']['km']) {
                $pointsBreakdown["Bonus Day ($date)"] = $challengeConfig['bonusDay']['points'];
                $bonusDayAchieved = true;
                $consideredActivities[] = $activity['activity_id'];
            }
        }

        // Award Points Based on Criteria (Independent)
        if ($daysWithMinDistance >= $challengeConfig['minDistance']['minDays']) {
            // Cap the number of days to the maximum allowed
            $daysCounted = ($challengeConfig['minDistance']['maxDays'] > 0) ? 
                min($daysWithMinDistance, $challengeConfig['minDistance']['maxDays']) : 
                $daysWithMinDistance;
            $points += $challengeConfig['minDistance']['pointsPerDay'] * $daysCounted;
        }

        // Check for active days (count days that reached bonus distance)
        $activeDays = count(array_filter($activitiesByDate, function ($dailyActivities) use ($challengeConfig) {
            return $dailyActivities[0]['distance'] >= $challengeConfig['activeDay']['km'] * 1000; // Filter by distance
        }));

        if ($activeDays >= $challengeConfig['activeDay']['minDays']) {
            // Cap the number of active days to the maximum allowed
            $activeDaysCounted = min($activeDays, $challengeConfig['activeDay']['maxDays']);
            $points += $challengeConfig['activeDay']['pointsPerDay'] * $activeDaysCounted;
            $pointsBreakdown['Active Days'] = $challengeConfig['activeDay']['pointsPerDay'] * $activeDaysCounted;
        }

        // Award points for overall distance (only if minimum days are met)
        if ($daysWithMinDistance >= $challengeConfig['minDistance']['minDays'] &&
            $totalDistance >= $challengeConfig['overallMinDistance']['km']) {
            $points += $challengeConfig['overallMinDistance']['points'];
            $pointsBreakdown['Overall Distance'] = $challengeConfig['overallMinDistance']['points'];
        }
        
        // Adding bonus points if applicable (only once during the challenge)
        if($bonusDayAchieved){
            $points += $challengeConfig['bonusDay']['points'];
        }

        return ['points' => $points, 'breakdown' => $pointsBreakdown, 'considered_activities' => $consideredActivities];
    }

    /**
     * Calculates the total distance (in km) from an array of activities.
     *
     * @param array $activities The user's activities within the challenge period.
     * @return float The total distance in km.
     */
    private function calculateTotalDistance($activities)
    {
        return array_reduce($activities, function ($carry, $activity) {
            return $carry + ($activity['distance'] / 1000); // Convert meters to kilometers
        }, 0);
    }
}
