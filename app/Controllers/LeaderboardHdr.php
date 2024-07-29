<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\StravaActivityModel;
use App\Config\AppConstants;
use CodeIgniter\Database\Exceptions\DatabaseException;

class LeaderboardHdr extends BaseController
{
    protected $session;
    protected $selectedStyle;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Initialize the session
        $this->session = \Config\Services::session();

        // Set session variable for style if not set already
        if (!$this->session->has('selectedStyle')) {
            $styles = ['indian', 'us', 'south-african', 'colombian', 'uk-hk'];
            $preferredStyle = $styles[array_rand($styles)];
            $this->session->set('selectedStyle', 'style-' . $preferredStyle . '.css');
        }
        $this->selectedStyle = $this->session->get('selectedStyle');
    }
    public function index()
    {
        $data['selectedStyle'] = $this->session->get('selectedStyle');

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
        usort($leaderboardData, fn ($a, $b) => $b['points'] - $a['points']);

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
        $daysWithMinDistance = []; // Track qualifying days
        $bonusDayAchieved = false;
        $totalDistance = 0;
        $consideredActivities = []; // Array to store considered activities

        foreach ($activities as $activity) {
            $distance = $activity['distance'] / 1000;
            $date = date('Y-m-d', strtotime($activity['start_date_local']));
            if ($distance >= $challengeConfig['minDistance']['km'] && !in_array($date, $daysWithMinDistance)) {
            $totalDistance += $distance;

            }
            // Check for minimum daily distance (but only if it hasn't been met for the day yet)
            if ($distance >= $challengeConfig['minDistance']['km'] && !in_array($date, $daysWithMinDistance)) {
                $daysWithMinDistance[] = $date;
                $pointsBreakdown["Min. Distance Day ($date)"] = $challengeConfig['minDistance']['pointsPerDay'];
            }

            // Check for bonus day
            if (!$bonusDayAchieved && $distance >= $challengeConfig['bonusDay']['km']) {
                $pointsBreakdown["Bonus Day ($date)"] = $challengeConfig['bonusDay']['points'];
                $bonusDayAchieved = true;
            }

            // Check for active day
            if ($distance >= $challengeConfig['activeDay']['km']) {
                $pointsBreakdown["Active Day ($date)"] = $challengeConfig['activeDay']['pointsPerDay'];
            }

            // Add activity to considered activities if it contributes to any category
            if ($distance >= $challengeConfig['minDistance']['km'] || $distance >= $challengeConfig['bonusDay']['km'] || $distance >= $challengeConfig['activeDay']['km']) {
                $consideredActivities[] = [
                    'activity_id' => $activity['activity_id'],
                    'activity_name' => $activity['name'] // Assuming your data has a 'name' field
                ];
            }
        }

        // Award Points Based on Criteria (Independent)
        if (count($daysWithMinDistance) >= $challengeConfig['minDistance']['minDays']) {
            // Cap the number of days to the maximum allowed
            $daysCounted = ($challengeConfig['minDistance']['maxDays'] > 0) ?
                min(count($daysWithMinDistance), $challengeConfig['minDistance']['maxDays']) :
                count($daysWithMinDistance);
            $points += $challengeConfig['minDistance']['pointsPerDay'] * $daysCounted;
        }

        // Check for active days (count days that reached bonus distance)
        $activeDays = count(array_filter($activities, function ($activity) use ($challengeConfig) {
            return $activity['distance'] >= $challengeConfig['activeDay']['km'] * 1000; // Filter by distance
        }));

        if ($activeDays >= $challengeConfig['activeDay']['minDays']) {
            // Cap the number of active days to the maximum allowed
            $activeDaysCounted = min($activeDays, $challengeConfig['activeDay']['maxDays']);
            $points += $challengeConfig['activeDay']['pointsPerDay'] * $activeDaysCounted;
        }

        // Award points for overall distance (only if minimum days are met)
        if (
            $daysWithMinDistance >= $challengeConfig['minDistance']['minDays'] &&
            $totalDistance >= $challengeConfig['overallMinDistance']['km']
        ) {
            $points += $challengeConfig['overallMinDistance']['points'];
        }

        // Adding bonus points if applicable (only once during the challenge)
        if ($bonusDayAchieved) {
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
