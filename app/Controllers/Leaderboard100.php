<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\StravaActivityModel;
use App\Config\AppConstants;
use CodeIgniter\Database\Exceptions\DatabaseException;

class Leaderboard100 extends BaseController
{
    public function index()
    {
        // 1. Challenge Configuration
        $challengeConfig = [
            'minDailyDistance' => 5,            // Minimum daily distance in kilometers
            'minQualifyingDays' => 12,          // Minimum qualifying days for overall points
            'bonusDistance' => 12,             // Distance in kilometers for bonus points
            'maxBonusDays' => 2,               // Maximum days to earn bonus points
            'pointsPerDay' => 10,
            'bonusPoints' => 25,
            'overallPoints' => 100
        ];

        // 2. Get Challenge Start and End Dates
        $startDate = AppConstants::CHALLENGE_START_DATE;
        $endDate = AppConstants::CHALLENGE_END_DATE;

        // 3. Calculate Challenge Duration
        $dateDiff = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24) + 1; // Total challenge days

        // 4. Input Validation (Ensure challenge is at least one day long)
        if ($dateDiff < 1) {
            throw new \Exception('Challenge duration must be at least 1 day.');
        }
        
        // 5. Get Users from Database
        $activityModel = new StravaActivityModel();
        $userModel = new UserModel();

        try {
            $users = $userModel->select(['name', 'strava_athlete_id','profile_medium'])->findAll();
        } catch (DatabaseException $e) {
            return redirect()->back()->with('error', 'Error Fetching Users.'.$e->getMessage());
        }

        // 6. Prepare Leaderboard Data for each user
        $leaderboardData = [];
        foreach ($users as $user) {
            $activities = $activityModel->getActivitiesForLeaderboard(
                $user['strava_athlete_id'], 
                $startDate, 
                $endDate, 
                ['Walk', 'Run'] // You can adjust this for different filter types
            );
            
            $totalDistance = $this->calculateTotalDistance($activities);
            $pointsCalculation = $this->calculatePoints($activities, $challengeConfig, $dateDiff, $totalDistance);

            // Ensure points_breakdown exists
            if (!isset($pointsCalculation['breakdown'])) {
                $pointsCalculation['breakdown'] = [];
            }

            // Combine user info, points, and activity details
            $leaderboardData[] = array_merge(
                $user, 
                $pointsCalculation, 
                [
                    'total_distance' => $totalDistance,
                    'total_activities' => count($activities),
                    'qualifying_activities' => $this->getQualifyingActivities($activities, $dateDiff)
                ]
            );
        }

        // 7. Sort Leaderboard by Points (Descending)
        usort($leaderboardData, fn($a, $b) => $b['points'] - $a['points']);

        // 8. Render the Leaderboard View
        return view('leaderboard100_view', [
            'leaderboardData' => $leaderboardData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'challengeConfig' => $challengeConfig,
        ]);
    }

    // Private Helper Methods

    /**
     * Calculates points based on challenge criteria.
     */
    private function calculatePoints($activities, $challengeConfig, $dateDiff, $totalDistance)
    {
        $points = 0;
        $pointsBreakdown = [];
        $daysWithMinDistance = 0;
        $bonusDays = 0;
        $minQualifyingDays = $challengeConfig['minQualifyingDays'];

        foreach ($activities as $activity) {
            $distance = $activity['distance'] / 1000; // Convert meters to kilometers

            if ($distance >= $challengeConfig['minDailyDistance']) {
                $daysWithMinDistance++;
            }
            if ($distance >= $challengeConfig['bonusDistance'] && $bonusDays < $challengeConfig['maxBonusDays']) {
                $bonusDays++;
            }
        }

        // Award Points Based on Criteria (Independent)
        if ($daysWithMinDistance >= $challengeConfig['minQualifyingDays']) {
            $points += $challengeConfig['pointsPerDay'] * $minQualifyingDays;
            $pointsBreakdown[$challengeConfig['minDailyDistance'].'km+'] = $challengeConfig['pointsPerDay'] * $minQualifyingDays;
        }

        if ($bonusDays > 0) {
            $points += $challengeConfig['bonusPoints'] * $bonusDays;
            $pointsBreakdown[$challengeConfig['bonusDistance'].'km+'] = $challengeConfig['bonusPoints'] * $bonusDays;
        }

        $overallMinDistance = ($challengeConfig['minDailyDistance'] * $dateDiff) + $challengeConfig['bonusDistance'];
        if ($totalDistance >= $overallMinDistance) {
            $points += $challengeConfig['overallPoints'];
            $pointsBreakdown['Overall'] = $challengeConfig['overallPoints'];
        }
        
        return ['points' => $points, 'breakdown' => $pointsBreakdown];
    }   

    private function getQualifyingActivities($activities, $dateDiff)
    {
        $qualifyingActivities = [];
        $daysWithMinDistance = 0;
        $bonusDays = 0;

        // Group activities by date
        $activitiesByDate = [];
        foreach ($activities as $activity) {
            $date = date('Y-m-d', strtotime($activity['start_date_local']));
            $activitiesByDate[$date][] = $activity;
        }

        // Find longest activity for each day
        foreach ($activitiesByDate as $date => $dailyActivities) {
            usort($dailyActivities, function ($a, $b) {
                return $b['distance'] - $a['distance'];
            });
            $longestActivity = $dailyActivities[0];

            if ($longestActivity['distance'] >= 5000 && $daysWithMinDistance < $dateDiff) {
                $qualifyingActivities[] = $longestActivity;
                $daysWithMinDistance++;
            }

            if ($longestActivity['distance'] >= 12000 && $bonusDays < 2) {
                $qualifyingActivities[] = $longestActivity;
                $bonusDays++;
            }
        }

        return $qualifyingActivities;
    }
    
    private function calculateTotalDistance($activities){
        return array_reduce($activities, function ($sum, $activity) {
            return $sum + $activity['distance'] / 1000;
        }, 0);
    }
}

