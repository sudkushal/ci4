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
        // Challenge Configuration 
        $challengeConfig = [
            'minDailyDistance' => 5,            // Minimum daily distance in km
            'minQualifyingDays' => 12,          // Minimum qualifying days for the overall challenge
            'bonusDistance' => 12,            // Distance in km for bonus points
            'maxBonusDays' => 2,               // Maximum days to earn bonus points
            'pointsPerDay' => 10,
            'bonusPoints' => 25,
            'overallPoints' => 100
        ];

        $startDate = AppConstants::CHALLENGE_START_DATE;
        $endDate = AppConstants::CHALLENGE_END_DATE;
        
        // Calculate challenge duration and dynamic values
        $dateDiff = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24) + 1;
        $minQualifyingDays = min($challengeConfig['minQualifyingDays'], $dateDiff); 
        $overallMinDistance = ($challengeConfig['minDailyDistance'] * $minQualifyingDays) + $challengeConfig['bonusDistance'];
        
        // Model Instantiation
        $activityModel = new StravaActivityModel();
        $userModel = new UserModel();
        $users = $userModel->select(['name', 'strava_athlete_id','profile_medium'])->findAll();
        $activityTypes = ['Walk', 'Run'];
        
        // Prepare Leaderboard Data
        $leaderboardData = [];
        foreach ($users as $user) {
            $activities = $activityModel->getActivitiesForLeaderboard($user['strava_athlete_id'], $startDate, $endDate, $activityTypes);
            $pointsCalculation = $this->calculatePoints($activities, $challengeConfig, $minQualifyingDays, $overallMinDistance);
            if ($pointsCalculation['points'] > 0) {
                $leaderboardData[] = array_merge($user, $pointsCalculation, [
                    'total_distance' => $this->calculateTotalDistance($activities),
                    'total_activities' => count($activities),
                    'qualifying_activities' => $this->getQualifyingActivities($activities, $dateDiff)
                ]);
            }
        }

        // Sort and Render
        usort($leaderboardData, fn($a, $b) => $b['points'] - $a['points']);
        return view('leaderboard100_view', [
            'leaderboardData' => $leaderboardData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'filterLabel' => 'All Activities',
            'challengeConfig' => $challengeConfig,
            'overallMinDistance' => $overallMinDistance
        ]);
    }

    // Private Methods
    private function calculatePoints($activities, $challengeConfig, $minQualifyingDays, $overallMinDistance)
    {
        $points = 0;
        $pointsBreakdown = [];
        $daysWithMinDistance = 0;
        $bonusDays = 0;
        $totalDistance = 0;
        
        foreach ($activities as $activity) {
            $distance = $activity['distance'] / 1000; 
            
            if ($distance >= $challengeConfig['minDailyDistance']) {
                $daysWithMinDistance++;
            }
            if ($distance >= $challengeConfig['bonusDistance'] && $bonusDays < $challengeConfig['maxBonusDays']) {
                $bonusDays++;
            }
            $totalDistance += $distance;
        }

        if ($daysWithMinDistance >= $minQualifyingDays && $totalDistance >= $overallMinDistance) {
            $points += $challengeConfig['overallPoints'];
            $pointsBreakdown['Overall'] = $challengeConfig['overallPoints'];
            $points += $challengeConfig['pointsPerDay'] * $daysWithMinDistance; 
            $pointsBreakdown[$challengeConfig['minDailyDistance'].'km+'] = $challengeConfig['pointsPerDay'] * $daysWithMinDistance;
            $points += $challengeConfig['bonusPoints'] * $bonusDays;
            $pointsBreakdown[$challengeConfig['bonusDistance'].'km+'] = $challengeConfig['bonusPoints'] * $bonusDays;
        }

        return ['points' => $points, 'breakdown' => $pointsBreakdown];
    }

    private function getQualifyingActivities($activities, $dateDiff)
    {
        $qualifyingActivities = [];
        $daysWithMinDistance = 0;
        $bonusDays = 0;

        foreach ($activities as $activity) {
            if ($activity['distance'] >= 5000 && $daysWithMinDistance < $dateDiff) {
                $qualifyingActivities[] = $activity;
                $daysWithMinDistance++;
            }

            if ($activity['distance'] >= 12000 && $bonusDays < 2) {
                $qualifyingActivities[] = $activity;
                $bonusDays++;
            }
        }
        return $qualifyingActivities;
    }

    private function calculateTotalDistance($activities)
    {
        return array_reduce($activities, function ($sum, $activity) {
            return $sum + $activity['distance'] / 1000;
        }, 0);
    }
}
